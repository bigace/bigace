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
 * This file holds method regarding the unqiue name features.
 */

import('classes.item.Item');
import('classes.item.MasterItemType');


/**
* Finds an Item by its unique Name. If none could be found, null is returned.
*
* @param String $uniqueName the unique name to lookup 
* @return mixed the Item or null
*/
function bigace_item_by_unique_name($uniqueName) {
	// prepare sql to find the item reference by its unique name
	$res = bigace_unique_name_raw($uniqueName);
	if($res != null) {
		$mit = new MasterItemType();
		$itemtype = $mit->getItemTypeForCommand($res['command']);
		return new Item($itemtype, $res['itemid'], ITEM_LOAD_LIGHT, $res['language']);
	}
	
	return null;
}

/**
 * Fetch the raw result for a unique name. If none could be found, null is returned.
 *
 * @param String $uniqueName the unqiue name to lookup
 * @return mixed the array result or null 
 */
function bigace_unique_name_raw($uniqueName) 
{
	// prepare sql to find the item reference by its unique name
	$values = array ( 'UNIQUE_NAME' => $uniqueName );
	$sql = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('unique_name_find');
	$sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
	$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);

		// found unique name - fetch values from result
	if($res->count() != 0) {
		return $res->next();
	}

	return null;
}

/**
 * Find the maximum number of this unique url. Returns false if none is found.
 * Example: You have the files:
 * test-1.jpg/test-2.jpg and test-3.jpg
 * this method would  return 3 for the call bigace_unique_name_max("test-") and 
 * false for bigace_unique_name_max("foo").
 *
 * @param String $uniqueName the unqiue name to lookup
 * @return mixed the result or false
 */
function bigace_unique_name_max($uniqueName) {
	
	// prepare sql to find the item reference by its unique name
	$values = array ( 'UNIQUE_NAME' => $uniqueName );
	$sql = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('unique_name_find');
	$sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement('SELECT * FROM {DB_PREFIX}unique_name WHERE name like "%'.$uniqueName.'%" AND cid = {CID} ORDER BY name DESC LIMIT 0,1', $values, true);
	$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);

    // found unique name - fetch values from result
	if($res->count() != 0) {
		$name = $res->next();
		$name = $name["name"];
		$name = str_replace($uniqueName,"",$name);
		$name = substr($name, 0, strpos($name, ".")); 
		return $name;
	}

	return false;
}

?>