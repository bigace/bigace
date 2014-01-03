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
 * @subpackage fright
 */
 

/**
 * The FrightService is DEPRECATED!
 * Use <code>has_group_permission($group_id, $fright);</code>
 * and <code>has_user_permission($userid, $fright);</code> instead.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage fright
 */
class FrightService
{
    /**
     * @deprecated see has_group_permission($group_id, $fright);
     */
    function hasGroupFright($group_id, $fright)
    {
		return has_group_permission($group_id, $fright);
    }

    /**
     * @deprecated see has_user_permission($userid, $fright);
     */
    function hasFright($userid, $fright)
    {
		return has_user_permission($userid, $fright);
    }

}

?>