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

import('classes.modul.Modul');

/**
 * Checks if the given modul is existing.
 * 
 * Parameter:
 * - name 		(required)
 * - version 	(optional)
 * - minversion (optional)
 * - assign 	(optional)
 */
function smarty_function_modul_check($params, &$smarty)
{	
	if(!isset($params['name'])) {
		$smarty->trigger_error("modul_check: missing 'name' attribute");
		return;
	}
	
	$name = $params['name'];
	$exists = false;
	
	if(file_exists($GLOBALS['_BIGACE']['DIR']['modul'] . $name) && is_dir($GLOBALS['_BIGACE']['DIR']['modul'] . $name)) {
		
		// directory exists, if no version check is requested, we assume the modul exists
		if(!isset($params['version']) && !isset($params['minversion']))
			$exists = true;
		else {
			$mod = new Modul($name);
			if(isset($params['version']))
				$exists = version_compare($mod->getVersion(), $params['version']);
			else if (isset($params['minversion'])) 
				$exists = version_compare($mod->getVersion(), $params['minversion'], '>=');
		}	
	}
    
	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $exists);
	}
	else {
		return $exists;
	}
}
