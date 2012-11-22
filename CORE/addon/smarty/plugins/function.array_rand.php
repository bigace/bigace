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
 * Same like array_rand but for Smarty.
 */
function smarty_function_array_rand($params, &$smarty)
{
	if(!isset($params['var'])) {
		$smarty->trigger_error("array_rand: missing 'var' attribute");
		return;
	}
	
	
	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], array_rand($params['var']));
		return;
	}

	return array_rand($params['var']);
}
