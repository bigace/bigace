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
 * @subpackage item
 */

/**
 * This file holds several useful methods for editor handling.
 */
function bigace_get_all_editor() {
	$all = array();
    $dirHandle = opendir($GLOBALS['_BIGACE']['DIR']['editor']);
    while(false != ($tf = readdir($dirHandle))) {
    	if($tf != '.' && $tf != '..' && is_dir($GLOBALS['_BIGACE']['DIR']['editor'] . '/' . $tf) ) {
			$all[] = $tf;
		}
	}
	return $all;
}

?>
