<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.<br>Copyright (C) Kevin Papst.
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

/**
 * This represents a MenuTemplate, that is used to render a requested Menu.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.api
 * @subpackage menu
 */
class MenuTemplate
{
	/**
     * Returns the Name of this Template. 
	 * @return String the Template Name
	 */
    function getName() {
        return null;
    }

    /**
     * Returns the Description of this Template. 
	 * @return String the Template Description
     */
    function getDescription() {
    	return null;
    }

    /**
     * Returns whether this Layout supports dynamic Portlets.
     * @return boolean if portlets are supported  
     */
    function hasPortletSupport() {
        return false;
    }
    
    /**
     * Returns an array of Strings, each String representing one 
     * Portlet column definition.
     * Each of these Strings will be represented in the Portlet Administration 
     * as a Select Box.
     * If an empty array or null is returned, the default value will be taken:
     * See constant PORTLET_DEFAULT_COLUMN.
     * @return array an array of Strings, an empty array or null  
     */
    function getPortletColumns() {
    	return array();
    }
	   
    /**
     * Returns an array of Strings, each String representing one Name of an additional Content piece.
     * Each of these Content pieces will be editable as HTML Content.
     * If an empty array or null is returned, no additional content columns are defined.
     * @return array an array of Strings, an empty array or null  
     */
    function getContentNames() {
    	return array();
    }
    
}
?>