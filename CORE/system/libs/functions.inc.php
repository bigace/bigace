<?php
//
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS for MySQL                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) Kevin Papst                                              |
// | Web           http://www.bigace.de                                     |
// | Sourceforge   http://sourceforge.net/projects/bigace/                  |
// +------------------------------------------------------------------------+
// | This source file is subject to version 2 or (at your option) any later |
// | version, of the GNU General Public License as published by the Free    |
// | Software Foundation, available at:                                     |
// | http://www.gnu.org/licenses/gpl.html                                   |
// +------------------------------------------------------------------------+
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// +------------------------------------------------------------------------+
//

import('classes.util.Translations');
import('classes.util.LinkHelper');
import('classes.permission.ItemPermission');

/**
 * Library including all standard procedures that are explicitely needed for BIGACE.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.libs
 */

/**
 * Loads a Language File. The Translations (or simply keys) will be accessible through <code>getTranslation('foo')</code>.
 * This trys to load the Language File in the current Session Language Locale.
 * If you want to specify which Locale will be loaded, pass the short
 * locale as second parameter ('en', 'de', ...):
 * (Example: load "system.lang.php" by calling loadLanguageFile('system')
 *
 * @param String the Language Filename (before extension '.lang.php').
 */
function loadLanguageFile($filename, $locale = _ULC_, $directory = '')
{
    Translations::loadGlobal($filename, $locale, $directory);
}


/**
 * Loads an value from the Language File, pass a second parameter for the case the translation cannot be found.
 *
 * @param    String  the translation key
 * @param    String  a String being returned if no translation could be found
 * @return   String  the translation
 */
function getTranslation($name, $common = '')
{
    return Translations::translateGlobal($name, $common);
}


/**
 * Searches for the given varname in _POST, _GET, _COOKIE and then in _GLOBALS Variable Scope.
 * If one is found it will be returned, else the notfound value is returned.
 *
 * @param    String the varname to lookup
 * @param    String the varname to return is none was found
 * @return   String the varname or notfound if none was given
 */
function extractVar($varname, $notfound = '')
{
    if ( isset($_POST[$varname]) ) {
        return $_POST[$varname];
    } else if ( isset($_GET[$varname]) ) {
        return $_GET[$varname];
    } else if ( isset($_COOKIE[$varname]) ) {
        return $_COOKIE[$varname];
    } else if ( isset($GLOBALS[$varname]) ) {
        return $GLOBALS[$varname];
    }
    return $notfound;
}

/**
 * Returns a permanent link to the current page.
 */
function get_permalink($params = array()) {
	if(!isset($GLOBALS['MENU']))
		return null;

	return LinkHelper::getUrlFromCMSLink( LinkHelper::getCMSLinkFromItem($GLOBALS['MENU']), $params );
}

/**
 * Creates a Link to the given Menu Id.
 * Uses the function <code>createLink($params)</code>.
 *
 * @param    mixed   the Menu ID
 * @param    array   key - value pairs to append to the link (leave for none)
 * @return   String  the MenuLink
 */
function createMenuLink($mid = '', $params = array(), $name = '')
{
    $mid = ($mid === '') ? _BIGACE_TOP_LEVEL : $mid;
    return createCommandLink(_BIGACE_CMD_MENU, $mid, $params, $name);
}

/**
 * Creates any BIGACE Link - including the Session ID.
 * You should use this function to avoid a loose of the User Session in case of not accepted Cookies.
 * @deprecated do not use any longer!
 *
 * @param    array  the Parameter as key-value mapping
 * @param    String the link address
 * @return   String the url
 */
function createLink($params, $adress = '')
{
	return LinkHelper::createLink($adress, $params);
}

/**
 * Creates a BIGACE Command Link.
 * @deprecated do not use any longer!
 *
 * @param    String  the BIGACE command
 * @param    String  the BIGACE ID
 * @param    array   the URL Parameter as key-value mapping
 * @param    String  the file name
 * @return   String  the formatted Link
 */
function createCommandLink($cmd, $id, $params = array(), $name = '')
{
    if ($name == '') $name = 'index.php';

	$cmdLink = new CMSLink();
	$cmdLink->setCommand($cmd);
	$cmdLink->setFileName($name);
	$cmdLink->setItemID($id);
	return LinkHelper::getUrlFromCMSLink($cmdLink, $params);
}

/**
 * Returns a random String.
 * @return String the Random String
 */
function getRandomString()
{
    mt_srand((double)microtime()*1000000);
    return md5 (uniqid (mt_rand()));
}

/**
 * Returns if the given String is a Parameter that is already used by the System.
 * If returning true DO NOT use this Key as URL Parameter!
 *
 * @param    String  a key that should be used as URL Param
 * @return   boolean if key can be used or not
 */
function isSystemParameter($key)
{
    return ($key == 'cmd' || $key == 'id' || $key == 'name' || $key == bigace_session_name());
}

/**
 * Loads a BIGACE Core Class dynamically.
 * This simulates a dynamic class loader and saves you from the requirement
 * to know about the directory structure or File naming.
 *
 * @deprecated use import() instead
 * @param string the subpackage where the class is located
 * @param string the class to load
 */
function loadClass($subpackage, $classname)
{
	import('classes.'.$subpackage.'.'.$classname);
}

/**
 * Import a class or interface using the java naming syntax.
 *
 * This method replaces the older <code>loadClass($subpackage, $classname)</code>,
 * because you can load even files in the Interface (API) folder.
 *
 * For each file you have to pass the package as well (classes/api), cause
 * its used include path begins one filesystem level higher.
 *
 * Old:
 * <code>loadClass('item', 'Item');</code>
 * New:
 * <code>import('classes.item.Item');</code>
 * *
 * You can load classes in deeper packages than the old ones, simply by passing
 * more levels (api.authentication.ldap.LDAPAuthenticator).
 *
 * @param string name the name of the package to be imported
 * @return void
 */
