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

import('classes.workflow.WorkflowService');
import('classes.menu.MenuService');
import('classes.menu.Menu');

/**
 * This class wraps all the Editor calls.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage administration
 */
class EditorContext
{
    /**
     * @access private
     */
     var $userID;
    /**
     * @access private
     */
     var $editor;
    /**
     * @access private
     */
     var $menu = NULL;
    /**
     * @access private
     */
     var $isFuture = false;
    
    function EditorContext($editorType, $userID)
    {
        $this->editor = $editorType;
        $this->userID = $userID;
    }
    
    function getLanguage() 
    {
    	$temp = $this->getMenu();
        return new Language($temp->getLanguageID());
    }
    
    function getMenu()
    {
        if ($this->menu == NULL)
        {
            $langid = $this->getLanguageID();
            
            // Workflow and Future Version support
            $wfservice = new WorkflowService(_BIGACE_ITEM_MENU);
            if ($wfservice->hasRunningWorkflow($GLOBALS['_BIGACE']['PARSER']->getItemID(), $langid)) 
            {
                $FUTUREWF = $wfservice->getWorkflowForItem($GLOBALS['_BIGACE']['PARSER']->getItemID(), $langid);
                $this->menu = $FUTUREWF->getItem();
                $this->isFuture = true;
            }
            else 
            {
                $MENU_SERVICE   = new MenuService();
                $this->menu     = $MENU_SERVICE->getMenu($GLOBALS['_BIGACE']['PARSER']->getItemID(), $langid);
                $this->isFuture = false;
            }
        }
        return $this->menu;
    }
    
    function isWorkflowVersion()
    {
        return $this->isFuture;
    }
    
    function getLanguageID()
    {
        $data = extractVar('data', array());
        return (isset($data['langid'])) ? $data['langid'] : $GLOBALS['_BIGACE']['PARSER']->getLanguage();
    }
    
    function canAccessEditor()
    {
        return (has_user_permission($this->userID, _BIGACE_FRIGHT_USE_EDITOR) || 
                has_user_permission($this->userID, 'edit_menus') ||
                has_user_permission($this->userID, _BIGACE_FRIGHT_ADMIN_MENUS));
    }
    
    /**
     * @deprecated use hasWritePermission() instead 
     * @return unknown_type
     */
    function hasMenuWriteRights()
    {
    	return $this->hasWritePermission();
    }

    function hasWritePermission()
    {
		$temp = $this->getMenu();
    	return has_item_permission(_BIGACE_ITEM_MENU, $temp->getID(), 'w');
    }
    
}

?>