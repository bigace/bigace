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
 * @subpackage item.menu
 */

import('classes.item.ItemService');
import('classes.item.ItemRequest');
import('classes.item.SimpleItemTreeWalker');
import('classes.menu.Menu');
import('classes.workflow.WorkflowService');

require_once(dirname(__FILE__).'/AjaxCommand.php');


class TreeXml extends AjaxCommand
{
    function getID() {
        return extractVar('treeID', null);
    }

    function execute()
    {
        $myId = $this->getID();
        SetXmlHeaders();
        echo '<?xml version="1.0"?>';
        echo "\n<tree>\n";

        if($myId != null)
        {
            $callback = extractVar('callback', 'selectMenu');
        	$req = new ItemRequest(_BIGACE_ITEM_MENU, $myId);
        	$req->setTreetype(ITEM_LOAD_LIGHT);
        	$req->setOrderBy(ORDER_COLUMN_POSITION);
        	$req->setOrder($req->_ORDER_ASC);
        	$req->setFlagToExclude($req->FLAG_ALL_EXCEPT_TRASH);
        	$menu_info = new SimpleItemTreeWalker($req);

        	for ($i=0; $i < $menu_info->count(); $i++)
        	{
        		$temp_menu = $menu_info->next();
                echo $this->createTreeXmlNode($temp_menu, $callback);
        	}
        }
        else
        {
            $GLOBALS['LOGGER']->logError("Problems creating XML Output for Menu Tree, missing ID!");
        }

        echo "\n</tree>\n";
    }

    function createItemXmlTreeLink($item) {
        return createAdminLink('ajaxCommand', array('treeID' => $item->getID(), 'ajaxCmd' => 'TreeXml'), 'tree.xml', 'plain');
    }

function createTreeXmlNode($item, $callback = 'selectMenu') {
    $service = new ItemService(_BIGACE_ITEM_MENU);
    $ws = new WorkflowService($item->getItemtypeID());
    $hasWF = $ws->hasRunningWorkflow($item->getID(), $item->getLanguageID());

    $tree = '<tree text="'.prepareXMLName($item->getName()).'" action="javascript:'.$callback.'(\''.$item->getID().'\',\''.$item->getLanguageID().'\')'.'"';
    if($hasWF && $item->isHidden())
        $tree .= ' icon="'.$GLOBALS['_BIGACE']['style']['DIR'].'menutree/filehiddenworkflow.png"';
    else if($hasWF)
        $tree .= ' icon="'.$GLOBALS['_BIGACE']['style']['DIR'].'menutree/fileworkflow.png"';
    else if($item->isHidden())
        $tree .= ' icon="'.$GLOBALS['_BIGACE']['style']['DIR'].'menutree/filehidden.png"';

	if (!$service->isLeaf($item->getID())) {
        $tree .= ' src="'.ConvertToXmlAttribute($this->createItemXmlTreeLink($item)).'"';
    }
    $tree .= '/>' . "\n";
    $GLOBALS['LOGGER']->logDebug($tree);
    return $tree;
}

}
