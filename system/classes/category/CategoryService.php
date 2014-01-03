<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.<br>Copyright (C) Kevin Papst.
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

loadClass('category', 'Category');
loadClass('category', 'CategoryTreeWalker');
loadClass('category', 'CategoryItemEnumeration');
loadClass('category', 'ItemCategoryEnumeration');

/**
 * The CategoryService serves all kinds of Category Objects.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage category
 */
class CategoryService
{

    /**
     * Instantiates a new CategoryService.
     * This is used for receiving any kind of Category Object.
     */
    function CategoryService() {
    }

    /**
     * Gets the Category Top Level Item.
     * @return Category the Top Level Category
     */
    function getTopLevel() {
        return new Category( _BIGACE_TOP_LEVEL );
    }

    /**
     * Fetch the Category Object with the given ID.
     *
     * @param int id the Category ID
     * @return Category
     */
    function getCategory($id)
    {
        return new Category($id);
    }

    /**
     * Returns a new CategoryTreeWalker to get information about Categorys, in their tree-order
     * @return CategoryTreeWalker the CategoryTreeWalker
     */
    function getCategoryEnumeration()
    {
        return new CategoryTreeWalker( _BIGACE_TOP_LEVEL );
    }

    /**
     * Returns an Enumeration above all Items (for the given Itemtype),
     * that are linked to a Category.
     * @return CategoryItemEnumeration list of Items
     */
    function getItemsForCategory($itemtype_id, $categoryid)
    {
        return new CategoryItemEnumeration($itemtype_id, $categoryid);
    }

    /**
     * Get a list of all Categorys that are linked to the given Item.
     * @return ItemCategoryEnumeration list of Categorys
     */
    function getCategorysForItem($itemtype_id, $item_id)
    {
        return new ItemCategoryEnumeration($itemtype_id, $item_id);
    }

    /**
     * THE IMPLEMENTATION MAY CHANGE, BE CAREFUL WHEN USING!
     * Fetches all Items that are linked to a Category, Itemtype independ.
     *
     * Returns an Result array.
     *
     * The Array has the usable Indices:
     * - itemid
     * - itemtype
     *
     * @return array see list above for array indices
     */
    function getAllItemsForCategory($categoryid)
    {
	    $values = array( 'CATEGORY_ID' => $categoryid,
	                     'CID'         => _CID_ );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('category_select_linked_items');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
     * Count the lins for a Category
     * @return int the amount of linked items for the Category
     */
    function countLinksForCategory($categoryid)
    {
        $temp = $this->getAllItemsForCategory($categoryid);
        return $temp->count();
    }

    /**
     * Returns whether an Item id lnked to a known Category.
     * @return boolean if the item is linked or not
     */
    function isItemLinkedToCategory($itemtype_id, $item_id, $category_id)
    {
	    $values = array( 'CATEGORY_ID' => $category_id,
	                     'ITEM_ID'     => $item_id,
	                     'ITEMTYPE'    => $itemtype_id,
	                     'TABLE'       => 'item_category',
	                     'CID'         => _CID_ );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('category_select_link');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $bla = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

		return ($bla->count() > 0);
    }

}