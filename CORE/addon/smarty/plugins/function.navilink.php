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

import('classes.util.SmartyLink');
import('classes.util.LinkHelper');

/**
 * DEPRECATED!!!
 * 
 * Use {link} instead!
 * 
 * Creates an URL to browse the Website with Smarty.
 * 
 * Parameter:
 * - item
 * - id
 * - language
 * - template
 * 
 * @deprecated do not use anymore, use {link} instead!
 */
function smarty_function_navilink($params, &$smarty)
{
	if(!isset($params['item']) && !isset($params['id'])) {
		$smarty->trigger_error("navilink: missing 'id' or 'item' attribute");
		return;
	}
	
	if(isset($params['item'])) {
		$link = LinkHelper::getCMSLinkFromItem($params['item']);
		$link->setCommand('smarty');
	} 
	else {
		$link = new SmartyLink();
		$link->setItemID($params['id']);
	}

	if(isset($params['language']))
		$link->setLanguageID($params['language']);

	if(isset($params['template']))
		$link->setAction($params['template']);

	return LinkHelper::getUrlFromCMSLink($link);
}
