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

import('classes.item.ItemService');

/**
 * Checks if the given item is a leaf.
 * 
 * Parameter:
 * - item	=
 * - id		=
 * - assign =
 */
function smarty_function_is_leaf($params, &$smarty)
{	
	if(!isset($params['item']) && isset($params['id'])) {
		$smarty->trigger_error("is_leaf: neither 'item' not 'id' attribute is set");
		return;
	}
	
	$item = (isset($params['item']) ? $params['item'] : null);
	
	$isLeaf = false;
	
	if(!is_null($item) && $item->getItemtypeID() == _BIGACE_ITEM_MENU && isset($GLOBALS['MENU_SERVICE'])) {
		$isLeaf = $GLOBALS['MENU_SERVICE']->isLeaf($item->getID());
	}
	else {
		if(isset($params['item'])) {
			$service = new ItemService($item->getItemtypeID());
			$isLeaf = $service->isLeaf($item->getID());
		}
		else { 
			$service = new ItemService(_BIGACE_ITEM_MENU);
			$isLeaf = $service->isLeaf($params['id']);
		}
	}

	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $isLeaf);
		return;
	}
	return $isLeaf;
}
