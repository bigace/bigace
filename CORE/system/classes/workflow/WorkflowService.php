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
 * @subpackage workflow
 */

import('classes.item.Itemtype');
import('classes.item.ItemFutureService');
import('classes.group.GroupService');
import('classes.workflow.WorkflowActivity');
import('classes.workflow.PublishingWorkflow');
import('classes.workflow.SingleReviewWorkflow');

loadLanguageFile('workflow');

/**
 * The WorkflowService provides methods to receive Future versions within a Workflow.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage workflow
 */
class WorkflowService extends Itemtype
{
    /**
     * @access private
     */
    var $itemService = NULL;
    /**
     * @access private
     */
    var $futureService = NULL;
    
    function WorkflowService($itemtype) 
    {
        $this->Itemtype($itemtype);
    }
    
    /**
     * Get an ItemService instance.
     * @access private
     */
    function getItemService() 
    {
        if ($this->itemService == NULL) {
            $this->itemService = new ItemService($this->getItemtypeID());
        }
        return $this->itemService;
    }
    
    /**
     * Get a FutureService instance.
     * @access private
     */
    function getFutureService() 
    {
        if ($this->futureService == NULL) {
            $this->futureService = new ItemFutureService($this->getItemtypeID());
        }
        return $this->futureService;
    }
    
    /**
     * Returns all available Workflow types for the Itemtype.
     * @static 
     */
    static function getAllWorkflowTypes()
    {
        return array( 
                new PublishingWorkflow(),
                new SingleReviewWorkflow()
               );
    }

    /**
     * Updates the Items Content within the running Workflow.
     * If there is no running Workflow for the Item Language Version,
     * a Workflow will be started before.
     * 
     * @return boolean if the update could be performed
     */
    function updateItemContent($itemid, $languageid, $content) 
    {
        if (!$this->hasRunningWorkflow($itemid, $languageid))
        {
            $this->startWorkflow($itemid, $languageid);
        }
        $service = $this->getFutureService();
        return $service->updateFutureContent($itemid, $languageid, $content);
    }
    
    /**
     * This starts a Workflow by creating a Future Version and 
     * calling <code>assignWorkflow</code> with the current global User ID.
     */
    function startWorkflow($itemid, $languageid)
    {
        $this->startWorkflowForUser($itemid, $languageid, $GLOBALS['_BIGACE']['SESSION']->getUserID());
    }

    /**
     * This starts a Workflow by creating a Future Version and 
     * calling <code>assignWorkflow</code> with the given User ID.
     */
    function startWorkflowForUser($itemid, $languageid, $userid)
    {
        $service = $this->getFutureService();
        $service->createFutureVersion($itemid, $languageid);
        $this->assignWorkflow($itemid, $languageid, $userid);
    }

    /**
     * This publishes the Workflow Version. 
     * updates the Future Content, switching assigned User.
     *  
     */
    function publishWorkflowContent($itemid, $languageid)
    {
        $ifs = $this->getFutureService();
        if ($ifs->hasFutureVersion($itemid, $languageid))
        {
            $this->updateWorkflowItem($itemid, $languageid, array('activity' => '', 'num_5' => '0'));
            // if the future version can be deleted 
            if($ifs->publishFutureContent($itemid, $languageid))
            {
                // delete it afterwards
                return $ifs->deleteFutureVersion($itemid, $languageid);
            }
        }
    }
    
    /**
     * Returns a Workflow Instance for the given Item.
     * If this Item exists in a Future Version, it returns the Workflow
     * Name from this Future Version, otherwise it returns the current Workflow Name.
     * If none could be found, it returns NULL.
     * 
     * @return Workflow a Workflow instance or NULL
     */
    function getWorkflowForItem($itemid, $languageid) 
    {
        $future = $this->getFutureService();
        if ($future->hasFutureVersion($itemid, $languageid))
        {
            $item = $future->getFutureVersion($itemid, $languageid);
        }
        else
        {
            $service = $this->getItemService();
            $item = $service->getItem($itemid, ITEM_LOAD_FULL, $languageid);
        }

        $name = $item->getWorkflowName();
        
        if($name != NULL && strlen(trim($name)) > 0)
        {
            $temp = new $name();
            $temp->init($item);
            return $temp;
        }
        
        return NULL;
    }
    
    /**
     * Performs the given Activity on the Items Workflow, if one is started
     * and within the next Activites List.
     */
    function performActivity($itemid, $languageid, $activityID)
    {
        $future = $this->getFutureService();
        if ($future->hasFutureVersion($itemid, $languageid))
        {
            $wf = $this->getWorkflowForItem($itemid, $languageid);
            if ($wf != NULL)
            {
                $activities = $wf->getPossibleActivities();
                foreach($activities AS $activity)
                {
                    if($activity->getID() == $activityID)
                    {
                        return $wf->performActivity($activityID);
                    }
                }
            }
        }
        return FALSE;
    }
    
    /**
     * Sets the Activity ID for the given Workflow.
     * @return boolean whether the Activity could be set or not 
     */
    function setActivity($itemid, $languageid, $activityID)
    {
        return $this->updateWorkflowItem($itemid, $languageid, array('activity' => $activityID));
    }
    
    /**
     * @access private
     */
    function updateWorkflowItem($itemid, $languageid, $data)
    {
        if ($this->hasRunningWorkflow($itemid, $languageid))
        {
            $service = $this->getFutureService();
            return $service->updateDatabaseFutureVersion($itemid, $languageid, $data);
        }
        return FALSE;
    }
    
