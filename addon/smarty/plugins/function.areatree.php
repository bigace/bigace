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
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.smarty
 * @subpackage function
 */

import('classes.util.SmartyLink');
import('classes.util.LinkHelper');
import('classes.right.RightService');
import('classes.menu.MenuService');

/**
 * Prints or fetches a Tree for the current area in the page tree
 * 
 * Parameter:
 * language 	= the language to get the tree for, default is the current language from the session (the users language)
 * css 			= the css class for the rendered link (<a>) node, default is empty
 * hidden 		= show also hidden pages in the page tree, defult is false
 * prefix 		=	the html to be prepended to every item link in the areatree (will be printed before the <a href="...>itemnam</a> code of each item), default is empty
 * suffix 		= the html to be appended to every item link in the areatree (will be printed after the <a href="...>itemnam</a> code of each item), default is empty
 * selected 	= the css class for all items on the path to root, default is empty
 * start 		= the initial value that will be prepended to the output of this tag, default is empty
 * maxdepth 	= the maximum level depth to crawl into the page tree. No pages with a higher level value then the speicifed will be displayed in the areatree, default is 999 (all levels)
 * assign 		= if set the output of this tag will be assigned to the variable instead of being printed to the output stream 
 * folded 		= check if only the subtree elements on path to root (and their siblings) should be shown or all items in subtree, default is false (=show all items in subtree)
 * debug 		= if set to true, debug output will be printed out while rendering the areatree, default is false
 * beforeLevelUp   = the text/html that should be place before a level down step source code, default is empty
 * beforeLevelDown = get the text/html that should be place before a level up step source code, default is empty
 */
