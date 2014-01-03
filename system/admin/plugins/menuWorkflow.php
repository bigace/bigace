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
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.administration
 */

/**
 * This Plugin display all pending and for the User assigned Workflows.
 * The User may assign pending Workflows and edit assigned Workflow instances.
 */

check_admin_login();
admin_header();

import('classes.workflow.WorkflowService');
import('classes.util.html.FormularHelper');

define('PARAM_METHOD', 'wfmethod');
define('PARAM_ITEMID', 'wfitemid');
define('PARAM_LANGUAGE', 'wflang');
define('PARAM_ACTIVITY', 'wfactivity');
define('METHOD_EXECUTE_ACTIVITY', 'activityStart');

$SERVICE = new WorkflowService(_BIGACE_ITEM_MENU);

// check if we should execute any task
$method = extractVar(PARAM_METHOD, '');
if ($method != '')
{
    if ($method == METHOD_EXECUTE_ACTIVITY)
    {
        $id         = extractVar(PARAM_ITEMID, '');
        $langid     = extractVar(PARAM_LANGUAGE, '');
        $activity   = extractVar(PARAM_ACTIVITY, '');
        if($id != '' && $langid != '' && $activity != '')
        {
            if (!$SERVICE->performActivity($id, $langid, $activity))
            {
                $GLOBALS['LOGGER']->logError("WF: Could not performActivity(".$id.", ".$langid.", ".$activity.")");
            }
        }
        unset($id);
        unset($langid);
        unset($activity);
    }
}


// --------------------------------------------------------------
// ------------------ [START] Pending Workflows -----------------

echo '<b><u>'.getTranslation('pending_title', 'Pending Workflows').'</u></b><br>';

// Get assigned Jobs and display them if list has at least one token
$allAssigned = $SERVICE->getPendingWorkflows($GLOBALS['_BIGACE']['SESSION']->getUserID());

if (count($allAssigned) > 0)
{
    echo '&nbsp;<p>'.getTranslation('pending_text').'</p><br>';
    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("AssignedWorkflowList.tpl.htm", false, true);

    $cssClass = "row1";
    
    foreach($allAssigned AS $workflow)
    {
        $future = $workflow->getItem();
        $activities = $workflow->getPossibleActivities();
        $acts = array('' => '');
        foreach($activities AS $activity)
        {
            $acts[$activity->getName()] = $activity->getID(); 
        }
        $act = createNamedSelectBox(PARAM_ACTIVITY, $acts);
        $url = createAdminLink($GLOBALS['MENU']->getID(), array(PARAM_METHOD => METHOD_EXECUTE_ACTIVITY, PARAM_LANGUAGE => $future->getLanguageID(), PARAM_ITEMID => $future->getID()));
    
        $tpl->setCurrentBlock("row") ;
        $tpl->setVariable("CSS_CLASS", $cssClass) ;
        $tpl->setVariable("ACTION_URL", $url);
        $tpl->setVariable("WORKFLOW_NAME", $workflow->getName());
        $tpl->setVariable("ITEM_NAME", $future->getName());
        $tpl->setVariable("LANG_LOCALE", $future->getLanguageID());
        $tpl->setVariable("PREVIEW_URL", createMenuLink($future->getID(), array('previewItemID' => $future->getID(), 'previewLanguageID' => $future->getLanguageID())));
        $tpl->setVariable("NEXT_ACTIVITY", $act);
        $tpl->setVariable("PERFORM_STEP", "BUTTON");
        $tpl->parseCurrentBlock("row") ;
      
        $cssClass = ($cssClass == "row1") ? "row2" : "row1";
    }
    
    $tpl->show();
}
else
{
    echo '<br><b>'.getTranslation('no_pending', 'No pending Worflows available.').'</b><br>';
}

// ------------------- [END] Pending Workflows ------------------
// --------------------------------------------------------------

echo '<br><br>';

// --------------------------------------------------------------
// ----------------- [START] Assigned Workflows -----------------

echo '<b><u>'.getTranslation('assigned_title', 'Assigned Workflows').'</u></b><br>';

// Get assigned Jobs and display them if list has at least one token
$allAssigned = $SERVICE->getAssignedWorkflows($GLOBALS['_BIGACE']['SESSION']->getUserID());

if (count($allAssigned) > 0)
{
    echo '&nbsp;<p>'.getTranslation('assigned_text').'</p><br>';
    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("AssignedWorkflowList.tpl.htm", false, true);

    $cssClass = "row1";
    
    foreach($allAssigned AS $workflow)
    {
        $future = $workflow->getItem();
        $activities = $workflow->getPossibleActivities();
        $acts = array('' => '');
        foreach($activities AS $activity)
        {
            $acts[$activity->getName()] = $activity->getID(); 
        }
        $act = createNamedSelectBox(PARAM_ACTIVITY, $acts);
        $url = createAdminLink($GLOBALS['MENU']->getID(), array(PARAM_METHOD => METHOD_EXECUTE_ACTIVITY, PARAM_LANGUAGE => $future->getLanguageID(), PARAM_ITEMID => $future->getID()));
    
        $tpl->setCurrentBlock("row") ;
        $tpl->setVariable("CSS_CLASS", $cssClass) ;
        $tpl->setVariable("ACTION_URL", $url);
        $tpl->setVariable("WORKFLOW_NAME", $workflow->getName());
        $tpl->setVariable("ITEM_NAME", $future->getName());
        $tpl->setVariable("LANG_LOCALE", $future->getLanguageID());
        $tpl->setVariable("PREVIEW_URL", createMenuLink($future->getID(), array('previewItemID' => $future->getID(), 'previewLanguageID' => $future->getLanguageID())));
        $tpl->setVariable("WORKFLOW_ACTIVITY", ($workflow->getActivityID() == NULL ? '' : $workflow->getActivityID()));
        $tpl->setVariable("NEXT_ACTIVITY", $act);
        $tpl->setVariable("PERFORM_STEP", "BUTTON");
        $tpl->parseCurrentBlock("row") ;
      
        $cssClass = ($cssClass == "row1") ? "row2" : "row1";
    }
    
    $tpl->show();
}
else
{
    echo '<br><b>'.getTranslation('no_assigned', 'No assigned Worflows available.').'</b><br>';
}

// ------------------ [END] Assigned Workflows ------------------
// --------------------------------------------------------------

admin_footer();

?>
