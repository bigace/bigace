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
 * @subpackage right
 */

/**
 * The RightAdminService is used for creating and deleting Right entrys.
 * Initialize this Service with a Itemtype to work with.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage right
 */
class RightAdminService
{

    private $itemtype = null;

    /**
    * This initalizes the AdminService with the correct Itemtype.
    * @param int itemtype the Itemtype to handle the rights for
    */
    function RightAdminService($itemtype)
    {
        $this->itemtype = $itemtype;
    }

    /**
     * Delete all right entrys that belong to the given Item ID
     * @param int the Item ID
     * @return Object the DB Result
     */
    function deleteItemRights($itemid)
    {
        $values = array( 'ITEM_ID' => $itemid, 'ITEMTYPE'  => $this->itemtype );
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement("DELETE FROM {DB_PREFIX}group_right WHERE itemtype={ITEMTYPE} AND itemid={ITEM_ID} AND cid={CID}",$values,true);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
    }

    /**
     * Delete the special right entry that belong to the given Group and Item.
     *
     * @param int the Group ID
     * @param int the Item ID
     * @return Object the DB Result
     */
    function deleteGroupRight($group_id, $itemid)
    {
       $values = array( 'GROUP_ID'  => $group_id,
                        'ITEM_ID'   => $itemid,
                        'ITEMTYPE'  => $this->itemtype );
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->loadAndPrepareStatement('right_delete_group_right',$values);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
    }

    /**
     * Checks if a Right exists for the given Group and Item ID.
     *
     * @param int the Group ID to check
     * @param int the ItemID to check
     * @return boolean whether a Right exists or not
     */
    function checkForExistence($group, $itemid)
    {
       $values = array( 'GROUP_ID'  => $group,
                        'ITEM_ID'   => $itemid,
                        'ITEMTYPE'  => $this->itemtype );
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->loadAndPrepareStatement('right_select_existence',$values);
        $result = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
        return ($result->count() > 0);
    }

    function changeRight($group_id, $itemid, $value)
    {
       $values = array( 'GROUP_ID'    => $group_id,
                        'ITEM_ID'     => $itemid,
                        'ITEMTYPE'    => $this->itemtype,
                        'RIGHT_VALUE' => $value );
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->loadAndPrepareStatement('right_change_group_right',$values);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
    }

    /**
     * Creates a copy for all permission entries by selecting them from the parent
     * and applying them to the child.
     *
     * The $parent doesn't need to be the real item parent of $child, it can also
     * be of another $itemtype.
     *
     * @param int parent the Item ID of the Item to creates a righty copy from
     * @param int child the ItemID of the Item to create the right entrys for
     * @param int itemtype the itemtype to select existing permission from
     */
    function createRightCopy($parent, $child, $itemtype = null)
    {
        if($itemtype === null) {
            $itemtype = $this->itemtype;
        }
        $values = array( 'ITEM_ID'  => $parent,
                         'ITEMTYPE' => $itemtype );
        $sql = "SELECT * FROM {DB_PREFIX}group_right WHERE itemtype={ITEMTYPE} AND itemid={ITEM_ID} AND cid={CID}";
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);

        for ($i=0; $i < $res->count(); $i++)
        {
            $temp = $res->next();
            $this->createGroupRight($temp['group_id'], $child, $temp['value']);
        }
    }

    /**
     * Creates a Right entry for the given Group.
     * If there is already an enry for this group existing, this one will be changed.
     */
    function createGroupRight($group_id, $itemid, $value)
    {
        if ($this->checkForExistence($group_id, $itemid)){
            $this->changeRight($group_id, $itemid, $value);
        } else {
            $values = array( 'GROUP_ID'    => $group_id,
                             'ITEM_ID'     => $itemid,
                             'ITEMTYPE'    => $this->itemtype,
                             'RIGHT_VALUE' => $value );
            $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->loadAndPrepareStatement('right_create_group_right',$values);
            $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sql);
        }
    }

    /**
     * Delete all permission of the given Group in ALL Itemtypes!
     *
     * @param $groupID the GroupID to delete permissions for
     */
    function deleteAllGroupRight($groupID)
    {
        $values = array( 'GROUP_ID'    => $groupID );
        $sql = "DELETE FROM {DB_PREFIX}group_right WHERE group_id={GROUP_ID} AND cid={CID}";
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
    }

}