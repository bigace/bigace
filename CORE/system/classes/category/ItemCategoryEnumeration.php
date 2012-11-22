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
 * @subpackage category
 */

import('classes.category.DBCategory');

/**
 * The ItemCategoryEnumeration fetches all Categories for one Item.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage category
 */
class ItemCategoryEnumeration
{
	
	/**
	 * @access private
	 */
	var $categorys;
	/**
	 * @access private
	 */
	var $current_pos = 0;
	
	/**
	* Gets a Category SearchResult.
	*
	* @param int 	the Itemtype ID
	* @param int    the ItemID
	*/
	function ItemCategoryEnumeration($itemtypeid, $itemid)
	{
	    $values = array( 'ITEMTYPE_ID' => $itemtypeid,
	                     'ITEM_ID'     => $itemid,
	                     'CID'         => _CID_ );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_select_categorys');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $this->categorys = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	}
	
	/**
	 * Counts the amount of Categories that ar linked to the Item.
	 * @return int the amount of linked Categories
	 */
	function count() {
	    return $this->categorys->count();
	}

    /**
     * Gets the next Category.
     * @return Category the next Category
     */
	function next() {
	    $this->current_pos++;
		return new DBCategory( $this->categorys->next() );
	}
	
	/**
	 * Returns whether there is at least one more Category.
	 * @return boolean if there is at least one more Category 
	 */
	function hasNext() {
	    return ($this->current_pos < $this->count());
	}

}

?>