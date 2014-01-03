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
 * @subpackage right
 */

/**
 * Represents a Right for one Item and Group.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage right
 */
class GroupRight
{
	/**
	 * @access private
	 */
    var $right;

    /**
     * Load a Item right for a Group.
     *
     * @param    int     the Itemtype ID
     * @param    int     the Group ID
     * @param    int     the Item ID
     */
    function GroupRight($itemtype, $group_id, $itemid)
    {
        $this->item_id = $itemid;

        $values = array( 'GROUP_ID'  => $group_id,
                         'ITEM_ID'   => $itemid,
                         'ITEMTYPE'  => $itemtype );
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->loadAndPrepareStatement('right_select_group',$values);
        $right = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);

        if (!$right) {
            $right = array ("value" => _BIGACE_RIGHTS_NO);
        } 
        $temp = $right->next();

        $this->right = $temp;
    }


    /**
     * Gets the Item ID this Right represents.
     * @return int the Item ID
     */
    function getItemID() {
        return $this->right["itemid"];
    }

    /**
     * Gets the Group ID this Right represents.
     * @return int the Group ID
     */
    function getGroupID() {
        return $this->right["group_id"];
    }

    /**
     * Checks if the Group can read the given Item.
     * @return boolean if or if not group is allowed to read the item
     */
    function canRead() {
        return $this->_checkIsTrue(_BIGACE_RIGHTS_READ);
    }


    /**
     * Checks if the Group can write the given Item.
     * @return boolean if or if not group is allowed to write the item
     */
    function canWrite() {
        return $this->_checkIsTrue(_BIGACE_RIGHTS_WRITE);
    }


    /**
     * Checks if the Group can delete the given Item.
     * @return boolean if or if not group is allowed to delete the item
     */
    function canDelete() {
        return $this->_checkIsTrue(_BIGACE_RIGHTS_DELETE);
    }

    /**
     * This checks if the given Right is true. 
     * @access private
     */
    function _checkIsTrue($right_to_check)
    {       	
    	// if current user is suoer user, he is allowed to do everything
    	// FIXME this may bring out totally wrong values
        if ($GLOBALS['_BIGACE']['SESSION']->getUserID() == _BIGACE_SUPER_ADMIN) {
            return true;
        }

        return ($this->right['value'] >= $right_to_check);
    }

    /**
     * Get the right value.
     */
    function getValue() {
        return $this->right['value'];
    }

}

?>