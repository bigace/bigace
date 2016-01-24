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

import('classes.item.ItemProjectService');

/**
 * Smarty plugin to fetch project item values.
 * 
 * Parameter:
 * - assign   = the variable name that is used to bind result in smarty context
 * - item 	= the item to fetch project values for 
 * - type		= "text" or "num" (default: "text")
 * - name 	= the name of the project value (if not set all parameter are returned) 
 * - default  = value returned if found project field with name "name" is not found (default: null). not evaluated if name is missing.
 */
function smarty_function_project_field($params, &$smarty)
{
	if(!isset($params['assign'])) {
		$smarty->trigger_error("project_field: missing 'assign' attribute");
		return;
	}	
	if(!isset($params['item'])) {
		$smarty->trigger_error("project_field: missing 'item' attribute");
		return;
	}
	
	$item = $params['item'];
	$type = isset($params['type']) ? $params['type'] : "text";
	$name = isset($params['name']) ? $params['name'] : "";
	$default = isset($params['default']) ? $params['default'] : null;
	
    $ips = new ItemProjectService(_BIGACE_ITEM_MENU);

    if(empty( $name ))
    {
	    if($type == "text")
	    	$result = $ips->getAllText($item->getID(), $item->getLanguageID());
    	else
	    	$result = $ips->getAllNum($item->getID(), $item->getLanguageID());
    }
	else 
	{
	    if($type == "text")
	    	$result = $ips->getProjectText($item->getID(), $item->getLanguageID(), $name, $default);
	    else
	    	$result = $ips->getProjectNum($item->getID(), $item->getLanguageID(), $name, $default);
	}

	$smarty->assign($params['assign'], $result);
	return;
}
