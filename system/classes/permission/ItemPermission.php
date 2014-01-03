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
 * @subpackage permission
 */

/**
 * An instance represents access permission for a user and object.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage permission
 */
class ItemPermission
{
    private $perm;
    private $uid;

    /**
     * One instance is used for the given user and item.
     *
     * @param int the Itemtype to check the permission for
     * @param int the Item ID to work with
     * @param int uid the User ID to work with, if not passed the current users id is used
     */
    function ItemPermission($itemtype, $itemid, $uid = null)
    {
        if(is_null($uid))
            $uid = $GLOBALS['_BIGACE']['SESSION']->getUserID();

        $this->uid = $uid;

        $values = array( 'USER_ID'   => $this->uid,
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
        $this->perm = $tempright;
    }

    /**
     * Checks if the User is allowed/has the permission to read the given item.
     * @return boolean if or if not user is allowed to read the item
     */
    function canRead() {
        return $this->checkIsTrue(_BIGACE_RIGHTS_READ);
    }

    /**
     * Checks if the User is allowed/has the permission to write the given item. 
     * @return boolean if or if not user is allowed to write the item
     */
    function canWrite() {
        return $this->checkIsTrue(_BIGACE_RIGHTS_WRITE);
    }

    /**
     * Checks if the User is allowed/has the permission to delete the given item. 
     * @return boolean if or if not user is allowed to delete the item
     */
    function canDelete() {
        return $this->checkIsTrue(_BIGACE_RIGHTS_DELETE);
    }

    /**
     * Checks if the passed permission is set, where permission is one of "r" (read), "w" (write), "d" (delete).
     *
     * @param int the Itemtype to check the permission for
     * @param int the Item ID to work with
     * @param String permission the permission string to check
     * @return boolean whether the User has the permission or not
     */
    function can($permission = null) 
    {
        if(!is_null($permission))
        {
            switch(strtolower($permission)) {
                case 'r':
                    return $this->canRead();
                    break;
                case 'w':
                    return $this->canWrite();
                    break;
                case 'd':
                    return $this->canDelete();
                    break;
            }
        }
        return false;
    }

    /**
     * This checks if the given permission is true. 
     *
     * @param int the permission value to check 
     * @return boolean If or if not ;-)
     */
    private function checkIsTrue($value)
    {   
        if ($this->uid == _BIGACE_SUPER_ADMIN) {
            return true;
        }

        for ($i=0; $i < count($this->perm); $i++) 
        {
            if ($this->perm[$i]['value'] >= $value) {
                return true;
            }
        }
        return false;        
    }

    /**
     * Returns the permission value for this request.
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

