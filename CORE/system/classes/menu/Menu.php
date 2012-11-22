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
 * @subpackage menu
 */

import('classes.item.Item');
import('classes.modul.Modul');

/**
 * Class used for handling Menus.
 *
 * For currently used Text/Num/Date fields, see Item.php.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage menu
 */
class Menu extends Item
{
    
    /**
     * Instantiates a Menu representing the given Menu ID.
     * If you pass null as ID the Object will not be initialized but only
     * instantiated.
     *
     * @param int the Menu ID or null
     * @param String the treetype
     * @param String the Language ID
     */
    function Menu($id=null, $treetype=ITEM_LOAD_FULL, $languageID='')
    {
    	if(func_num_args() > 0)
        	$this->init(_BIGACE_ITEM_MENU, $id, $treetype, $languageID);
    }
    
    /**
    * Gets the Modul ID for the current Menu.
    * @return String the Modul ID
    */
    function getModulID() 
    {
        return $this->getItemText('3');
    }

    /**
    * Gets the Layout Name for this Menu.
    * @return String the Layout Name
    */
    function getLayoutName()
    {
        return $this->getItemText('4');
    }

    /**
     * Gets the Modul that is linked to this Menu.
     *
     * @return Modul the Modul for this Menu
     */
    function getModul() 
    {
        return new Modul($this->getModulID());
    }

    /**
     * Gets a Menu instance that holds all information about the Parent MenuEntry.
     *
     * @return Menu the Parent of this Menu
     */
    function getParent()
    {
        return new Menu($this->getParentId(), ITEM_LOAD_FULL, $this->getLanguageID());
    }
    
    /**
     * Checks whether this is the Root Menu or not.
     */
    function isRoot() 
    {
        return ($this->getID() == _BIGACE_TOP_LEVEL || $this->getParentID() == _BIGACE_TOP_PARENT);
    }

}

?>