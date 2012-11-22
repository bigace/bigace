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

/**
 * The SimpleItemTreeWalker takes a ItemRequest as constructor parameter
 * and then serves as enumeration above the returned items.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage item
 */
class SimpleItemTreeWalker
{
	// FIXME: WILL RESULT IN AN ENDLESS LOOP IF TOP_LEVEL IS NOT THERE FOR THE REQUESTED LANGUAGE!
	
    private $items;
    private $request;
    private $returnType;
    private $itemType;
    
    /**
     * Get all children of the given Item.
     *
     * @param ItemRequest the ItemRequest defining the Items to fetch
     */
    function SimpleItemTreeWalker($treeRequest)
    {
    	$this->setRequest($treeRequest);
    }

    /**
     * @return ItemRequest the ItemRequest to fetch
     * @access private
     */
    function setRequest($treeRequest) {
    	if (is_object($treeRequest) && strcasecmp(get_class($treeRequest),'ItemRequest') == 0) {
    		$this->request = $treeRequest;
    		$this->init();
    	} else {
    		$GLOBALS['LOGGER']->logError('SimpleItemTreeWalker - Got $treeRequest of wrong Object type!');
    		$this->items = array();
    	}
    }
        
    /**
     * @return ItemRequest the ItemRequest to fetch
     * @access private
     */
    function getRequest() {
    	return $this->request;
    }
    
    /**
     * This prepares and performs the SQL select.
     * 
     * @access private
     */
    function init()
    {
    	$req = $this->getRequest();

        // Vars to be used
        $orderBy = $req->getOrderBy();
        $order = $req->getOrder();
        $itemid = $req->getID();
        $languageID = $req->getLanguageID();
        $treeType = $req->getTreeType();
        $itemtype = $req->getItemType();
        $limitFrom = $req->getLimitFrom();
        $limitTo = $req->getLimitTo();
        $categories = $req->getCategories();
        
        $excludeFlags = $req->getExcludeFlags();
        if (!is_array($excludeFlags)) $excludeFlags = array($excludeFlags);
        
		// initialize this itemtype representation properly
        $itt = new Itemtype($itemtype);
        $this->itemType = $itemtype;

		if($req->getReturnType() == null) {
			$this->returnType = $itt->getClassName();
		} else {
			$this->returnType = $req->getReturnType();
		}
        
        if ($orderBy != '') {
            if(strrpos($orderBy,'(') === false) // if this is a column from the item table
                $order = " ORDER BY a.".$orderBy." ".$order;
            else // otherwise this is a function like "order by rand()" its table independent
                $order = " ORDER BY ".$orderBy." ".$order;
        }

        $extension = '';

        if (!is_null($itemid) && $itemid != '') {
            $extension .= " AND a.parentid='".$itemid."' ";     
        } 
        else {
            // if no parent was selected, we fetch ALL items, BUT:
            // we do ONLY fetch the toplevel for menus
            if($itemtype != _BIGACE_ITEM_MENU)
                $extension .= " AND a.parentid != '"._BIGACE_TOP_PARENT."' ";     
        }
                
        if (!is_null($languageID) && $languageID != '') {
            $extension .= " AND a.language='".$languageID."' ";     
        }

        if(is_array($excludeFlags) && count($excludeFlags) > 0) {
            $extension .= " AND a.num_3 NOT IN (";
            for($i=0; $i < count($excludeFlags); $i++) {
                $extension .= " ".$excludeFlags[$i]." ";
                if($i < count($excludeFlags)-1)
                	$extension .= ","; 
            }
            $extension .= ") ";
        }
        
        $limit = '';
        if (((int)$limitTo) > 0 && ((int)$limitFrom) >= 0) {
        	$limit = " LIMIT ".$limitFrom."," . $limitTo;
        }
        $tempUser = $GLOBALS['_BIGACE']['SESSION']->getUser();
        
        $joinExtension = '';
        if($categories != null && count($categories) > 0)
        {
        	$joinExtension = " INNER JOIN {DB_PREFIX}item_category ic ON ic.cid=a.cid AND 
        						ic.itemid=a.id AND ic.itemtype='".$itemtype."' AND ic.categoryid IN (";
            for($i=0; $i < count($categories); $i++) {
        		$joinExtension .= " " . $categories[$i] . " ";
                if($i < count($categories)-1)
                	$joinExtension .= ","; 
        	}
            $joinExtension .= ") "; 
        }
                
	    $values = array( 'ITEMTYPE'    		=> $itemtype,
                         'USER'        		=> $tempUser->getID(),
                         'RIGHT_VALUE' 		=> _BIGACE_RIGHTS_NO,
	                     'ORDER_BY'    		=> $order,
	                     'COLUMNS'     		=> MasterItemType::getSelectColumns($itemtype,$treeType), 
	                     'LANGUAGE'    		=> $languageID,
	                     'WHERE_EXTENSION'  => $extension,
	                     'JOIN_EXTENSION'   => $joinExtension,
	    				 'LIMIT'	   		=> $limit );
	    
        $sqlFile = 'item_tree_walker';
        if ($tempUser->isSuperUser()) {
            $sqlFile = 'item_tree_walker_no_rights';
        }
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement($sqlFile);
	    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values);

	    $this->items = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
        unset($tempUser);
		unset($sql);
    }

    /**
     * Count the amount of all fetched Items.
     * @return int the amount of Items
     */
    function count() {
        return $this->items->count();
    }

    /**
     * Get the next Item.
     * 
     * @return Item the next received Item
     */
    function next() 
    { 	
    	$temp = $this->items->next();
    	if($temp === FALSE)
    		return FALSE;
    		
    	$myObject = new $this->returnType();
    	$myObject->_setItemValues($temp);
	    $myObject->initItemtype($this->itemType);
	    
        return $myObject;
    }

}
