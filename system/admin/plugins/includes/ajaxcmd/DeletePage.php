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

require_once(dirname(__FILE__) . '/AjaxCommand.php');

import('classes.item.ItemAdminService');
import('classes.menu.MenuAdminService');

class DeletePage extends AjaxCommand
{
    function getID() {
        return extractVar('treeID', null);
    }

    function execute()
    {
        $deleteID = $this->getID();

        SetXmlHeaders();
        echo '<?xml version="1.0"?>';

        echo "\n<DeletePage>\n";

        if($deleteID != null)
        {
            if(has_item_permission(_BIGACE_ITEM_MENU, $deleteID, 'd'))
            {
                if ($deleteID == _BIGACE_TOP_LEVEL) {
                    echo createBooleanNode('Result', false, array('treeID' => $deleteID));
                    echo createPlainNode(_PARAM_AJAX_MSG, 'Can not delete the root item.');
                } else {
                    $menuAdmin = new MenuAdminService();
                    $result = $menuAdmin->deleteItem($deleteID, true);
                    unset($menuAdmin);
                    if($result) {
                        echo createBooleanNode('Result', true, array('treeID' => $deleteID));
                        echo createPlainNode(_PARAM_AJAX_MSG, 'Deleted item.');
                    } else {
                        echo createBooleanNode('Result', false, array('treeID' => $deleteID));
                        echo createPlainNode(_PARAM_AJAX_MSG, 'Problem deleting item.');
                    }
                }
            }
            else
            {
                echo createBooleanNode('Result', false, array('treeID' => $deleteID));
                echo createPlainNode(_PARAM_AJAX_MSG, 'No permission to delete item.');
            }
        }
        else
        {
            echo createBooleanNode('Result', false, array('treeID' => $deleteID));
            echo createPlainNode(_PARAM_AJAX_MSG, 'Parameter missing');
            $GLOBALS['LOGGER']->logError("Problems executing DeletePage AjaxCommand, missing ID!");
        }

        echo "\n</DeletePage>\n";
        exit;
    }
}

