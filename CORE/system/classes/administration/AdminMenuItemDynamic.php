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
 * @subpackage administration
 */

import('classes.util.Translations');
import('classes.administration.AdminMenuItem');

/**
 * Class used for admin Menu wrapper.
 * Represents a Admin TOP LEVEL Menu.
 *
 * Note: There are two kind of Admin Menus, TOP Level and direct Childs.
 * There is no plan for the future to add deeper Menus Structures.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage administration
 */
class AdminMenuItemDynamic extends AdminMenuItem
{

    /**
     * Initializes this Menu with the Settings of the given ID.
     */
    function AdminMenuItemDynamic($directory, $id, &$parent)
    {
        $name = $id . '.ini';
        $config = array('id' => $id, 'name' => $id, 'translate' => FALSE, 'frights' => array(), 'menu.translate' => FALSE, 'hide' => FALSE);

        // Plugin depend settings
        if (is_file($directory . '/' . $name))  {
            $ini = parse_ini_file($directory . '/' . $name, TRUE);
            $config = array_merge($config,$ini);
        }

        $this->AdminMenuItem($config, $parent);
    }

    function loadTranslation() {
        Translations::loadGlobal($this->getID(), ADMIN_LANGUAGE, $this->getPath());
    }
    
}
