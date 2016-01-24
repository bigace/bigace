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

import('classes.category.ItemCategoryEnumeration');

/**
 * Returns all Categories linked to the given Item.
 * 
 * Parameter:
 * - id 
 * OR
 * - item
 *  
 * - assign
 * - itemtype
 */
function smarty_function_item_category($params, &$smarty)
{
	if(!isset($params['id']) && !isset($params['item'])) {
		$smarty->trigger_error("item_category: missing 'id' AND 'item' attribute");
		return;
	}

	if(!isset($params['assign'])) {
		$smarty->trigger_error("item_category: missing 'assign' attribute");
		return;
	}
	
	// one of ID and item must be set!
	$type = isset($params['itemtype']) ? $params['itemtype'] : _BIGACE_ITEM_MENU;
	$id = isset($params['id']) ? $params['id'] : $params['item']->getID();
	
	$ice = new ItemCategoryEnumeration($type, $id);
	
	$cats = array();
	
	while($ice->hasNext()) {
		$cats[] = $ice->next();
	}

	$smarty->assign($params['assign'], $cats);
	
	return;
}
