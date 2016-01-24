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
 * This file holds several useful item requests.
 */

import('classes.item.ItemRequest');
import('classes.item.ItemEnumeration');

/**
 * Fetches the last created items for the given ItemRequest.
 * If an ID is set in the request, it will be used as parent where
 * the items will be looked-up beneath.
 * If no ID is set, the search is done at the complete site.
 *
 * @param ItemRequest $itemrequest
 * @return ItemEnumeration all found items
 */
function bigace_last_created_items($itemrequest)
{
	$itemrequest->setOrder($itemrequest->_ORDER_DESC);
	$sqlFile = 'item_select_last_created';
    if ($GLOBALS['_BIGACE']['SESSION']->isSuperUser()) {
    	$sqlFile = 'item_select_last_created_no_rights';
    }

    $ext = '';
        
    if($itemrequest->getID() != null)
    	$ext = ' AND a.parentid = ' . $itemrequest->getID();

    $excludeFlags = $itemrequest->getExcludeFlags();
    if (!is_array($excludeFlags)) $excludeFlags = array($excludeFlags);
    if(is_array($excludeFlags) && count($excludeFlags) > 0) {
        $ext .= " AND a.num_3 NOT IN (";
        for($i=0; $i < count($excludeFlags); $i++) {
            $ext .= " ".$excludeFlags[$i]." ";
            if($i < count($excludeFlags)-1)
            	$ext .= ","; 
        }
        $ext .= ") ";
    }

    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement($sqlFile);
    $values = array ( 'LANGUAGE'    => $itemrequest->getLanguageID(),
                          'LIMIT_START' => $itemrequest->getLimitFrom(),
                          'LIMIT_STOP'  => $itemrequest->getLimitTo(),
                          'USER'        => $GLOBALS['_BIGACE']['SESSION']->getUserID(),
                          'PERMISSION' 	=> _BIGACE_RIGHTS_NO,
        				  'EXTENSION'	=> $ext,
                          'ITEMTYPE'    => $itemrequest->getItemType(),
        				  'ORDER' 		=> $itemrequest->getOrder() );
    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
    return new ItemEnumeration($GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql), $itemrequest->getItemType());
}

/**
 * Fetches the last edited items for the given ItemRequest.
 * If an ID is set in the request, it will be used as parent where
 * the items will be looked-up beneath.
 * If no ID is set, the search is done at the complete site.
 *
 * @param ItemRequest $itemrequest
 * @return ItemEnumeration all found items
 */
function bigace_last_edited_items($itemrequest)
{
	$itemrequest->setOrder($itemrequest->_ORDER_DESC);
	$sqlFile = 'item_select_last_edited';
    if ($GLOBALS['_BIGACE']['SESSION']->isSuperUser()) {
    	$sqlFile = 'item_select_last_edited_no_rights';
    }
        
	$ext = '';
        
    if($itemrequest->getID() != null) {
    	$ext .= ' AND a.parentid = ' . $itemrequest->getID();
    }

    $excludeFlags = $itemrequest->getExcludeFlags();
    if (!is_array($excludeFlags)) $excludeFlags = array($excludeFlags);
    if(is_array($excludeFlags) && count($excludeFlags) > 0) {
        $ext .= " AND a.num_3 NOT IN (";
        for($i=0; $i < count($excludeFlags); $i++) {
            $ext .= " ".$excludeFlags[$i]." ";
            if($i < count($excludeFlags)-1)
            	$ext .= ","; 
        }
        $ext .= ") ";
    }
    	
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement($sqlFile);
    $values = array ( 'LANGUAGE'    => $itemrequest->getLanguageID(),
                          'LIMIT_START' => $itemrequest->getLimitFrom(),
                          'LIMIT_STOP'  => $itemrequest->getLimitTo(),
                          'USER'        => $GLOBALS['_BIGACE']['SESSION']->getUserID(),
                          'PERMISSION' => _BIGACE_RIGHTS_NO,
        				  'EXTENSION'	=> $ext,
                          'ITEMTYPE'    => $itemrequest->getItemType(),
        				  'ORDER' 		=> $itemrequest->getOrder() );
    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
    return new ItemEnumeration($GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql), $itemrequest->getItemType());
}

/**
 * Fetches all items that have the ID that is EQUAL or LIKE the ID
 * given in the ItemRequest.
 *
 * @param ItemRequest $itemrequest
 * @return ItemEnumeration all found items
 */
