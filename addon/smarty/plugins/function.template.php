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

import('classes.smarty.SmartyTemplate');

/**
 * Returns the full URL to the given Template OR if the 
 * assign parameter is passed, the SmartyTemplate object.
 *  
 * Parameter:
 * - name 	= (required) the Template name
 * - assign = (optional) name of the smarty variable to assign to
 */
function smarty_function_template($params, &$smarty)
{
	if(!isset($params['name'])) {
		$smarty->trigger_error("template: missing 'name' attribute");
		return;
	}	
	
	$template = new SmartyTemplate($params['name']);

	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $template);
		return;
	}
	
	return $template->getFilename();
}
