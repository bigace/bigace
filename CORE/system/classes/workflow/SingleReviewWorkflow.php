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
 * @subpackage workflow
 */

loadClass('workflow', 'Workflow');
loadClass('workflow', 'PublishingActivity');
loadClass('workflow', 'TerminatingActivity');
loadClass('workflow', 'PendingReviewActivity');
loadClass('workflow', 'RejectChangesActivity');
loadClass('workflow', 'AssignWorkflowActivity');

/**
 * The SingleReviewWorkflow is a Workflow where one Person of a 
 * specified User Group has to accept the submitted Changes.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage workflow
 */
class SingleReviewWorkflow extends Workflow
{
    
    function SingleReviewWorkflow() {
        // default constructor
    }

    function getDescription() 
    {
        return getTranslation('single_review_description');
    }
    
    function getName()
    {
        return getTranslation('single_review_name', 'Single Review Workflow');
    }

    function initWorkflow() 
    {
        //$GLOBALS['LOGGER']->logDebug('SingleReview Worflow is initialized');
    }
    
    function performActivity($activityID)
    {
    	$item = $this->getItem();
        if ($this->getActivityID() == NULL)
        {
            if($activityID == TERMINATING_ACTIVITY_ID)
            {
                $act = new TerminatingActivity();
                return $act->performActivity($item->getItemtypeID(), $item->getID(), $item->getLanguageID());
            }
            else if($activityID == PENDING_REVIEW_ACTIVITY_ID)
            {
                $act = new PendingReviewActivity($this->getReviewerGroupID());
                return $act->performActivity($item->getItemtypeID(), $item->getID(), $item->getLanguageID());
            }
        }
        else if ($this->getActivityID() == PENDING_REVIEW_ACTIVITY_ID)
        {
            if($activityID == ASSIGN_WORKFLOW_ACTIVITY_ID)
            {
                $act = new AssignWorkflowActivity($GLOBALS['_BIGACE']['SESSION']->getUserID());
                return $act->performActivity($item->getItemtypeID(), $item->getID(), $item->getLanguageID());
            }
        }
        else if ($this->getActivityID() == ASSIGN_WORKFLOW_ACTIVITY_ID)
        {
            if($activityID == PUBLISHING_ACTIVITY_ID)
            {
                $act = new PublishingActivity();
                return $act->performActivity($item->getItemtypeID(), $item->getID(), $item->getLanguageID());
            }
            else if($activityID == REJECT_CHANGES_ACTIVITY_ID)
            {
                $act = new RejectChangesActivity('');
                return $act->performActivity($item->getItemtypeID(), $item->getID(), $item->getLanguageID());
            }
        }
        $GLOBALS['LOGGER']->logError('Invalid passed Activity: ' . $activityID);
        return FALSE;
    }
    
    function getPossibleActivities() 
    {
        if ($this->getActivityID() == NULL)
        {
            return array(
                    new PendingReviewActivity($this->getReviewerGroupID()),
                    new TerminatingActivity()
                   );
        }
        else if ($this->getActivityID() == PENDING_REVIEW_ACTIVITY_ID)
        {
            return array(
                    new AssignWorkflowActivity($GLOBALS['_BIGACE']['SESSION']->getUserID())
                   );
        }
        else if ($this->getActivityID() == ASSIGN_WORKFLOW_ACTIVITY_ID)
        {
            return array(
                    new PublishingActivity(),
                    new RejectChangesActivity('')
                   );
        }
        
        return array();
    }
    
    /**
     * Returns the User Group ID of all User, who are 
     * allowed to review and Publish the Changes. 
     */
    function getReviewerGroupID()
    {
        return ConfigurationReader::getConfigurationValue('workflow', 'singlereview.group.id');
    }
    
}

?>