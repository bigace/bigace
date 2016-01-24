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
 * @subpackage configuration
 */

// REMEMBER TO ADD EVERY NEW TYPE TO THE LIST OF TYPES
// IN THE CONFIGURATION ADMIN PANEL
if (!defined('CONFIG_TYPE_STRING'))
{
	/**
	 * Marks a Config Entry as a String Type.
	 * @access public
	 */
 	define('CONFIG_TYPE_EDITOR', 'editor');
	/**
	 * Marks a Config Entry as a Language locale.
	 * @access public
	 */
 	define('CONFIG_TYPE_LANGUAGE', 'language');
 	/**
	 * Marks a Config Entry as a Category ID.
	 * @access public
	 */
 	define('CONFIG_TYPE_CATEGORY_ID', 'category');
 	/**
	 * Marks a Config Entry as a String Type.
	 * @access public
	 */
 	define('CONFIG_TYPE_STRING', 'string');
	/**
	 * Marks a Config Entry as a Admin Style.
	 * @access public
	 */
 	define('CONFIG_TYPE_ADMIN_STYLE', 'adminstyle');
	/**
	 * Marks a Config Entry as a Integer Type.
	 * @access public
	 */
 	define('CONFIG_TYPE_INT', 'integer');
	/**
	 * Marks a Config Entry as a Long Type.
	 * @access public
	 */
 	define('CONFIG_TYPE_LONG', 'long');
	/**
	 * Marks a Config Entry as a Boolean Type.
	 * @access public
	 */
 	define('CONFIG_TYPE_BOOLEAN', 'boolean');
	/**
	 * Marks a Config as a Timestamp. 
	 * @access public
	 */
 	define('CONFIG_TYPE_TIMESTAMP', 'timestamp');
	/**
	 * Marks a Config as a Menu ID. 
	 * @access public
	 */
 	define('CONFIG_TYPE_MENU_ID', 'menu_id');
    /**
     * Marks a Config as a User Group ID. 
     * @access public
     */
    define('CONFIG_TYPE_GROUP_ID', 'group');
    /**
     * Marks a Config as a Template. 
     * @access public
     */
    define('CONFIG_TYPE_TEMPLATE', 'template');
    /**
     * Marks a Config as a Template or Include. 
     * @access public
     */
    define('CONFIG_TYPE_TEMPLATE_INCLUDE', 'tpl_inc');
    /**
     * Marks a Config as a Design. 
     * @access public
     */
    define('CONFIG_TYPE_DESIGN', 'design');
    /**
     * Marks a Config as LogLevel. 
     * @access public
     */
    define('CONFIG_TYPE_LOGLEVEL', 'loglevel');
    /**
     * Marks a Config as ClassName. 
     * @access public
     */
    define('CONFIG_TYPE_CLASSNAME', 'class');
}

/**
 * This class represents one Configuration Entry.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage configuration
 */
class ConfigurationEntry
{
	/**
	 * @access private
	 */
	var $entry = array();
	
	function ConfigurationEntry($entryArray)
	{
		$this->entry = $entryArray;
	} 
	
	function getPackage() {
		return $this->_getInternalValue('package'); 
	}

	function getType() {
		return $this->_getInternalValue('type'); 
	}

	function getName() {
		return $this->_getInternalValue('name');
	}

	function getValue() {
		return $this->_getInternalValue('value'); 
	}
    
	function getConsumerID() {
		return $this->_getInternalValue('cid'); 
	}
	
	/**
	 * @access private
	 */
	function _getInternalValue($name) {
		// set value if not already filled with data
		if (!isset($this->entry[$name])) 
			$this->entry[$name] = '';
		return $this->entry[$name];
	} 

}

?>