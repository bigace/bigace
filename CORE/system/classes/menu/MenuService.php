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

import('classes.item.ItemService');
import('classes.menu.Menu');
import('classes.language.ItemLanguageEnumeration');

/**
 * Class used for handling Menus.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage menu
 */
class MenuService extends ItemService
{

    /**
    * Instantiates a MenuService.
    */
    function MenuService()
    {
        $this->initItemService(_BIGACE_ITEM_MENU);
    }
    
    function getMenu($id, $languageID = null)
    {
        if ($languageID != null && $this->existsLanguageVersion($id, $languageID)) {
            return $this->getClass($id, ITEM_LOAD_FULL, $languageID);
        } else {
            return $this->getClass($id);
        }
    }
    
    function existsLanguageVersion($id, $langid)
    {
        $enum = $this->getItemLanguageEnumeration($id);
        for ($i=0; $i<$enum->count(); $i++) {
            $tempLang = $enum->next();
            if ($tempLang->getID() == $langid) {
                return true;
            }
        }
        return false;
    }

}

?>