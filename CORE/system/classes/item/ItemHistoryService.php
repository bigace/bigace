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

import('classes.item.Itemtype');
import('classes.item.ItemHistoryEnumeration');

/**
 * Holds methods for receiving History Versions of Items of the initialized Itemtype.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage item
 */
class ItemHistoryService extends Itemtype
{
    /**
    * Initalizes a new ItemHistoryService for a special Itemtype
    */
    function ItemHistoryService($itemtype = '')
    {
        $this->initItemHistoryService($itemtype);
    }

    function initItemHistoryService($itemtype)
    {
        $this->initItemtype($itemtype);
    }
    
    function getHistoryVersions($itemid, $languageid)
    {
        $values = array ( 'CID'             => _CID_, 
                          'ITEM_ID'         => $itemid,
                          'ITEMTYPE'        => $this->getItemtypeID(),
                          'LANGUAGE_ID'     => $languageid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_history_select_all');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logSQL('Select History Versions with Statement: ' . $sqlString);
        $historyEnum = new ItemHistoryEnumeration($GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString));
        $historyEnum->initItemtype($this->getItemtypeID());
        return $historyEnum;
    }

    function getHistoryVersion($itemid, $languageid, $modifiedDate)
    {
        $values = array ( 'CID'             => _CID_, 
                          'ITEM_ID'         => $itemid,
                          'ITEMTYPE'        => $this->getItemtypeID(),  
                          'LANGUAGE_ID'     => $languageid,
                          'MODIFIED_DATE'   => $modifiedDate );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_history_select');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logSQL('Select History Version with Statement: ' . $sqlString);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $hi = new HistoryItem($res->next());
        $hi->initItemtype($this->getItemtypeID());
        return $hi;
    }

    
    /**
     * Count all history Versions of this Item.
     * @return int the amount of History Versions for this Item
     */
    function countHistoryVersions($id, $languageid)
    {
	    $values = array( 'ITEMTYPE'    => $this->getItemtypeID(),
	                     'ITEM_ID'     => $id,
	                     'LANGUAGE_ID' => $languageid,
	                     'CID'         => _CID_ );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_history_count_versions');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
	    $version = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	    $version = $version->next();
	            
        return $version[0];
    }

}

?>