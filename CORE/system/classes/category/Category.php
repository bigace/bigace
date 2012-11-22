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
 
import('classes.category.CategoryTreeWalker');

/**
 * A Category within BIGACE.
 * Categories are used to be linked to Items to build any kind of meta-structure.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage category
 */
class Category
{
	/**
	 * @access private
	 */
    var $category;

    /**
    * This intializes the Object with the given Category ID
    *
    * @param    int the Category ID
    */
    function Category($id)
    {
	    $values = array( 'CATEGORY_ID' => $id,
	                     'CID'         => _CID_ );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('category_select');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $this->category = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $this->_setValues($this->category->next());
    }
    
    /**
     * @access private
     */
    function _setValues($values) {
        $this->category = $values;
    }


    /**
     * Gets the ID of the current Category
     * @return int the ID
     */
    function getID() {
        return $this->category["id"];
    }

	/**
	 * Returns the Name of the Category.
	 * @return String the Category Name
	 */
    function getName() {
        return $this->category["name"];
    }


    function getDescription() {
        return $this->category["description"];
    }

    function getParentID() {
        return $this->category["parentid"];
    }
	
	/**
	 * Returns the Parent Category or null if this is the TOP LEVEL Category.
	 * @return Category the Parent Category or null
	 */
    function getParent() {
    	if($this->getParentID() == _BIGACE_TOP_PARENT)
    		return null;
        return new Category($this->getParentID());
    }
	
	/**
	 * Returns the position of this Category.
	 * @access private
	 */
    function getPosition() {
        return $this->category["position"];
    }
    
    /**
     * Get the childs of this Category.
     * @return CategoryTreeWalker the children of this Category
     */
    function getChilds() {
        return new CategoryTreeWalker( $this->getID() );
    }
	
	/**
	 * Alias for getChilds().
	 * @deprecated use getChilds() instead
     * @return CategoryTreeWalker the children of this Category
	 */
    function getChildEnumeration() {
        return $this->getChilds();
    }

    /**
     * Counts the amount of children for this Category.
     * @return int the amount of children
     */
    function countChilds() {
	    $values = array( 'PARENT_ID'   => $this->getID(),
	                     'CID'         => _CID_ );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('category_count_childs');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $childs = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $temp = $childs->next();
        return $temp[0];
    }

    /**
     * Returns if this Category has Children.
     * @return boolean whether this Category has Children or not
     */
    function hasChilds() {
        if ($this->countChilds() > 0) {
            return true;
        } 
        return false;
    }
        
}

?>