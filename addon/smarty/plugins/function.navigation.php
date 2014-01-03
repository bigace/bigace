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
import('classes.item.ItemRequest');
import('classes.item.SimpleItemTreeWalker');

/**
 * Prints a configurable navigation.
 * 
 * Parameter:
 * - id			=
 * - language	=
 * - css		=
 * - selected 	=
 * - active		=
 * - prefix		=
 * - suffix		=
 * - last		=		
 * - start		=
 * - orderby	=
 * - activeInTree = 
 * - end		=
 * - order		=
 * - active		=
 * - selected	=
 * - counter	=
 * - rel		=
 */
function smarty_function_navigation($params, &$smarty)
{
	$id = (isset($params['id']) ? $params['id'] : $GLOBALS['_BIGACE']['PARSER']->getItemID());	
	$lang = (isset($params['language']) ? $params['language'] : $GLOBALS['_BIGACE']['PARSER']->getLanguage());	
	$css = (isset($params['css']) ? ' class="'.$params['css'].'"' : '');
	$selected = (isset($params['selected']) ? ' class="'.$params['selected'].'"' : '');
	$active = (isset($params['active']) ? $params['active'] : '');
	$pre = (isset($params['prefix']) ? $params['prefix'] : '');
	$after = (isset($params['suffix']) ? $params['suffix'] : '');
	$html = (isset($params['start']) ? $params['start'] : '');
	$rel = (isset($params['rel']) ? ' rel="'.$params['rel'].'"' : '');
	$orderby = (isset($params['orderby']) ? $params['orderby'] : null);
	$activeInTree = (isset($params['activeInTree']) && $params['activeInTree'] == "true") ? true : false;
	
	$ms = null;
	if($activeInTree && !isset($GLOBALS['MENU_SERVICE'])) {
		import('classes.menu.MenuService');
		$ms = new MenuService();
	}
	else {
		$ms = $GLOBALS['MENU_SERVICE'];
	}
	
    $req = new ItemRequest(_BIGACE_ITEM_MENU, $id);
    $req->setLanguageID($lang);
    $req->setTreetype(ITEM_LOAD_LIGHT);
    if(!is_null($orderby))
    	$req->setOrderBy($orderby);
    if(isset($params['order']) && (strtoupper($params['order']) == "DESC" || strtoupper($params['order']) == "asc"))
    	$req->setOrder(strtoupper($params['order']));
	//$menu_info = $ms->getLightTreeForLanguage($id, $lang);
	
	$menu_info = new SimpleItemTreeWalker($req);

	if(isset($params['counter']))
        $smarty->assign($params['counter'], $menu_info->count());

    for ($i=0; $i < $menu_info->count(); $i++) 
    {
		$class = $css;
		$prefix = $pre;
		$temp_menu = $menu_info->next();
		if ((isset($params['active']) || isset($params['selected']))) {
			$do = false;
			if($temp_menu->getID() == $GLOBALS['_BIGACE']['PARSER']->getItemID())
				$do = true;
			else if($activeInTree && $ms->isChildOf($temp_menu->getID(), $GLOBALS['_BIGACE']['PARSER']->getItemID()))
				$do = true;
			
			if($do) {
				$class = $selected;
				if(isset($params['active'])) 
					$prefix = $active;
			}
		}
		$link = LinkHelper::getCMSLinkFromItem($temp_menu);
/*
		$link = new SmartyLink();
		$link->setItemID($temp_menu->getID());
		$link->setLanguageID($temp_menu->getLanguageID());
*/
		$html .= $prefix . "<a href=\"".LinkHelper::getUrlFromCMSLink($link)."\"".$class.$rel.">".$temp_menu->getName()."</a>"
		       . (($i == ($menu_info->count()-1) && isset($params['last'])) ? $params['last'] : $after);
    }
    
    return $html . (isset($params['end']) ? $params['end'] : '');
}
