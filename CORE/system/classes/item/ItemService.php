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
import('classes.item.Item');
import('classes.item.ItemTreeWalker');

/**
 * Holds methods for receiving Items of the initialized Itemtype.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage item
 */
class ItemService extends Itemtype
{
    /**
     * Initalizes a new ItemService for the given Itemtype.
     */
    function ItemService($itemtype = '') {
        $this->initItemService($itemtype);
    }

    function initItemService($itemtype) {
        $this->initItemtype($itemtype);
    }
    
    function getItemtype() {
        return $this->getItemtypeID();
    }

    function getItem($item_id, $treetype = ITEM_LOAD_FULL, $languageID = '') {
        return new Item( $this->getItemtypeID(), $item_id, $treetype, $languageID );
    }

    function getTreeWalker($id, $orderby = ORDER_COLUMN_POSITION) {
        return $this->getTree($id, $orderby);
    }

    function getTree($id, $orderby = ORDER_COLUMN_POSITION) {
        return new ItemTreeWalker($this->getItemtypeID(), $id, $orderby, ITEM_LOAD_FULL, '');
    }

    function getLightTree($id, $orderby = ORDER_COLUMN_POSITION) {
        return new ItemTreeWalker($this->getItemtypeID(), $id, $orderby, ITEM_LOAD_LIGHT, '');
    }

    function getTreeForLanguage($id, $languageID, $orderby = ORDER_COLUMN_POSITION) {
        return new ItemTreeWalker($this->getItemtypeID(), $id, $orderby, ITEM_LOAD_FULL, $languageID);
    }

    function getLightTreeForLanguage($id, $languageID, $orderby = ORDER_COLUMN_POSITION) {
        return new ItemTreeWalker($this->getItemtypeID(), $id, $orderby, ITEM_LOAD_LIGHT, $languageID);
    }
        
    function getItemLanguageEnumeration($item_id) {
        import('classes.language.ItemLanguageEnumeration');
        return new ItemLanguageEnumeration($this->getItemtypeID(), $item_id);
    }
    
    /**
     * Calculates the Level beneath TOP LEVEL.
     */
    function countLevel($item_id) {
        $temp = $this->getWayHome($item_id, false);
        return count($temp);
    }
    
    /**
     * @param int the Item ID to start from
     * @param boolean whether to include the Start ID or not
     * @return array an Array with Item ID for the Way Home
     */
    function getWayHome($item_id, $include) {
        $level = array();
        if ($include) {
            array_push($level, $item_id);
        }
        $parent = $item_id;
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_count_level');
        while ($parent != _BIGACE_TOP_LEVEL) 
        {
            $values = array (   'ITEMTYPE'  => $this->getItemtypeID(), 
                                'CID'       => _CID_, 
                                'ITEM_ID'   => $parent);
            $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
            $parentid   = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
            $temp       = $parentid->next();
            $parent     = $temp[0];
            array_push($level, $parent);
        }
        return $level;
    }
    
    /**
     * Get the last edited Items for the specified Language. 
     * You may define the amount of items by setting the Start and Stop values.
     * @param String language_id the Language ID
     * @deprecated use the functions from classes.item.ItemRequests instead
     */
    function getLastEditedItems($language_id, $start = '0', $stop = '5')
    {
    	$GLOBALS['LOGGER']->logInfo('using deprecated call ItemService::getLastEditedItems');
    	import('classes.item.ItemRequests');
    	import('classes.item.ItemRequest');
    	$ir = new ItemRequest($this->getItemtypeID());
    	$ir->setLanguageID($language_id);
    	$ir->setLimit($start, $stop);
    	return bigace_last_edited_items($ir);
    }

    /**
     * Counts all Items, except TOP_LEVEL.
     * This does only count one version of each item. 
     * If a Item has several language Versions, only one of them is counted.
     * @return int the amount of all Items
     */
    function countAllItems() 
    {
        $sqlFile = 'item_count_all';
        if ($GLOBALS['USER']->isSuperUser()) {
            $sqlFile = 'item_count_all_no_rights';
        }
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement($sqlFile);
        $values = array ( 'ITEMTYPE'     => $this->getItemtypeID(), 
                          'TOP_LEVEL_ID' => _BIGACE_TOP_LEVEL,
                          'USER'         => $GLOBALS['_BIGACE']['SESSION']->getUserID(),
                          'RIGHT_VALUE'  => _BIGACE_RIGHTS_NO );
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $cnt = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
        $cnt = $cnt->next();
        return $cnt['counter'];
    }
    
    /**
     * Checks whether the given Item is a Leaf.
     * The check will be performed above all languages, so if the Item exists 
     * ONLY in German, but the Childs are all in English, it returns FALSE, 
     * even if you do not see children within a Navigation (for example).
     * @return boolean TRUE if the Item has childs in any language, FALSE otherwise 
     */
    function isLeaf($itemID) 
    {
        $sqlFile = 'item_is_leaf';
        if ($GLOBALS['USER']->isSuperUser()) {
            $sqlFile = 'item_is_leaf_no_rights';
        }
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement($sqlFile);
        $values = array ( 'ITEMTYPE'    => $this->getItemtypeID(), 
                          'PARENT_ID'   => $itemID,
                          'USER'        => $GLOBALS['_BIGACE']['SESSION']->getUserID(),
                          'RIGHT_VALUE' => _BIGACE_RIGHTS_NO );
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
        return ($res->count() == 0);
        //$res = $res->next();
        //return ($res[0] == 0);
    }
    
    /**
     * Returns true if "child" is somewhere beneath "parent" in the Menu Tree.
     * This method is time consuming!
     * @return boolean whether child_id is a child of parent_id 
     */
    function isChildOf($parent_id, $child_id)
    {
        if ($child_id == _BIGACE_TOP_LEVEL) {
            return false;
        }
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->loadAndPrepareStatement('menu_is_child_of', array('ITEM_ID' => $child_id, 'CID' => _CID_));
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($res);

        $child = $res->next();
        if ($child['parentid'] == $parent_id) {
            return true;
        }
        else {
           if (($child['parentid'] == _BIGACE_TOP_PARENT) || ($child['id'] == _BIGACE_TOP_LEVEL)) {
                return false;
           }
           else {
                return $this->isChildOf($parent_id, $child['parentid']);
           }
        }
    }

    function increaseViewCounter($itemid,$language) {
    	$values = array('ID' => $itemid, 'LANGUAGE' => $language);
    	$sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement("UPDATE {DB_PREFIX}item_".$this->getItemtypeID()." SET viewed = viewed + 1 WHERE id = {ID} AND language = {LANGUAGE} AND cid = {CID}", $values, true);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
    }
}

?>