function bigace_find_by_id($itemrequest)
{
	$sqlFile = 'item_find_by_id';
    if ($GLOBALS['_BIGACE']['SESSION']->isSuperUser()) {
    	$sqlFile = 'item_find_by_id_no_rights';
    }
        
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement($sqlFile);
    $values = array ( 'LANGUAGE'    => $itemrequest->getLanguageID(),
                          'LIMIT_START' => $itemrequest->getLimitFrom(),
                          'LIMIT_STOP'  => $itemrequest->getLimitTo(),
                          'USER'        => $GLOBALS['_BIGACE']['SESSION']->getUserID(),
                          'PERMISSION'  => _BIGACE_RIGHTS_NO,
        				  'ID'			=> $itemrequest->getID(),
                          'ITEMTYPE'    => $itemrequest->getItemType(),
        				  'ORDER' 		=> $itemrequest->getOrder() );
    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);

    return new ItemEnumeration($GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql), $itemrequest->getItemType());
}

/**
 * Fetches the most visited items.
 *
 * @param ItemRequest $itemrequest
 * @return ItemEnumeration all found items
 */
function bigace_most_visited($itemrequest) 
{
	$ext = '';
	if(!is_null($itemrequest->getID()))
		$ext = " AND a.parentid = " . $itemrequest->getID(); 
	$tables = "{DB_PREFIX}item_{ITEMTYPE} a";
    if (!$GLOBALS['_BIGACE']['SESSION']->isSuperUser()) {
    	$ext .= " AND b.itemtype='{ITEMTYPE}' AND b.cid='{CID}' AND b.itemid=a.id
				AND (c.cid='{CID}' AND c.userid='{USER}' AND c.group_id = b.group_id AND b.value > '{PERMISSION}') "; 
    	$tables .= ", {DB_PREFIX}group_right b, {DB_PREFIX}user_group_mapping c";
    }
	
	$sqlString = "SELECT a.* FROM ".$tables." WHERE a.cid='{CID}' ".$ext."  
			ORDER BY a.viewed DESC
			LIMIT {LIMIT_START}, {LIMIT_STOP}";
        
    $values = array ( 'LANGUAGE'    => $itemrequest->getLanguageID(),
                      'LIMIT_START' => $itemrequest->getLimitFrom(),
                      'LIMIT_STOP'  => $itemrequest->getLimitTo(),
                      'USER'        => $GLOBALS['_BIGACE']['SESSION']->getUserID(),
                      'PERMISSION'  => _BIGACE_RIGHTS_NO,
                      'ITEMTYPE'    => $itemrequest->getItemType() );
    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);

    return new ItemEnumeration($GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql), $itemrequest->getItemType());
}

/**
 * Count the amount of items.
 *
 * @param ItemRequest $itemrequest
 * @return int the amount of items
 */
function bigace_count_items($itemrequest) {
	$ext = '';
    $extJoin = '';

	if(!is_null($itemrequest->getID()))
		$ext = " AND a.parentid = '{PARENT}'"; 
	if(!is_null($itemrequest->getLanguageID()))
		$ext .= " AND a.language = '{LANGUAGE}'"; 

    if (!$GLOBALS['_BIGACE']['SESSION']->isSuperUser()) {
        $extJoin = " RIGHT JOIN {DB_PREFIX}group_right b ON 
            b.itemid = a.id AND b.itemtype = '{ITEMTYPE}' AND b.cid = '{CID}' AND b.value > '{PERMISSION}'
            RIGHT JOIN {DB_PREFIX}user_group_mapping c ON 
            c.group_id = b.group_id AND c.cid = '{CID}' AND c.userid = '{USER}' ";
    }

	$sqlString = "SELECT count(a.id)
                    FROM {DB_PREFIX}item_{ITEMTYPE} a
                    ".$extJoin."
                    WHERE a.cid = '{CID}' " . $ext;
	        
    $values = array ( 'LANGUAGE'    => $itemrequest->getLanguageID(),
                      'PARENT'      => $itemrequest->getID(),
                      'USER'        => $GLOBALS['_BIGACE']['SESSION']->getUserID(),
                      'PERMISSION'  => _BIGACE_RIGHTS_NO,
                      'ITEMTYPE'    => $itemrequest->getItemType() );
    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, false);
    $t = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
    $t = $t->next();
    return $t[0];
}
