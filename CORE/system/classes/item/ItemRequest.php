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
 * The ItemRequest is a value Container, that is be used for 
 * defining a Request against the CMS ItemService.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage item
 */
class ItemRequest
{
	var $_ORDER_ASC = "ASC"; 
	var $_ORDER_DESC = "DESC"; 
	
    var $FLAG_ALL = array(); 
    var $FLAG_ALL_EXCEPT_TRASH = array(FLAG_TRASH); 
    var $FLAG_ALL_EXCEPT_TRASH_AND_HIDDEN = array(FLAG_TRASH,FLAG_HIDDEN); 

	/**
	 * @access private
	 */
	private $id = null;
	/**
	 * @access private
	 */
	private $langid;
	/**
	 * @access private
	 */
	private $treetype = ITEM_LOAD_FULL;
	/**
	 * @access private
	 */
	private $itemtype;
	/**
	 * @access private
	 */
	private $orderby = ORDER_COLUMN_POSITION;
	/**
	 * @access private
	 */
	private $orderDirection = "ASC";
	/**
	 * @access private
	 */
	private $limitFrom = 0;
	/**
	 * @access private
	 */
	private $limitTo = 0;
    /**
     * @access private
     */
    private $flagExclude = array(FLAG_TRASH,FLAG_HIDDEN);
    /**
     * @access private
     */
    private $returnType = null;
    /**
     * @access private
     */
    private $categories = array();
    
	/**
	 * Create a new ItemRequest for the given Itemtype and ItemID.
	 * @param int the Itemtype
	 * @param int the Item ID
	 */
	function ItemRequest($itemtype, $itemID = null) 
	{
		$this->setItemType($itemtype);
		if(!is_null($itemID))
			$this->setID($itemID);
	}
	
	/**
	 * Set the ItemID to fetch.
	 * @param int the Item ID
	 */
	function setID($id) {
		$this->id = $id;
	}
    
    /**
     * Set an array of flags or a single flag to exclude.
     * 
     * Accepts:
     * - FLAG_ALL
     * - ALL_EXCEPT_TRASH
     * - ALL_EXCEPT_TRASH_AND_HIDDEN (default)
     * 
     * @param array flags an array of int to exclude
     */
    function setFlagToExclude($flags)
    {
        if(is_array($flags)) {
            $this->flagExclude = $flags;
        } else {
        	$this->flagExclude[] = $flags;
        }
    }

    /**
     * Sets the field, the results will be ordered by.
     *
     * @param string the field name
     */
	function setOrderBy($order) {
		$this->orderby = $order;
	}
	
	/**
	 * If you want to fetch Items for one or more Categories, add their IDs.
	 * Call this method oce for each Category.
	 *
	 * @param int a Category ID
	 */
	function setCategory($id) {
		$this->categories[] = $id;
	}
	
	function getCategories() {
		return $this->categories;
	}

	/**
	 * Sets the ClassName that should be used when returning the entries.
	 * Remember to import the Class before fetching the Results!
	 *
	 * @param String $classname the Classname to return
	 */
	function setReturnType($classname) {
		$this->returnType = $classname;
	}

	function setLimit($from, $to) {
		$this->limitFrom = $from;
		$this->limitTo = $to;
	}

	function setOrder($direction) {
		if ($direction == $this->_ORDER_ASC || $direction == $this->_ORDER_DESC || $direction == '')
			$this->orderDirection = $direction;
	}

	/**
	 * Set the Language ID we are going to fetch.
	 * @param String the Language ID
	 */
	function setLanguageID($languageID) {
		$this->langid = $languageID;
	}
	
	function setTreetype($type) {
		$this->treetype = $type;
	}
	
	function setItemType($itemtype) {
		$this->itemtype = $itemtype;
	}

	function getLimitFrom() {
		return $this->limitFrom;
	}

	function getLimitTo() {
		return $this->limitTo;
	}

	function getID() {
		return $this->id;
	}
    
    function getExcludeFlags() {
        return $this->flagExclude;
    }
    
	function getOrderBy() {
		return $this->orderby;
	}

	function getOrder() {
		return $this->orderDirection;
	}

	function getLanguageID() {
		return $this->langid;
	}
	
	function getReturnType() {
		return $this->returnType;
	}
	
	function getTreetype() {
		return $this->treetype;
	}
	
	function getItemType() {
		return $this->itemtype;
	}	
}

?>