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
 * @subpackage layout
 */

import('api.menu.MenuTemplate');

/**
 * A Layout is a Defintion of a set of PHP Pages.
 * 
 * Each Layout has at least a Name and a default Template, which is 
 * used to render a requested Menu.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage layout
 */
class Layout extends MenuTemplate
{
    /**
     * @access private
     */
    var $layout = array();
    /**
     * @access private
     */
    var $exists   = FALSE;
    /**
     * @access private
     */
    var $key;       

    /**
    * Initializes the Object with the given Name
    *
    * @param    String the Name
    * @param    String the Key
    */
    function Layout($name, $key = '')
    {
        $this->key = $key;
        // TODO sanitize filename
        $layoutname = _BIGACE_DIR_CID . 'presentation/definition/' . $name . '.php';
        if (($this->exists = file_exists($layoutname))) {
        	$DEFINITION = array();
            include ($layoutname);
            $this->layout = $DEFINITION[$name];
        }
    }
    
    /**
     * Returns the Name of this Layout.  
     */
    function getName() 
    {
    	return $this->getSetting('NAME');
    }

    /**
     * Returns the Title of this Layout.  
     */
    function getTitle() 
    {
    	return $this->getSetting('TITLE');
    }

    /**
     * Returns whether this Layout is public and therefor may be visible within Dialogs.  
     */
    function isPublic() 
    {
        return (isset($this->layout['PUBLIC']) ? $this->layout['PUBLIC'] : false);
    }
    
    /**
     * Returns whether this Layout is a System Layout (which may not be public visible).  
     */
    function isSystem() 
    {
        return !$this>isPublic();
    }

    /**
     * Returns the Description of this Layout. 
     */
    function getDescription() 
    {
    	return $this->getSetting('DESCRIPTION');
    }
    
    /**
     * Returns whether this Layout supports dynamic Portlets.  
     */
    function hasPortletSupport() {
        //$t = $this->getPortletNames();
        $t = $this->getPortletColumns();
        return (is_array($t) && count($t) > 0);
    }
    
    /**
     * Return an Array with all supported Portlet Names for this Layout. 
     * -- REMOVED WITH 2.1 --- ALWAYS SHOW ALL PORTLETS --
    function getPortletNames() {
         return $this->getSetting('portlet', array());
    }*/

    /**
     * @return array an array of column names
     */
    function getPortletColumns() {
         return $this->getSetting('portlet_columns', array());
    }

    /**
     * Returns the URL to this Layout. 
     * If a SubKey was specified it returns the URL to this Template. 
     */
    function getURL() 
    {
        $url = $this->getSetting('STANDARD'); 
        if ($this->key != '' && $this->existsKey($this->key)) {
        	$url = $this->getURLForKey($this->key);
        }
        return $url;
    }

    /**
     * Returns the URL for the subKey to this Template. 
     */
    function getURLForKey($key) 
    {
        foreach ($this->getKeys() AS $k => $v) {
            if ($key == $k) {
                return $v;
            }
        }
        return '';
    }

    /**
     * Returns the Full URL to this Template. 
     */
    function getFullURL() 
    {
        return $GLOBALS['_BIGACE']['DIR']['consumer'] . 'presentation/layout/' . $this->getURL();
    }
    
    /**
     * Returns all configured Keys for this Layout. 
     */
    function getKeys() 
    {
        return $this->getSetting('KEYS', array());
    }

    /**
     * Returns the Default CSS for this Layout. May be used by the Editors to preview HMTL.
     */
    function getCSS() 
    {
        return $this->getSetting('CSS');
    }

    /**
     * Returns an array of Strings, each String representing one Name of an additional Content piece.
     * Each of these Content pieces will be editable as HTML Content.
     * If an empty array or null is returned, no additional content columns are defined.
     * @return array an array of Strings, an empty array or null  
     */
    function getContentNames() {
        return $this->getSetting('CONTENT', array());
    }
        
    /**
     * Returns the configured Setting with the given Name or default (empty String) 
     * if none could be found. 
     */
    function getSetting($name, $default = '')
    {
    	if (isset($this->layout[$name])) {
    		return $this->layout[$name];
    	}
    	return $default;
    }
    
    /**
     * Checks if the given key exists in this Layout.
     * If no key was passed, we check for the Key this Layout was constructed with. 
     */
    function existsKey($key = '')
    {
    	if ($key == '') $key = $this->key;
        $bla = $this->getSetting('KEYS', array());
        return isset($bla[$key]);
    }

	/**
	 * Return the File Size of this Layout.
	 */
	function getSize()
	{
	    if ( file_exists($this->getFullURL()) ) {
		    return filesize($this->getFullURL());
	    }
        return 0;
	}
	
	/**
	 * Check whether this Layout really exist.
	 */
	function exists() 
	{
	    return $this->exists;
	}
	
	/**
	 * Returns the qualified String representation of this Layout to be appended to Command Links.
	 */
	function toString() 
	{
		$s = '';
		if ($this->exists()) {
			$s = '_t' . $this->getName();
			if ($this->key != '')
				$s .= '_k' . $this->key; 
		}
		return $s;
	}
    
}

?>