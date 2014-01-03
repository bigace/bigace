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
 * Returns the Captcha Code to be displayed, or validates input values.
 *
 * Parameter:
 * ==========
 * validate = name of the parameter of the image to be evaluated
 * value = the value to be validated
 * assign = depends on validate or create
 */
function smarty_function_captcha($params, &$smarty)
{
	$captcha = ConfigurationReader::getValue("system", "captcha", null);
	if($captcha == null) {
		$smarty->trigger_error("captcha: wrong configuration 'system/captcha'");
		return;
	}
	
	// captcha should be validated
	if(isset($params['validate'])) {
		if(!isset($params['value'])) {
			$params['value'] = null;
		}

		$value = false;	
	   	switch($captcha->validate($params['validate'],$params['value'])) {
   			case 1:
   				$value = true;
   				break;			
        }
		if(isset($params['assign'])) {
			$smarty->assign($params['assign'], $value);
		}
		return $value;
	}	
	
    $captchaCode = $captcha->get();
    
	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $captchaCode);
	}
	return $captchaCode;
}
