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
 * @subpackage template
 */

import('api.menu.MenuTemplate');
import('classes.smarty.SmartyTemplate');
import('classes.smarty.SmartyStylesheet');

/**
 * This represents a Design which is chooseable in the Menu Administration.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage smarty
 */
class SmartyDesign extends MenuTemplate
{
	/**
	 * @access private
	 */
    private $value;
    private $portlets;
    private $contents;

    function SmartyDesign($name = null)
    {
    	if($name != null)
    		$this->loadByName($name);
    }
    
    /**
     * @access protected
     */
    function setArray($values){
    	$this->value = $values;
    }
    
    /**
     * @access private
     */
    function loadByName($name)
    {
	    $values = array( 'NAME' => $name );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_design_load');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values,true);
        $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $this->setArray( $temp->next() );
        unset($temp);
    }

	/**
	 * @return String the Design Name
	 */
    function getName() {
        return $this->value["name"];
    }
    
	/**
	 * @return String the Design Description
	 */
    function hasPortletSupport() {
        return ($this->value["portlets"] == '1');
    }

	/**
	 * @return String the Design Description
	 */
    function getDescription() {
        return $this->value["description"];
    }
	
	/**
	 * @return SmartyStylesheet the associated Stylesheet
	 */
    function getStylesheet() {
        return new SmartyStylesheet($this->value["stylesheet"]);
    }

	/**
	 * @return SmartyTemplate the associated Template
	 */
    function getTemplate() {
        return new SmartyTemplate($this->value["template"]);
    }

	/**
	 * Returns an array with names of Portlet columns. Array might be empty.
	 * @return array the supported Portlet columns
	 */
    function getPortletColumns() {
    	if(is_null($this->portlets)) {
    		$values = array('DESIGN' => $this->getName());
    		$this->portlets = $GLOBALS['_BIGACE']['SQL_HELPER']->fetchAll('design_portlets_get', $values, true, 'name');
    	}
    	return $this->portlets; 
    }
	
    /**
     * Returns an array of Strings, each String representing one Name of an additional Content piece.
     * Each of these Content pieces will be editable as HTML Content.
     * If an empty array or null is returned, no additional content columns are defined.
     * @return array an array of Strings, an empty array or null  
     */
    function getContentNames() {
    	if(is_null($this->contents)) {
    		$values = array('DESIGN' => $this->getName());
	        $this->contents = $GLOBALS['_BIGACE']['SQL_HELPER']->fetchAll('design_contents_get', $values, true, 'name');
     	}
    	return $this->contents; 
    }    
}
?>