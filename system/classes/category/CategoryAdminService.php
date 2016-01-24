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
 
import('classes.util.IdentifierHelper');
 
/**
 * The CategoryAdminService provides all kind of writing services for
 * Categorys inside BIGACE.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage category
 */
class CategoryAdminService 
{

    /**
    * Change an existing Category.
    *
    * @param    int     the Category ID that represents the Category to change
    * @param    String  the new name
    * @param    String  the new url
    * @param    String  the new mimetype 
    * @return   Object  the DB result
    */
    function changeCategory($id, $parentid, $position, $name, $description)
    {
	    $values = array( 'NAME'         => $name,
	                     'PARENT_ID'    => $parentid,
	                     'POSITION'     => $position,
	                     'DESCRIPTION'  => $description,
	                     'ID'           => $id );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('admin_change_category');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
	    return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }


    /**
    * Deletes an Category from BIGACE
    *
    * @param    int the Category ID
    */
    function deleteCategory($id)
    {
        $this->deleteAllLinksForCategory($id);
        
	    $values = array( 'ID' => $id );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('admin_delete_category');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
	    return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
    * Deletes all links for a Category.
    *
    * @param    int the Category ID
    */
    function deleteAllLinksForCategory($id) 
    {
  	    $values = array( 'ID' => $id );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('admin_delete_links_for_category', true);
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
	    return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
     * Provide an array with these values:
     * array('name' => 'foo', 'description' => 'bar', 'parentid' => _BIGACE_TOPLEVEL).
     */
    function createCategory($data) 
    {
        if ( isset($data['parentid']) && isset($data['name']) ) 
        {
            $pid = $this->_getMaxPositionFor($data['parentid']) + 1;
            $mid = $this->_getMaxID() + 1;

            $values = array( 'CATEGORY_ID'  => $mid,
    	                     'POSITION'     => $pid,
    	                     'NAME'         => $data['name'],
    	                     'DESCRIPTION'  => (isset($data['description']) ? $data['description'] : ''),
    	                     'PARENT_ID'    => $data['parentid'] );
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('category_create');
    	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
    	    $res = $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sqlString);
            return $mid;
        } 
        return false;
    }
    
    function createCategoryLink($itemtype, $itemid, $categoryid) 
    {
	    $values = array( 'ITEM_ID'      => $itemid,
	                     'ITEMTYPE'     => $itemtype,
	                     'CATEGORY_ID'  => $categoryid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('category_create_link', true);
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sqlString);
    }

    function deleteCategoryLink($itemtype, $itemid, $categoryid) 
    {
	    $values = array( 'ITEM_ID'      => $itemid,
	                     'ITEMTYPE'     => $itemtype,
	                     'CATEGORY_ID'  => $categoryid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('category_delete_link', true);
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
     * @access private
     */
    function _getMaxPositionFor($parentid)
    {
	    $values = array( 'CATEGORY_ID' => $parentid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('category_select_max_position');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $pid = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $pid = $pid->next();
        return $pid[0];        
    }
    
    /**
     * @access private
     */
    function _getMaxID()
    {
        return IdentifierHelper::getMaximumID( 'category' );
    }

}

?>