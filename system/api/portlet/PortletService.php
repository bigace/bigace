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
 * @package bigace.api
 * @subpackage portlet
 */

/**
 * The default Project Field that is used for writing and reading Portlets.
 */
define('PORTLET_DEFAULT_COLUMN', '1');

/**
 * This class provides methods for reading and saving of Portlets.
 *  
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.api
 * @subpackage portlet
 */
class PortletService
{
    /**
     * If this function is called with <code>true</code> also 
     * the Portlets are rendered, that return <code>false</code> for 
     * <code>Portlet->displayPortlet()</code>. 
     * @param boolean ignorePortletDisplaySetting whether the Portlets setting should be ignored or not
     */
    function setIgnoreDisplaySetting($ignorePortletDisplaySetting) {
    }
    
    /**
     * Get an array with ready parsed and configured Portlets for the given 
     * Itemtype and ID. Even if Portlets are ment to be used with Menus, 
     * it is possible to fetch them for all Itemtypes.
     * 
     * If no Column is submitted <code>PORTLET_DEFAULT_COLUMN</code> is used.
     * 
     * @param int itemtype the Itemtype to fetch the Portlets for
     * @param long itemid the ItemID to fetch the Portlets for
     * @param String languageid the LanguageID to fetch the Portlets for
     * @param String column the Name of the Column to fetch the Portlets for
     * @return array all Portlets for the given Item
     */
    function getPortlets($itemtype, $itemid, $languageid, $column = null) {
        return array(); 
    }

    /**
     * Saves the given Portlets for the Item.
     * Pass null or an empty array to delete portlet settings.
     * 
     * If no Column is submitted <code>PORTLET_DEFAULT_COLUMN</code> is used.
     * 
     * @param int itemtype the Itemtype to fetch the Portlets for
     * @param long itemid the ItemID to fetch the Portlets for
     * @param String languageid the LanguageID to fetch the Portlets for
     * @param array portlets an Array with configured Portlets to save
     * @param String column the Name of the Column to fetch the Portlets for
     * @return boolean whther we cold save the Portlets or not
     */
    function savePortlets($itemtype, $itemid, $languageid, $portlets, $column = null) {
        return false;
    }
    
}
 
?>