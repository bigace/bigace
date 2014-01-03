<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst
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

/**
 * The PublishingWorkflow is a really simple Workflow.
 * <br>
 * It saves all changes in a Future version, until the current User publishes these changes.
 * The changes will not be visible to the Public audience, until the User publishes them. 
 * We also do not create a thousand Histoty versions, cause only one Future version is updated, 
 * until everything is fine.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage workflow
 */
class PublishingWorkflow extends Workflow
{
    
    function PublishingWorkflow() {
        // default constructor
    }

    function getDescription() 
    {
        return getTranslation('publishing_wf_description');
    }
    
    function getName()
    {
        return getTranslation('publishing_wf_name', 'Publishing Workflow');
    }

    function initWorkflow() 
    {
        //$GLOBALS['LOGGER']->logDebug('Publishing Worflow is initialized');
    }

    function performActivity($activityID)
    {
    	$item = $this->getItem();
        if($activityID == PUBLISHING_ACTIVITY_ID)
        {
            $act = new PublishingActivity();
            return $act->performActivity($item->getItemtypeID(), $item->getID(), $item->getLanguageID());
        }
        else if($activityID == TERMINATING_ACTIVITY_ID)
        {
            $act = new TerminatingActivity();
            return $act->performActivity($item->getItemtypeID(), $item->getID(), $item->getLanguageID());
        }
        return FALSE;
    }
    
    function getPossibleActivities() 
    {
        if ($this->getActivityID() == NULL)
        {
            return array(
                    new PublishingActivity(),
                    new TerminatingActivity()
                   );
        }
        return array();
    }
}

?>