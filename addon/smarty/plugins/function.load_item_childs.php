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

import('classes.item.ItemRequest');
import('classes.item.SimpleItemTreeWalker');

/**
 * Fetches an array of items from a specified parent.
 * 
 * Parameter:
 * - itemtype
 * - id
 * - language
 * - orderby
 * - order
 * - assign
 * - counter
 * - amount
 */
function smarty_function_load_item_childs($params, &$smarty)
{	
	if(!isset($params['assign'])) {
		$smarty->trigger_error("load_item_childs: missing 'assign' attribute");
		return;
	}
	$itemtype = (isset($params['itemtype']) ? $params['itemtype'] : _BIGACE_ITEM_MENU);	
	$id = (isset($params['id']) ? $params['id'] : $GLOBALS['_BIGACE']['PARSER']->getItemID());	
	$lang = (isset($params['language']) ? $params['language'] : $GLOBALS['_BIGACE']['PARSER']->getLanguage());

	$ir = new ItemRequest($itemtype);
	$ir->setID($id);
	$ir->setLanguageID($lang);
	
	if(isset($params['orderby']))
		$ir->setOrderBy($params['orderby']);
	if(isset($params['order']))
		$ir->setOrder($params['order']);

	if(isset($params['amount']) || isset($params['to'])) {
		$ir->setLimit((isset($params['from']) ? $params['from'] : 0), (isset($params['to']) ? $params['to'] : $params['amount']));
	}
				
	$menu_info = new SimpleItemTreeWalker($ir);

	$items = array();

	if(isset($params['counter']))
		$smarty->assign($params['counter'], $menu_info->count());

    for ($i=0; $i < $menu_info->count(); $i++) {
		$items[] = $menu_info->next();
    }
    
	$smarty->assign($params['assign'], $items);
}
