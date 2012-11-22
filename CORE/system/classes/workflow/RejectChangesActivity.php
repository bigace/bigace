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

import('classes.workflow.WorkflowActivity');
import('classes.workflow.WorkflowService');
import('classes.item.ItemFutureService');
import('classes.group.GroupService');
import('classes.core.ServiceFactory');

if (!defined('REJECT_CHANGES_ACTIVITY_ID')) {
    /**
     * @access private
     */
    define('REJECT_CHANGES_ACTIVITY_ID', 'RejectChangesActivity');
}

/**
 * The RejectChangesActivity is initiated by the Reviewer.
 * It will be performed if the Reviewer does not like the changes and wants
 * the Author to rework on the Item.
 * It reassigns the Future Item back to the last "modifiedBy" User and deletes
 * all review Access Rights  
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage workflow
 */
class RejectChangesActivity extends WorkflowActivity
{
    var $activity = '';
    
    function RejectChangesActivity($activityID = '')
    {
        $this->activity = $activityID;
    }
    
    /**
     * Get the ID of this Activity.
     */
    function getID() 
    {
        return REJECT_CHANGES_ACTIVITY_ID;
    }

    /**
     * Get the Description of this Activity.
     */
    function getDescription() 
    {
        return getTranslation('reject_description');
    }
    
    /**
     * Get the Name of this Activity.
     */
    function getName() 
    {
        return getTranslation('reject_name', 'Reject Changes');
    }

    function performActivity($itemtype, $itemid, $languageid)
    {
        $ifs = new ItemFutureService($itemtype);
        if ($ifs->hasFutureVersion($itemid, $languageid))
        {
            $futureItem = $ifs->getFutureVersion($itemid, $languageid);
            $services = ServiceFactory::get();
            $PRINCIPALS = $services->getPrincipalService();
            $autor = $PRINCIPALS->lookupByID( $futureItem->getInitiatorID() );

            // change wf state and set rights
            $wfs = new WorkflowService($itemtype);
            $wfs->setActivity($itemid, $languageid, $this->activity);
            return $wfs->assignWorkflow($itemid, $languageid,$autor->getID());
        }
        return FALSE;
    }
    
}

?>