function import($name)
{
	include_once(_BIGACE_DIR_ROOT.'/system/'.str_replace('.', DIRECTORY_SEPARATOR, $name) . '.php');
}


/**
 * Creates and returns an instance of the Class, given by its full qualified Classname.
 * If the class does not exist, this method returns null!
 *
 * @param String classname the name of the class to be instanciated
 * @return Object or null
 */
function createInstance($classname)
{
    import($classname);
    $s = explode('.', $classname);
	if(class_exists($s[count($s)-1]))
	    return new $s[count($s)-1]();

	return null;
}

/**
 * Checks, if the passed object is a subclass or instance of the given classname.
 * Attention: The check is case insensitive, for PHP4 compatibility. This may change in the future!
 *
 * @return boolean wheter the given object is an instance or subclass of the given classname
 */
function is_class_of(&$obj, $className)
{
    if(is_subclass_of($obj, $className))
        return true;

    return (strtolower(get_class($obj)) == strtolower($className));
}

/**
 * Checks if a usergroup has the given permission.
 *
 * @param int group_id the Usergroup ID to check
 * @param String permission the permission string to check
 * @return boolean whether the group has the permission or not
 */
function has_group_permission($group_id, $permission)
{
	static $GROUP_PERM_CACHE = array();
    if (isset($GROUP_PERM_CACHE[_CID_][$group_id][$permission])) {
        return $GROUP_PERM_CACHE[_CID_][$group_id][$permission];
    }

    $values = array( 'GROUP_ID'     => $group_id,
                     'FRIGHT_NAME'  => $permission,
                     'CID'          => _CID_ );

    $res = $GLOBALS['_BIGACE']['SQL_HELPER']->sql('fright_has_group_fright', $values);
	$res = !(is_null($res) || $res->isError() || $res->count() == 0);
	$GROUP_PERM_CACHE[_CID_][$group_id][$permission] = $res;

    return $GROUP_PERM_CACHE[_CID_][$group_id][$permission];
}

/**
 * Checks if a permission exists for the given User.
 *
 * @param int userid the User ID to check
 * @param String permission the permission string to check
 * @return boolean whether the User has the permission or not
 */
function has_user_permission($userid, $permission)
{
    if ($userid == _BIGACE_SUPER_ADMIN) {
        return TRUE;
    }

	// cache permission access
	static $USER_PERM_CACHE = array();
    if (isset($USER_PERM_CACHE[_CID_][$userid][$permission])) {
        return $USER_PERM_CACHE[_CID_][$userid][$permission];
    }

    $values = array( 'USER_ID'      => $userid,
                     'FRIGHT_NAME'  => $permission,
                     'CID'          => _CID_ );

    $res = $GLOBALS['_BIGACE']['SQL_HELPER']->sql('fright_has_group_fright_by_user', $values);
	$res = !(is_null($res) || $res->isError() || $res->count() == 0);
	$USER_PERM_CACHE[_CID_][$userid][$permission] = $res;

    return $USER_PERM_CACHE[_CID_][$userid][$permission];
}

/**
 * Checks if the current user has the given functional permission.
 *
 * @param String permission the permission string to check
 * @return boolean whether the User has the permission or not
 */
function has_permission($permission)
{
	return has_user_permission($GLOBALS['_BIGACE']['SESSION']->getUserID(), $permission);
}

/**
 * Checks if the current user has the given permission on the object, where
 * permission is one of "r" (read), "w" (write), "d" (delete).
 *
 * @param int the Itemtype to check the permission for
 * @param int the Item ID to work with
 * @param String permission the permission string to check
 * @return boolean whether the User has the permission or not
 */
function has_item_permission($itemtype, $itemid, $permission = null)
{
    $ip = get_item_permission($itemtype, $itemid);
    return $ip->can($permission);
}

/**
 * Gets the permission for the given object and current user.
 *
 * @param int the Itemtype to check the permission for
 * @param int the Item ID to work with
 * @return ItemPermission the permission object
 */
function get_item_permission($itemtype, $itemid)
{
	return new ItemPermission($itemtype, $itemid);
}

/**
 * Returns the configuration setting with the name $name from the package $package.
 *
 * @param $name the config key
 * @param $package the config package
 * @param $default the default value to be returned if key is not set
 * @return unknown_type
 */
function get_option($package, $name, $default = null)
{
	return ConfigurationReader::getConfigurationValue($package, $name, $default);
}

/**
 * Returns the SQl Helper object, used for database access.
 * @return SQLHelper the SQL Helper to use
 */
function get_db()
{
	return $GLOBALS['_BIGACE']['SQL_HELPER'];
}

/**
 * Returns the rendered content of a module.
 *
 * @throws Exception
 * @param Menu $MENU
 * @param string $modulName
 * @param string|null $language
 * @return string
 */
function getRenderedModule($MENU, $modulName, $language = null)
{
    $lang = (null !== $language) ? $language : $GLOBALS['_BIGACE']['PARSER']->getLanguage();

    if (null === $MENU) {
        $MENU = &$GLOBALS['MENU'];
    }

    if (null === $modulName) {
        $modulName = $MENU->getModulID();
    }

    $mod = new Modul($modulName);

    if ($mod === null || !is_object($mod)) {
        throw new Exception('Module does not exist: ' . $modulName);
    }

    if ($mod->isTranslated()) {
        $mod->loadTranslation($lang);
    }

    if(!file_exists($mod->getFullURL())) {
        throw new Exception('Module file does not exist: ' . $modulName);
    }

    ob_start();
    include($mod->getFullURL());
    return ob_get_clean();
}