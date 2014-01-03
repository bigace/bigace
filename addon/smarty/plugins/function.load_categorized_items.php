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
 * Fetches an array of items for a category.
 *
 * - itemtype	itemtype
 * - category	category id to fetch items for
 * - id			parent to fetch items below
 * - language	language to fetch items in
 * - orderby	column name to order items
 * - order		the order to sort items
 * - assign		template variable name to assign array to
 * - counter	template variable name to assign amount of items to
 * - amount
 * - from
 * - to
 */
function smarty_function_load_categorized_items($params, &$smarty)
{	
	if(!isset($params['assign'])) {
		$smarty->trigger_error("load_categorized_items: missing 'assign' attribute");
		return;
	}
	if(!isset($params['category'])) {
		$smarty->trigger_error("load_categorized_items: missing 'category' attribute");
		return;
	}
	
	$cats = explode(",",$params['category']);
	if($cats === FALSE || count($cats) == 0) {
		$smarty->trigger_error("load_categorized_items: 'category' attribute must be a (comma separated) string");
		return;
	}

	$itemtype = (isset($params['itemtype']) ? $params['itemtype'] : _BIGACE_ITEM_MENU);	
	
	$ir = new ItemRequest($itemtype);
	foreach($cats AS $c)
		$ir->setCategory($c);
	
	if(isset($params['language']))
		$ir->setLanguageID($params['language']);
	if(isset($params['id']))
		$ir->setID(isset($params['id']));
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
