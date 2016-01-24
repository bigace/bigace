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
 * @subpackage group
 */

/**
 * The GroupAdminService is used for write access to Groups.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage group
 */
class GroupAdminService
{

    /**
     * Adds an User to a Group.
     */
    function addToGroup($groupid, $userid)
    {
        $values = array( 'GROUP_ID' => $groupid,
                         'USER_ID'  => $userid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('group_add_user');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logDebug('Adding User ('.$userid.') to Group ('.$groupid.')');
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
     * Removes an User from a Group.
     */
    function removeFromGroup($groupid, $userid)
    {
        $values = array( 'GROUP_ID' => $groupid,
                         'USER_ID'  => $userid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('group_remove_user');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logDebug('Removing User ('.$userid.') from Group ('.$groupid.')');
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }
    
    /**
     * Remove User from all groups currently mapped to.
     * This is needed for example when a User is deleted.
     * @param int the user that should be removed from all groups
     */
    function removeAllMemberships($userid) {
        $values = array( 'USER_ID'  => $userid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('group_remove_memberships');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logDebug('Removed all Memberships for User: '.$userid);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    	
    }

    /**
     * Deletes a UserGroup and nothing more!
     * @param int groupid ID of the Group to be deleted
     */
    function deleteGroup($groupid)
    {
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('group_delete');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array( 'GROUP_ID' => $groupid ));
        $GLOBALS['LOGGER']->logDebug('Deleting Group: ' . $groupid);
	    return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
     * Deletes all UserGroups.
     * This does not delete the User-Group mapping, make sure to fix this!!!!!!!
     */
    function deleteAllGroups()
    {
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('group_delete_all');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array());
	    return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
     * Creates a new UserGroup.
     * @param String name the Name of the new UserGroup
     * @return the new ID
     */
    function createGroup($name)
    {
        $id = $this->calculateNextID();
        $this->createGroupWithID($id, $name);
        return $id;
    }

    /**
     * Creates a new UserGroup with the given ID.
     * DO NOT USE THIS METHOD FOR SIMPLY CREATING A NEW GROUP!
     * @access protected
     * @return mixed the Database result
     */
    function createGroupWithID($id, $name)
    {
        $values = array( 'GROUP_ID'     => $id,
	                     'GROUP_NAME'   => $name );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('group_create');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
	    return $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sqlString);
    }

    private function calculateNextID()
    {
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('select_max_id_group');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array());
        $pid = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $pid = $pid->next();
        $mid = $pid['max'];

        if($mid < 100)
            $mid = 100;
        $mid = $mid + 10;

        return $mid;
    }

}
