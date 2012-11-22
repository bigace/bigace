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
 * Checks if "parent" is a parent (somewhere in the tree above) the "test" item.
 * 
 * Parameter:
 * - parent   = the Item ID to check for children
 * - test     = the Item ID we search below "parent"
 * - assign   = the template variable to assign the result to
 * - itemtype = the itemtype to perform this check for (default is MENU)
 */
function smarty_function_is_parent($params, &$smarty)
{	
	if(!isset($params['assign']) || !isset($params['parent']) || !isset($params['test'])) {
		$smarty->trigger_error("is_parent: the attributes 'assign', 'parent' and 'test' are required");
		return;
	}

    $it = isset($params['itemtype']) ? $params['itemtype'] : _BIGACE_ITEM_MENU;
    $is = new ItemService($it);
    $result = $is->isChildOf($params['parent'], $params['test']);

	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $result);
		return;
	}
	return $result;
}
