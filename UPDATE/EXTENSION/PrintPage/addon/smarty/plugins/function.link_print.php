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

import('classes.util.CMSLink');
import('classes.util.LinkHelper');

/**
 * Creates an URL to print a page.
 * 
 * Parameter:
 * - item
 * - language
 * - assign
 * - parameter
 * - template
 */
function smarty_function_link_print($params, &$smarty)
{
	if(!isset($params['item'])) {
		$smarty->trigger_error("link_print: missing 'item' attribute");
		return;
	}
	
	if($params['item']->getItemtypeID() != _BIGACE_ITEM_MENU) {
		$smarty->trigger_error("link_print: attribute 'item' must be a MENU");
		return;
	}
	
	$link = LinkHelper::getCMSLinkFromItem($params['item']);
	
	if($params['item']->getItemtypeID() == _BIGACE_ITEM_MENU)
		$link->setCommand(_BIGACE_CMD_MENU);
		
	if(isset($params['language']))
		$link->setLanguageID($params['language']);

	if(isset($params['template']))
		$link->setSubAction($params['template']);
    else
		$link->setSubAction('PRINT-PAGE');
    
	if(isset($params['parameter'])) {
		$params = explode(',', $params['parameter']);
		foreach($params AS $p) {
			$att = explode('=', $p);
			$link->addParameter($att[0], $att[1]);
		}
	}

	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], LinkHelper::getUrlFromCMSLink($link, array(), false));
		return;
	}
	
	return LinkHelper::getUrlFromCMSLink($link, array(), false);
}