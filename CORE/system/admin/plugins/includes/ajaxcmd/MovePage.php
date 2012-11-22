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
 * For further information visit {@link http://www.kevinpapst.de www.kevinpapst.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.administration
 */

require_once(dirname(__FILE__).'/AjaxCommand.php');

import('classes.item.ItemAdminService');
import('classes.menu.MenuAdminService');

class MovePage extends AjaxCommand
{
    function getID() {
        return extractVar('treeID', null);
    }

    function getParentID() {
        return extractVar('parentID', null);
    }

    function execute()
    {
        $moveID = $this->getID();
        $parentID = $this->getParentID();

        SetXmlHeaders();
        echo '<?xml version="1.0"?>';

        echo "\n<MovePage>\n";
        
        if(has_item_permission(_BIGACE_ITEM_MENU, $moveID, 'w'))
        {
            if($moveID == _BIGACE_TOP_LEVEL) {
                echo createBooleanNode('Result', false, array('parentID' => $parentID, 'treeID' => $moveID));
                echo createPlainNode(_PARAM_AJAX_MSG, 'Can not move TOP LEVEL menu');
                $GLOBALS['LOGGER']->logError("Tried to move TOP LEVEL Menu with MovePage AjaxCommand, canceled!");
            }
            else if($moveID != null && $parentID != null && $moveID != _BIGACE_TOP_LEVEL)
            {
                $menuAdmin = new MenuAdminService();
                if($menuAdmin->moveItem($moveID, $parentID)) {
                    echo createBooleanNode('Result', true, array('parentID' => $parentID, 'treeID' => $moveID));
                    echo createPlainNode(_PARAM_AJAX_MSG, 'Moved item.');
                }
                else {
                    echo createBooleanNode('Result', false, array('parentID' => $parentID, 'treeID' => $moveID));
                    echo createPlainNode(_PARAM_AJAX_MSG, 'Could not move item.');
                }
            }
            else
            {
                echo createBooleanNode('Result', false, array('parentID' => $parentID, 'treeID' => $moveID));
                echo createPlainNode(_PARAM_AJAX_MSG, 'Parameter missing');
                $GLOBALS['LOGGER']->logError("Problems with MovePage AjaxCommand, missing ID!");
            }
        }
        else
        {
            echo createBooleanNode('Result', false, array('parentID' => $parentID, 'treeID' => $moveID));
            echo createPlainNode(_PARAM_AJAX_MSG, 'No permission to move item.');
        }
        echo "\n</MovePage>\n";
        exit;
    }
}

