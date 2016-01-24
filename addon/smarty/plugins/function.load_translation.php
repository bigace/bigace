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

import('classes.util.Translations');

/**
 * Load a translation file either into the global or given namespace.
 * 
 * Parameter:
 * - name
 * - locale
 * - namespace
 * - directory
 */
function smarty_function_load_translation($params, &$smarty)
{
	if(!isset($params['name'])) {
		$smarty->trigger_error("load_translation: missing 'name' attribute");
		return;
	}	

	$name = $params['name'];
	$locale = (isset($params['locale']) ? $params['locale'] : _ULC_);
	$directory = (isset($params['directory']) ? $params['directory'] : null);

	if(isset($params['namespace'])) {
		Translations::load($name,$locale,$params['namespace'],$directory);
	} else {
		Translations::loadGlobal($name,$locale, $directory);
	}	
}
