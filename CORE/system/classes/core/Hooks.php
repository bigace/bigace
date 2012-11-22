<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst.
 *
 * BIGACE is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * BIGACE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @package bigace.classes
 * @subpackage core
 */

/**
 * The plugin API is located in this file, which allows for creating actions
 * and filters and hooking functions, and methods. The functions or methods will
 * then be run when the action or filter is called.
 *
 * The API callback examples reference functions, but can be methods of classes.
 * To hook methods, you'll need to pass an array one of two ways.
 *
 * Any of the syntaxes explained in the PHP documentation for the
 * {@link http://us2.php.net/manual/en/language.pseudo-types.php#language.types.callback 'callback'}
 * type are valid.
 *
 * Also see the {@link http://codex.wordpress.org/Plugin_API Plugin API} for more information
 * and examples on how to use a lot of these functions.
 *
 * A list of all existing hooks and filter can be found here:
 * {@link http://wiki.bigace.de/bigace:developer:hooks Hooks API}
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage core
 */
class Hooks
{
	// information about executed actions
    private static $actions = array();
    // all available actions and filter stay here
    private static $filter = array();
    // unused currently
    private static $mergedFilter = array();
    // the current executed filter
    private static $currentFilter = array();

    /**
     * add_filter() - Hooks a function or method to a specific filter action.
     *
     * Filters are the hooks that BIGACE launches to modify text of various types
     * before adding it to the database or sending it to the browser screen. Plugins
     * can specify that one or more of its PHP functions is executed to
     * modify specific types of text at these times, using the Filter API.
     *
     * To use the API, the following code should be used to bind a callback to the filter
     * <code>
     * function example_hook($example) { echo $example; }
     *
     * add_filter('example_filter', 'example_hook');
     * </code>
     *
     * Hooked functions can take extra arguments that are set when
     * the matching do_action() or apply_filters() call is run. The <tt>$accepted_args
     * allow for calling functions only when the number of args match. Hooked functions
     * can take extra arguments that are set when the matching <tt>do_action()</tt> or
     * <tt>apply_filters()</tt> call is run. For example, the action <tt>comment_id_not_found</tt>
     * will pass any functions that hook onto it the ID of the requested comment.
     *
     * <strong>Note:</strong> the function will return true no matter if the function was hooked
     * fails or not. There are no checks for whether the function exists beforehand and no checks
     * to whether the <tt>$function_to_add is even a string. It is up to you to take care and
     * this is done for optimization purposes, so everything is as quick as possible.
     *
     * @param string $tag The name of the filter to hook the <tt>$function_to_add</tt> to.
     * @param callback $function_to_add The name of the function to be called when the filter is applied.
     * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
     * @param int $accepted_args optional. The number of arguments the function accept (default 1).
     * @return boolean true
     */
    static function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1)
    {
	    $idx = Hooks::_filter_build_unique_id($tag, $function_to_add, $priority);
	    Hooks::$filter[$tag][$priority][$idx] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);
	    unset( Hooks::$mergedFilter[ $tag ] );
	    return true;
    }

    /**
     * apply_filters() - Call the functions added to a filter hook.
     *
     * The callback functions attached to filter hook <tt>$tag</tt> are invoked by
     * calling this function. This function can be used to create a new filter hook
     * by simply calling this function with the name of the new hook specified using
     * the <tt>$tag</a> parameter.
     *
     * The function allows for additional arguments to be added and passed to hooks.
     * <code>
     * function example_hook($string, $arg1, $arg2)
     * {
     *		//Do stuff
     *		return $string;
     * }
     * $value = apply_filters('example_filter', 'filter me', 'arg1', 'arg2');
     * </code>
     *
     * @param string $tag The name of the filter hook.
     * @param mixed $value The value on which the filters hooked to <tt>$tag</tt> are applied on.
     * @param mixed $var,... Additional variables passed to the functions hooked to <tt>$tag</tt>.
     * @return mixed The filtered value after all hooked functions are applied to it.
     */
    static function apply_filters($tag, $value)
    {
	    $args = array();
	    Hooks::$currentFilter[] = $tag;

	    // Do 'all' actions first
	    if ( isset(Hooks::$filter['all']) ) {
		    $args = func_get_args();
		    Hooks::_call_all_hook($args);
	    }

	    if ( !isset(Hooks::$filter[$tag]) ) {
		    array_pop(Hooks::$currentFilter);
		    return $value;
	    }

	    // Sort
	    if ( !isset( Hooks::$mergedFilter[ $tag ] ) ) {
		    ksort(Hooks::$filter[$tag]);
		    Hooks::$mergedFilter[ $tag ] = true;
	    }

	    reset( Hooks::$filter[ $tag ] );

	    if ( empty($args) )
		    $args = func_get_args();

	    do {
		    foreach( (array) current(Hooks::$filter[$tag]) as $the_ )
			    if ( !is_null($the_['function']) ){
				    $args[1] = $value;
				    $value = call_user_func_array($the_['function'], array_slice($args, 1, (int) $the_['accepted_args']));
			    }

	    } while ( next(Hooks::$filter[$tag]) !== false );

	    array_pop( Hooks::$currentFilter );

	    return $value;
    }

    /**
     * _call_all_hook() - Calls the 'all' hook, which will process the functions hooked into it.
     *
     * The 'all' hook passes all of the arguments or parameters that were used for the
     * hook, which this function was called for.
     *
     * This function is used internally for apply_filters(), do_action(), and do_action_ref_array()
     * and is not meant to be used from outside those functions. This function does not check for the
     * existence of the all hook, so it will fail unless the all hook exists prior to this function call.
     *
     * @uses Hooks::$filter Used to process all of the functions in the 'all' hook
     *
     * @param array $args The collected parameters from the hook that was called.
     * @param string $hook Optional. The hook name that was used to call the 'all' hook.
     */
    private static function _call_all_hook($args)
    {
	    reset( Hooks::$filter['all'] );
	    do {
		    foreach( (array) current(Hooks::$filter['all']) as $the_ )
			    if ( !is_null($the_['function']) )
				    call_user_func_array($the_['function'], $args);

	    } while ( next(Hooks::$filter['all']) !== false );
    }


    /**
     * Hooks::_filter_build_unique_id() - Build Unique ID for storage and retrieval
     *
     * It works by checking for objects and creating an a new property in the class
     * to keep track of the object and new objects of the same class that need to be added.
     *
     * It also allows for the removal of actions and filters for objects after they
     * change class properties. It is possible to include the property Hooks::$filter_id
     * in your class and set it to "null" or a number to bypass the workaround. However
     * this will prevent you from adding new classes and any new classes will overwrite
     * the previous hook by the same class.
     *
     * Functions and static method callbacks are just returned as strings and shouldn't
     * have any speed penalty.
     *
     * @global array Hooks::$filter Storage for all of the filters and actions
     * @param string $tag Used in counting how many hooks were applied
     * @param string|array $function Used for creating unique id
     * @param int|bool $priority Used in counting how many hooks were applied.  If === false and $function is an object reference, we return the unique id only if it already has one, false otherwise.
     * @param string $type filter or action
     * @return string Unique ID for usage as array key
     */
    private static function _filter_build_unique_id($tag, $function, $priority)
    {
	    // If function then just skip all of the tests and not overwrite the following.
	    if ( is_string($function) )
		    return $function;
	    // Object Class Calling
	    else if (is_object($function[0]) ) {
		    $obj_idx = get_class($function[0]).$function[1];
		    if ( !isset($function[0]->_filter_id) ) {
			    if ( false === $priority )
				    return false;
			    $count = count((array)Hooks::$filter[$tag][$priority]);
			    $function[0]->_filter_id = $count;
			    $obj_idx .= $count;
			    unset($count);
		    } else
			    $obj_idx .= $function[0]->_filter_id;
		    return $obj_idx;
	    }
	    // Static Calling
	    else if ( is_string($function[0]) )
		    return $function[0].$function[1];
    }

    /**
     * add_action() - Hooks a function on to a specific action.
     *
     * Actions are the hooks that the BIGACE core launches at specific points
     * during execution, or when specific events occur. Plugins can specify that
     * one or more of its PHP functions are executed at these points, using the
     * Action API.
     *
     * @uses add_filter() Adds an action. Parameter list and functionality are the same.
     *
     * @param string $tag The name of the action to which the <tt>$function_to-add</tt> is hooked.
     * @param callback $function_to_add The name of the function you wish to be called.
     * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
     * @param int $accepted_args optional. The number of arguments the function accept (default 1).
     */
    static function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	    return Hooks::add_filter($tag, $function_to_add, $priority, $accepted_args);
    }


    /**
     * do_action() - Execute functions hooked on a specific action hook.
     *
     * This function invokes all functions attached to action hook <tt>$tag</tt>.
     * It is possible to create new action hooks by simply calling this function,
     * specifying the name of the new hook using the <tt>$tag</tt> parameter.
     *
     * You can pass extra arguments to the hooks, much like you can with apply_filters().
     *
     * @see apply_filters() This function works similar with the exception that nothing is
     * returned and only the functions or methods are called.
     *
     * @param string $tag The name of the action to be executed.
     * @param mixed $arg,... Optional additional arguments which are passed on to the functions hooked to the action.
     * @return null Will return null if $tag does not exist in Hooks::$filter array
     */
    static function do_action($tag, $arg = '') {
	    if ( is_array(Hooks::$actions) )
		    Hooks::$actions[] = $tag;
	    else
		    Hooks::$actions = array($tag);

	    Hooks::$currentFilter[] = $tag;

	    // Do 'all' actions first
	    if ( isset(Hooks::$filter['all']) ) {
		    $all_args = func_get_args();
		    Hooks::_call_all_hook($all_args);
	    }

	    if ( !isset(Hooks::$filter[$tag]) ) {
		    array_pop(Hooks::$currentFilter);
		    return;
	    }

	    $args = array();
	    if ( is_array($arg) && 1 == count($arg) && is_object($arg[0]) ) // array(&$this)
		    $args[] =& $arg[0];
	    else
		    $args[] = $arg;
	    for ( $a = 2; $a < func_num_args(); $a++ )
		    $args[] = func_get_arg($a);

	    // Sort
	    if ( !isset( Hooks::$mergedFilter[ $tag ] ) ) {
		    ksort(Hooks::$filter[$tag]);
		    Hooks::$mergedFilter[ $tag ] = true;
	    }

	    reset( Hooks::$filter[ $tag ] );

	    do {
		    foreach ( (array) current(Hooks::$filter[$tag]) as $the_ )
			    if ( !is_null($the_['function']) )
				    call_user_func_array($the_['function'], array_slice($args, 0, (int) $the_['accepted_args']));

	    } while ( next(Hooks::$filter[$tag]) !== false );

	    array_pop(Hooks::$currentFilter);
    }


	/**
	 * current_filter() - Return the name of the current filter or action.
	 *
	 * @return string Hook name of the current filter or action.
	 */
	static function current_filter() {
		return end( Hooks::$currentFilter );
	}


	/**
	 * has_filter() - Check if any filter has been registered for a hook.
	 *
	 * @param string $tag The name of the filter hook.
	 * @param callback $function_to_check optional.  If specified, return the priority of that function on this hook or false if not attached.
	 * @return int|boolean Optionally returns the priority on that hook for the specified function.
	 */
	static function has_filter($tag, $function_to_check = false) {
		$has = !empty(Hooks::$filter[$tag]);
		if ( false === $function_to_check || false == $has )
			return $has;

		if ( !$idx = Hooks::_filter_build_unique_id($tag, $function_to_check, false) )
			return false;

		foreach ( array_keys(Hooks::$filter[$tag]) as $priority ) {
			if ( isset(Hooks::$filter[$tag][$priority][$idx]) )
				return $priority;
		}

		return false;
	}

	/**
	 * has_action() - Check if any action has been registered for a hook.
	 *
	 * @param string $tag The name of the action hook.
	 * @param callback $function_to_check optional.  If specified, return the priority of that function on this hook or false if not attached.
	 * @return int|boolean Optionally returns the priority on that hook for the specified function.
	 */
	static function has_action($tag, $function_to_check = false) {
		return Hooks::has_filter($tag, $function_to_check);
	}

	/**
	 * did_action() - Return the number times an action is fired.
	 *
	 * @param string $tag The name of the action hook.
	 * @return int The number of times action hook <tt>$tag</tt> is fired
	 */
	static function did_action($tag) {
		if ( empty(Hooks::$actions) )
			return 0;

		return count(array_keys(Hooks::$actions, $tag));
	}

	/**
	 * remove_action() - Removes a function from a specified action hook.
	 *
	 * This function removes a function attached to a specified action hook. This
	 * method can be used to remove default functions attached to a specific filter
	 * hook and possibly replace them with a substitute.
	 *
	 * @param string $tag The action hook to which the function to be removed is hooked.
	 * @param callback $function_to_remove The name of the function which should be removed.
	 * @param int $priority optional The priority of the function (default: 10).
	 * @param int $accepted_args optional. The number of arguments the function accpets (default: 1).
	 * @return boolean Whether the function is removed.
	 */
	static function remove_action($tag, $function_to_remove, $priority = 10, $accepted_args = 1) {
		return Hooks::remove_filter($tag, $function_to_remove, $priority, $accepted_args);
	}

	/**
	 * remove_filter() - Removes a function from a specified filter hook.
	 *
	 * This function removes a function attached to a specified filter hook. This
	 * method can be used to remove default functions attached to a specific filter
	 * hook and possibly replace them with a substitute.
	 *
	 * To remove a hook, the <tt>$function_to_remove</tt> and <tt>$priority</tt> arguments
	 * must match when the hook was added. This goes for both filters and actions. No warning
	 * will be given on removal failure.
	 *
	 * @param string $tag The filter hook to which the function to be removed is hooked.
	 * @param callback $function_to_remove The name of the function which should be removed.
	 * @param int $priority optional. The priority of the function (default: 10).
	 * @param int $accepted_args optional. The number of arguments the function accpets (default: 1).
	 * @return boolean Whether the function existed before it was removed.
	 */
	static function remove_filter($tag, $function_to_remove, $priority = 10, $accepted_args = 1) {
		$function_to_remove = Hooks::_filter_build_unique_id($tag, $function_to_remove, $priority);

		$r = isset(Hooks::$filter[$tag][$priority][$function_to_remove]);

		if ( true === $r) {
			unset(Hooks::$filter[$tag][$priority][$function_to_remove]);
			if ( empty(Hooks::$filter[$tag][$priority]) )
				unset(Hooks::$filter[$tag][$priority]);
			unset(Hooks::$mergedFilter[$tag]);
		}

		return $r;
	}
}