    /**
     * Returns if there is a running Workflow for the given Item.
     * @return boolean if there is a Workflow started already 
     */
    function hasRunningWorkflow($itemid, $languageid)
    {
        $future = $this->getFutureService();
        return ($future->hasFutureVersion($itemid, $languageid));
    }
    
    /**
     * Assign a Workflow to a single Person.
     * If there is nor Workflow running this returns FALSE.
     * If a Workflow is running, all access rights will be removed and ONLY
     * the assigned User will have the rights to access the Workflow.
     * 
     * @return boolean whether the assign could be performed or not
     */
    function assignWorkflow($itemid, $languageid, $userid)
    {
        if ($this->hasRunningWorkflow($itemid, $languageid))
        {
            $GLOBALS['LOGGER']->logDebug('Assign Workflow to User: ' . $userid. ' (ItemID:'.$itemid.', LanguageID:'.$languageid.')');
            $this->setPermittedUsers($itemid, array($userid), _BIGACE_RIGHTS_RWD);
            $this->updateWorkflowItem($itemid, $languageid, array('num_5' => $userid));
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Removes an assigment from the given Future Language Version.
     * Removing the Assigment brings a Workflow from the "AssignedWorkflow"
     * to the "PendingWorkflow" Status.
     * You have to pass an array with UserIDs that are allowed to assign the 
     * Job afterwards.
     *  
     * @param int itemid the ItemID
     * @param int languageid the LanguageID
     * @param array userids an Array with all UserIDs that might be permitted to assign the job
     */
    function setPendingWorkflow($itemid, $languageid, $userids)
    {
        if ($this->hasRunningWorkflow($itemid, $languageid))
        {
            $this->setPermittedUsers($itemid, $userids, _BIGACE_RIGHTS_RWD);
            $this->updateWorkflowItem($itemid, $languageid, array('num_5' => '0'));
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Sets a bunch of User access rights.
     * All existing right Entrys will be deleted and the new ones created.
     */
    function setPermittedUsers($itemid, $userids, $value)
    {
        $service = $this->getFutureService();
        $service->deleteFutureRights($itemid);
        $this->addPermittedUsers($itemid, $userids, $value);
    }

    /**
     * Adds a bunch of User access rights to the already existing ones.
     */
    function addPermittedUsers($itemid, $userids, $value)
    {
        $service = $this->getFutureService();
        foreach($userids AS $uid)
            $service->setFutureRights($itemid, $uid, $value);
    }

    /**
     * Adds User access rights for all User that are Members of
     * the given User Group.
     * Already existing rights will be kept.
     */
    function addPermittedUserGroup($itemid, $groupid, $value)
    {
        $groups = new GroupService();
        $userids = $groups->getMemberIDs($groupid);
        $this->addPermittedUsers($itemid, $userids, $value);
    }
    
    /**
     * Returns whether the given User is allowed to edit
     * the given Item Language Version.
     * This ONLY returns proper values for Workflow Items.
     * If the given Item is NOT in a Workflow, we always return false.
     * @return boolean true if Item is in a Workflow and User is allowed to edit  
     */
    function isPermittedToEdit($userid, $itemid, $languageid)
    {
        if ($this->hasRunningWorkflow($itemid, $languageid))
        {
            // running workflows can ONLY be edited by the assignee
            $temp = $this->getWorkflowForItem($itemid, $languageid);
            $temp = $temp->getItem();
            if($temp->getAssignedUserID() == NULL) {
                // content of pending workflow items may NOT be edited
                return false;
            }
            else
            {
                return ($userid == $temp->getAssignedUserID());
            }
        }
        
        return false;
    }
    
    /**
     * Return all pending Workflow the given User has access rights on.
     * Pending Workflows are all Workflows that are currently not assigned 
     * to anyone. 
     * Any permitted User may take this Job by calling 
     * <code>assignWorkflow($itemid, $languageid, $userid)</code>. 
     *   
     * @param int the UserID to get all Pending Workflows for
     */
    function getPendingWorkflows($userid)
    {
        $values = array ( 'CID'         => _CID_, 
                          'USER_ID'     => $userid,
                          'ITEMTYPE'    => $this->getItemtypeID(),
                          'RIGHT_VALUE' => _BIGACE_RIGHTS_RW );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('workflow_pending');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logSQL('Select pending WFs for User with Statement: ' . $sqlString);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $wfs = array();
        for ($i=0; $i < $res->count(); $i++)
        {
            $futureItem = new FutureItem($res->next());
            $wfs[] = $futureItem->getWorkflow();
        }
        return $wfs;
    }
    
    /**
     * Get all assigned Jobs for the given User.
     * @return array an array with FutureItem instances
     */
    function getAssignedWorkflows($userid)
    {
        $values = array ( 'CID'         => _CID_, 
                          'USER_ID'     => $userid,
                          'ITEMTYPE'    => $this->getItemtypeID() );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('workflow_assigned');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logSQL('Select assigned WFs for User with Statement: ' . $sqlString);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $wfs = array();
        for ($i=0; $i < $res->count(); $i++)
        {
            $futureItem = new FutureItem($res->next());
            $wfs[] = $futureItem->getWorkflow();
        }
        return $wfs;
    }
    
}

?>