function smarty_function_areatree($params, &$smarty)
{
	//the language to get the tree for, default is the current language from the session (the users language)
	$lang = (isset($params['language']) ? $params['language'] : $GLOBALS['_BIGACE']['PARSER']->getLanguage());
	//the css class for the rendered link (<a>) node, default is empty
	$css = (isset($params['css']) ? $params['css'] : '');
	//show also hidden pages in the page tree, defult is false
	$showHidden = ((isset($params['hidden']) && strtolower($params['hidden']) == 'true') ? true : false);	
	//the html to be prepended to every item link in the areatree (will be printed before the <a href="...>itemnam</a> code of each item), default is empty
	$pre = (isset($params['prefix']) ? $params['prefix'] : '');
	//the html to be appended to every item link in the areatree (will be printed after the <a href="...>itemnam</a> code of each item), default is empty
	$post = (isset($params['suffix']) ? $params['suffix'] : '');
	//the css class for all items on the path to root, default is empty
	$selected = (isset($params['selected']) ? $params['selected'] : '');
	//the initial value that will be prepended to the output of this tag, default is empty
	$html = (isset($params['start']) ? $params['start'] : '');
	//the maximum level depth to crawl into the page tree. No pages with a higher level value then the speicifed will be displayed in the areatree, default is 999 (all levels) 
	$maxdepth = (isset($params['maxdepth']) ? intval($params['maxdepth']) : 999);
	//if set to true, debug output will be printed out while rendering the areatree, default is false
	$debug = (isset($params['debug']) && $params['debug'] === 'true');
	//the text/html that should be place before a level down step source code, default is empty
	$beforeLevelDown = (isset($params['beforeLevelDown']) ? $params['beforeLevelDown'] : '');
	//get the text/html that should be place before a level up step source code, default is empty
	$beforeLevelUp = (isset($params['beforeLevelUp']) ? $params['beforeLevelUp'] : '');
	//check if only the subtree elements on path to root (and their siblings) should be shown or all items in subtree, default is false (=show all items in subtree)
	$folded = (isset($params['folded']) && $params['folded'] === 'true'); 

	//on top level no tree will be returned
	if($GLOBALS['_BIGACE']['PARSER']->getItemID() == -1){
		if($debug){
	    	$html .= 'current item is root, so no area tree will be rendered';
    	}
    	return;
	}

	$rService = new RightService();
	
	$pathToRootItems = array();
	//find area item and build path to root item id array
    $id = $GLOBALS['_BIGACE']['PARSER']->getItemID();
    if($debug){
	    $html .= '<br/>trying to find area id for '.$id;
    }
	while ($id != _BIGACE_TOP_PARENT) {
	    $CURRENT = $GLOBALS['MENU_SERVICE']->getMenu($id, $lang);
    	if(!$CURRENT->isHidden() || $showHidden) {
	        $wayHomeRight = $rService->getItemRight(_BIGACE_ITEM_MENU, $CURRENT->getID(), $GLOBALS['_BIGACE']['SESSION']->getUserID());
	        if ($wayHomeRight->canRead()) {
				array_push($pathToRootItems,$CURRENT);
	        }
    	}
        $id = $CURRENT->getParentID();
    }
	$pathFromRootItems = array_reverse($pathToRootItems);
	$areaItem = $pathFromRootItems[1];

	if($debug){
	    $html .= '<br/>area id set to '.$areaItem->getId();
    }
	
    unset ($wayHomeRight);
    unset ($rService);
    unset ($CURRENT);

	//build tree array
	$menu_info = helper_menus_recursive($areaItem, 0, $maxdepth, array(), $pathToRootItems , $folded);
	
	$menu_tree = array();
	//filter all items that are hidden if needed
	$currentLevelParentId = -1;
	$previousLevelParentId = -1;
	$previousParentIds = array();
	$levelCount = 0;
	
	for ($i=0; $i < count($menu_info); $i++){
		$temp_menu = $menu_info[$i];
		if($debug){
	    	$html .= '<br/>walking item tree, current item is '.$temp_menu->getName();
    	}
		if(!$temp_menu->isHidden() || $showHidden) {
			//check if max level depth has been reached
			if($levelCount <= $maxdepth){
				if($debug){
	    			$html .= '<br/>item has been added';
		    	}
				array_push($menu_tree,$temp_menu);
			}
		}
	}
	
	if(isset($params['counter']))
        $smarty->assign($params['counter'], count($menu_tree));
		
	//check if result should be assigned to variable
	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $menu_tree);
		return;
	}
	
	//build html
	if($debug){
		$html .= '<br/>building html';
	}
	$previousLevel = $menu_tree[0]->level;
    for ($i = 0 ; $i < count($menu_tree) ; $i++) 
    {
    	$temp_menu = $menu_tree[$i];
		$class = $css;
		$prefix = $pre;
		if($debug){
			$html .= '<br/>printing link for '.$temp_menu->getName();
		}
		//check if active items should be highlighted
		if (isset($params['selected'])) {
			$doHighlight = false;
			foreach($pathToRootItems as $onPathToRootItem){
				if($onPathToRootItem->getId() == $temp_menu->getId()){
					$doHighlight = true;
					break;
				}
			}
			if($doHighlight) {
				$class = $selected;
			}
		}
		$link = LinkHelper::getCMSLinkFromItem($temp_menu);
		//check if level has changed
		if($temp_menu->level > $previousLevel){
			if($beforeLevelDown != ''){
				$prefix = $beforeLevelDown.$prefix;
			}
		} else if($temp_menu->level < $previousLevel){
			if($beforeLevelUp != ''){
				$prefixNew = '';
			    for($n = $temp_menu->level ; $n < $previousLevel ; $n++){			
					$prefixNew .= $beforeLevelUp;
				}
				$prefix = $prefixNew.$prefix;
			}
		}
		$tabs = '';
		for($t = 0 ; $t < $temp_menu->level ; $t++){
			$tabs .= "\t";
		}
		
		$html .= $tabs.$prefix . '<a href="'.LinkHelper::getUrlFromCMSLink($link).'" class="'.(($class != '') ? $class.' '.$class.'_': '').'areatree_level_'.$temp_menu->level.'">'.$temp_menu->getName().'</a>'
		       . (($i == (count($menu_tree)-1) && isset($params['last'])) ? $params['last'] : $post) . "\n";
		$previousLevel = $temp_menu->level;
    }
    //check if level closing tags need to be written
    if($beforeLevelUp != ''){
	    for($n = $menu_tree[0]->level ; $n < $previousLevel ; $n++){
		    $html .= $beforeLevelUp;
    	}
    }
	
    return $html . (isset($params['end']) ? $params['end'] : '');
}

function helper_menus_recursive($startMenu, $level, $max, $menuarray , $pathToRootItems , $folded) {
	//echo('helper_menus_recursive called for menu '.$startMenu->getName().', level = '.$level.', maxdepth = '.$max);
	if($level < $max) {	
		//echo ('level is ok');
		$childs = $startMenu->getChilds();

		if($childs->count() > 0) {
			//echo ('found '.$childs->count().' childnodes');
			for($i=0; $i < $childs->count(); $i++)
			{
				$tempMenu = $childs->next();
				$tempMenu->level = $level;
				array_push($menuarray,$tempMenu);
				//check if only siblings of items on path to root should be shown
				if($folded){
					//check if current item is on path to root
					$isOnPathToRoot = false;
					foreach($pathToRootItems as $pathToRootItem){
						if($pathToRootItem->getId() == $tempMenu->getId()){
							$isOnPathToRoot = true;
						}
	                }
	                if($isOnPathToRoot){
		                $menuarray = helper_menus_recursive($tempMenu, $level+1, $max,$menuarray,$pathToRootItems,$folded);
	                }
				} else {
	                $menuarray = helper_menus_recursive($tempMenu, $level+1, $max,$menuarray,$pathToRootItems,$folded);
	            }
			}
		}
	}
	return $menuarray;
}
