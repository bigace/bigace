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

loadClass('workflow', 'WorkflowActivity');
loadClass('workflow', 'WorkflowService');
loadClass('item', 'ItemFutureService');
loadClass('group', 'GroupService');

if (!defined('ASSIGN_WORKFLOW_ACTIVITY_ID')) 
{
    /**
     * @access private
     */
    define('ASSIGN_WORKFLOW_ACTIVITY_ID', 'AssignWorkflowActivity');
}

/**
 * The AssignWorkflowActivity is initiated by the Reviewer.
 * It will be performed if the Reviewer wants to take the Workflow Activity.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage workflow
 */
class AssignWorkflowActivity extends WorkflowActivity
{
    var $userID = '';
    
    function AssignWorkflowActivity($user = '')
    {
        if($user == '')
            $user = $GLOBALS['_BIGACE']['SESSION']->getUserID();
        $this->userID = $user;    
    }
    
    /**
     * Get the ID of this Activity.
     */
    function getID() 
    {
        return ASSIGN_WORKFLOW_ACTIVITY_ID;
    }

    /**
     * Get the Description of this Activity.
     */
    function getDescription() 
    {
        return getTranslation('assign_description');
    }
    
    /**
     * Get the Name of this Activity.
     */
    function getName() 
    {
        return getTranslation('assign_name', 'Assign Activity');
    }

    function performActivity($itemtype, $itemid, $languageid)
    {
        $service = new WorkflowService($itemtype);
        if ($service->hasRunningWorkflow($itemid, $languageid))
        {
            $service->setActivity($itemid, $languageid, $this->getID());
            return $service->assignWorkflow($itemid, $languageid, $this->userID);
        }
        return FALSE;
    }
    
}

?>