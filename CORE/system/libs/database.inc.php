<?php
//
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS for MySQL                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) Kevin Papst                                              |
// | Web           http://www.bigace.de                                     |
// | Mirror        http://bigace.sourceforge.net/                           |
// | Sourceforge   http://sourceforge.net/projects/bigace/                  |
// +------------------------------------------------------------------------+
// | This source file is subject to version 2 or (at your option) any later |
// | version, of the GNU General Public License as published by the Free    |
// | Software Foundation, available at:                                     |
// | http://www.gnu.org/licenses/gpl.html                                   |
// +------------------------------------------------------------------------+
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// +------------------------------------------------------------------------+
//

/**
 * Helper functions for database manipulation.
 * 
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @deprecated DO NOT USE ANY LONGER!
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.libs
 */
function get_all_table_names() {
	$names = array();
	$xml = simplexml_load_file(_BIGACE_DIR_ROOT . '/system/sql/structure.xml');
	foreach($xml->table AS $table)
		$names[] = $table['name'];
	return $names;
}


?>