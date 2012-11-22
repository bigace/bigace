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
 * @subpackage administration
 */

import('classes.administration.EditorContext');
import('classes.menu.MenuService');
import('classes.menu.MenuAdminService');
import('classes.menu.ContentAdminService');
import('classes.workflow.WorkflowService');
import('classes.right.RightService');

/**
 * This class wraps the Save process for Editor.
 * You do not have to perform rigt checks or Workflow calls, everything will be handled
 * in here.
 *
 * Simply call getMessage() and getTitle() to show Feedback messages to the User.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage administration
 */
class EditorSaveHelper
{
    /**
     * @access private
     */
    var $msg = '';
    /**
     * @access private
     */
    var $title = '';
    /**
     * @access private
     */
    var $editorContext = null;

    function EditorSaveHelper($context) {
        $this->editorContext = $context;
        loadLanguageFile('editor', $GLOBALS['_BIGACE']['SESSION']->getLanguageID());
    }

    /**
     * @access private
     */
    function translate($key) {
        return getTranslation($key);
    }

    function saveContent($content, $additional = array())
    {
        $context = $this->editorContext;
        $MENU = $context->getMenu();

        if ($context->hasMenuWriteRights())
        {
            $GLOBALS['LOGGER']->logDebug("[EDITOR] Save Menu (ID:".$MENU->getID()."/Language:".$MENU->getLanguageID().") for User ".$GLOBALS['_BIGACE']['SESSION']->getUserID());

            $adminService = new MenuAdminService();
            $indexer = '';

            // save additional project content before proceeding with the pages content
            if(!is_null($additional) && is_array($additional) && count($additional) > 0) {
                foreach($additional AS $prjKey => $prjValue)
                {
                    $csaver = new ContentSaver($MENU->getID(), $MENU->getLanguageID(), $prjKey, $prjValue);
                    save_content($csaver);
                    $indexer .= ' ' . $prjValue;
                }
            }

            // clear content (stripslashes already in init_session implemented)
            $parsedContent = $content;

            if(!$context->isWorkflowVersion() && ($MENU->getWorkflowName() == NULL || strlen(trim($MENU->getWorkflowName())) == 0))
            {
                // no workflow for this menu, save it directly
                if ($adminService->updateItemContent($MENU->getID(), $MENU->getLanguageID(), $parsedContent, array('text_5' => $indexer))) {
                    $this->msg = $this->translate('save_menu');
                    return true;
                } else {
                    $this->msg = $this->translate('error_save_menu');
                }
            }
            else
            {
                // save the updated content via the workflow service
                $wfs = new WorkflowService(_BIGACE_ITEM_MENU);

                if( $wfs->hasRunningWorkflow($MENU->getID(), $MENU->getLanguageID()) ) {
                    if(!$wfs->isPermittedToEdit($GLOBALS['_BIGACE']['SESSION']->getUserID(), $MENU->getID(), $MENU->getLanguageID())) {
                        $this->msg = $this->translate('error_running_workflow');
                    } else {
                        if ($wfs->updateItemContent($MENU->getID(), $MENU->getLanguageID(), $parsedContent)) {
                            $this->msg = $this->translate('save_workflow');
                            return true;
                        } else {
                            $this->msg = $this->translate('error_save_workflow');
                        }
                    }
                } else {
                    if ($wfs->updateItemContent($MENU->getID(), $MENU->getLanguageID(), $parsedContent)) {
                        $this->msg = $this->translate('start_workflow');
                        return true;
                    } else {
                        $this->msg = $this->translate('error_start_workflow');
                    }
                }
            }

        }
        else
        {
            $GLOBALS['LOGGER']->logError("User with ID: ".$GLOBALS['_BIGACE']['SESSION']->getUserID()." tried to save Menu (ID:".$MENU->getID()."/Language:".$MENU->getLanguageID().") without write rights.");
            $this->msg = $this->translate('error_no_write_rights');
        }
        return false;
    }

    function getMessage() {
        return $this->msg;
    }

}

?>