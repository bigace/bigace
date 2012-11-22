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
 * This checks the possible Rights of the given User.
 * If checks all Group Memberships and their rights.
 *
 * If you want to know if a special group has own rights on a special item use <code>GroupRight</code>.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage right
 */
class Right
{
    /**
     * @access private
     */
    var $right;
    /**
     * @access private
     */
    var $user_id;
    /**
     * @access private
     */
    var $item_id;

    /**
     * One instance is used for the given User and Item!
     *
     * @param int itemtype the Itemtype to check the right for
     * @param int userid the User ID to work with
     * @param int the Item ID to work with
     */
    function Right($itemtype, $userid, $itemid)
    {
        $this->user_id = $userid;
        $this->item_id = $itemid;
        $this->right = $this->loadRight($itemtype, $userid, $itemid);
    }

    /**
     * Loads the rights from the Database.
     * @param int itemtype the Itemtype to check the right for
     * @param int userid the User ID to work with
     * @param int the Item ID to work with
     * @return array the rights representing the given user and Item id
     * @access   private 
     */
    function loadRight($itemtype, $user, $itemid)
    {
        $values = array( 'USER_ID'   => $user,
                         'ITEM_ID'   => $itemid,
                         'ITEMTYPE'  => $itemtype );
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->loadAndPrepareStatement('right_select',$values);
        $right = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);

        $tempright = array();
        if (!$right->isError()) 
        {
            for($i=0; $i < $right->count(); $i++) 
            {
                $temp = $right->next();
                foreach($temp AS $k => $v) {
                    $tempright[$i][$k] = $v;
                }
            }
        }
        return $tempright;
    }

    /**
     * Gets the Item ID this Right represents.
     * @return   int     the Item ID
     * @access private
     */
    function getItemID()
    {
        return $this->item_id;
    }

    /**
     * Checks if the User is allowed/has the rights to read the given Page.
     * @return   boolean     if or if not user is allowed to read the page
     */
    function canRead() {
        return $this->checkIsTrue(_BIGACE_RIGHTS_READ);
    }

    /**
     * Checks if the User is allowed/has the rights to write the given Page 
     * @return boolean if or if not user is allowed to write the page
     */
    function canWrite() {
        return $this->checkIsTrue(_BIGACE_RIGHTS_WRITE);
    }

    /**
     * Checks if the User is allowed/has the rights to delete the given Page 
     * @return boolean if or if not user is allowed to delete the page
     */
    function canDelete()
    {
        return $this->checkIsTrue(_BIGACE_RIGHTS_DELETE);
    }

    /**
     * This checks if the given Right is true. If not and the Right is the NoneAnonymousRight it will
     * return the equal Anonymous Right. 
     *
     * @param    String  the database right name 
     * @return   boolean If or if not ;-)
     * @see      isTrue()
     * @access   private
     */
    function checkIsTrue($value)
    {   
        if ($this->user_id == _BIGACE_SUPER_ADMIN) {
            return true;
        }

        for ($i=0; $i < count($this->right); $i++) 
        {
            if ($this->right[$i]['value'] >= $value) {
                return TRUE;
            }
        }
        return FALSE;        
    }

    /**
     * Returns the Right value for this request.
     * 
     * Valid values are:
     * - _BIGACE_RIGHTS_NO
     * - _BIGACE_RIGHTS_READ
     * - _BIGACE_RIGHTS_WRITE
     * - _BIGACE_RIGHTS_DELETE
     */
    function getValue()
    {
        if ($this->canDelete()) {
            return _BIGACE_RIGHTS_DELETE;
        }
        if ($this->canWrite()) {
            return _BIGACE_RIGHTS_WRITE;
        }
        if ($this->canRead()) {
            return _BIGACE_RIGHTS_READ;
        }
        return _BIGACE_RIGHTS_NO;
    }

}

?>