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

import('classes.group.Group');

/**
 * The GroupService is used for receiving Group dependent information.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage group
 */
class GroupService 
{

    /**
     * Get all member IDs of the given User Group.
     * @return array an Array with User IDs
     */
    function getMemberIDs($groupid)
    {
        $user = array();
        $values = array( 'GROUP_ID' => $groupid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('group_member');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $results = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        for($i=0; $i < $results->count(); $i++)
        {
            $temp = $results->next();
            $user[] = $temp['userid'];
        }
        return $user;
    }
    
    /**
     * Get all Groups the given Principal is Member of.
     * @param Principal principal the Principal to get the Memberships for
     * @return array an Arry with Group instances
     */
    function getMemberships($principal)
    {
        $groups = array();
        $values = array( 'USER' => $principal->getID() );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('group_user_memberships');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $results = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        for($i=0; $i < $results->count(); $i++) {
        	$g = new Group();
        	$g->init($results->next());
            $groups[] = $g;
        }
        return $groups;
    }
    
    /**
     * Returns an array with <code>Principal</code> instances.
     * @return array all Principals that are member of the given Group
     */
    function getGroupMember($groupID)
    {
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();

        $ids = $this->getMemberIDs($groupID);
        $groupMember = array();

        foreach($ids AS $principalID)
        {
            $p = $PRINCIPALS->lookupByID($principalID);
            if($p != null) {
                $groupMember[] = $p;
            }
        }

        return $groupMember;
    }
    
}
