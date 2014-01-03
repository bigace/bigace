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
 */

import('classes.item.ItemService');
import('classes.item.ItemRequest');
import('classes.item.SimpleItemTreeWalker');
import('classes.menu.Menu');
import('classes.menu.MenuService');
import('classes.workflow.WorkflowService');

require_once(dirname(__FILE__) . '/AjaxCommand.php');


class TreeJSON extends AjaxCommand
{

    function execute()
    {
		header("Cache-Control: must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")-2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
    	header("Content-Type:text/html; charset=UTF-8");

        $myId = extractVar(_PARAM_TREE_ID, null);
        $myLng = extractVar(_PARAM_TREE_LANGUAGE, null);
        
        if(is_null($myLng)) {
            echo "Error: no language is set";
            $GLOBALS['LOGGER']->logError("Could not create JSON for menu-tree, missing Language parameter.");
        }
        else
        {
            if(is_null($myId) || $myId == _BIGACE_TOP_PARENT)
            {   
        		echo '['."\n";
                $ms = new MenuService();
                //if($ms->existsLanguageVersion(_BIGACE_TOP_LEVEL, $myLng)) {
                    $toplevel = $ms->getMenu(_BIGACE_TOP_LEVEL, $myLng);
                    echo createTreeJSONNode($toplevel, $myLng, true, "open");
                //}
        		echo ']'."\n";
            }
            else {
                echo createJSONTree($myId, $myLng);
            }
        }
        exit;      
    }

    function createItemXmlTreeLink($item) {
        return createAdminLink('ajaxCommand', array('treeID' => $item->getID(), 'ajaxCmd' => 'TreeXml'), 'tree.xml', 'plain');
    }

}


function createJSONTree($id, $lang)
{
	$req = new ItemRequest(_BIGACE_ITEM_MENU, $id);
	$req->setLanguageID($lang);
	$req->setTreetype(ITEM_LOAD_FULL);
	$req->setOrderBy(ORDER_COLUMN_POSITION);
	$req->setOrder($req->_ORDER_ASC);
	$req->setFlagToExclude($req->FLAG_ALL_EXCEPT_TRASH);
	$menu_info = new SimpleItemTreeWalker($req);
	
	$html = '['."\n";

    if($menu_info->count() > 0) 
    {
    	for ($i=0; $i < $menu_info->count(); $i++)
    	{
    		$temp_menu = $menu_info->next();
            $html .= createTreeJSONNode($temp_menu, $lang, false, null);
            if($i < $menu_info->count()-1)
                $html .= ",\n";
    	}
    }
    $html .= ']'."\n";
    return $html;
}

function createTreeJSONNode($item, $lang, $recurse = false, $state = null) 
{
    $service = new ItemService(_BIGACE_ITEM_MENU);
    $ws = new WorkflowService($item->getItemtypeID());
    $hasWF = $ws->hasRunningWorkflow($item->getID(), $item->getLanguageID());
    $leaf = $service->isLeaf($item->getID());

    if ($leaf)
        $icon = 'file';
    else
        $icon = 'folder';

    if($item->isHidden())
        $icon .= 'hidden';
    if($hasWF)
        $icon .= 'workflow';
    
    $metadata = array();
    $metadata['language'] = $item->getLanguageID();
    $metadata['id'] = $item->getID();
    $metadata['position'] = $item->getPosition();
    $metadata['parent'] = $item->getParentID();

    $tree = "\t".'{ attributes: { id : "'.$item->getID().'", rel : "folder", itemid : "'.$item->getID().'"';

    // ---- METADATA ---- start
    // everything inside metadata can be read on client side
    $tree .= ', mdata : \'{ creatable : true, '; // TODO check creatable by writeable?

    $perm = get_item_permission(_BIGACE_ITEM_MENU, $item->getID());

    if($item->getID() == _BIGACE_TOP_LEVEL) {
        $tree .= 'draggable : false, deletable : false, ';
        if($perm->canWrite())
            $tree .= 'creatable : true, renameable : true, ';
        else
            $tree .= 'creatable : false, renameable : false, ';
    }
    else {
        if(!$perm->canWrite())
            $tree .= 'creatable : false, draggable : false, renameable : false, ';
        else
            $tree .= 'creatable : true, draggable : true, renameable : true, ';

        if(!$perm->canDelete())
            $tree .= 'deletable : false, ';
        else
            $tree .= 'deletable : true, ';
    }
    
    if($leaf)
        $tree .= 'leaf : true, ';
    else
        $tree .= 'leaf : false, ';

    $i=0;
    foreach($metadata AS $k => $v) 
    {
        if($i++ > 0)
            $tree .= ", ";
        $tree .= $k . ' : "' . $v . '"';
    }                
    $tree .= ' }\' ';
    // ---- METADATA ---- end 
    
    $tree .= '}' ; // end attributes
    
    $tree .= ', data: { title: "'.prepareXMLName($item->getName()).'" ';

    $tree .= ', attributes : { ';
        $tree .= ' "class" : "'.$icon.'"';
    $tree .= ' } '; // end data

    $tree .= ' } '; // end data
    
    if (!is_null($state)) {
        $tree .= ', state: "'.$state.'"';
    }
    else {
        if (!$leaf) {
            $tree .= ', state: "closed"';
        }
    }
    
    if($recurse) {
        $temp = createJSONTree($item->getID(), $lang);
        if(strlen(trim($temp)) > 0)
            $tree .= ', children : ' . $temp;
    }

    $tree .= ' }'."\n";
    return $tree;
}
