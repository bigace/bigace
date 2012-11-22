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

/**
 * Returns the username or assigns all user values.
 * 
 * Parameter:
 * - name	(required) the username to lookup
 * - id		(required) the userID to lookup
 * - assign	(optional) name of the variable to assign values to 
 */
function smarty_function_user($params, &$smarty)
{
	if(!isset($params['name']) && !isset($params['id'])) {
		$smarty->trigger_error("user: missing 'name' or 'id' attribute");
		return;
	}
	$services = ServiceFactory::get();
	$principalService = $services->getPrincipalService();
	if(isset($params['name'])) {
		$principal = $principalService->lookup($params['name']);
	}
	else if(isset($params['id'])) {
		$principal = $principalService->lookupByID($params['id']);
	}
	
	if(!isset($params['assign']))
		return $principal->getName();
	
	$smarty->assign($params['assign'], $principal);
}
