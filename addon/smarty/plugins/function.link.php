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
 * Creates an URL for an Item or ID.
 * 
 * Parameter:
 * - item
 * - url
 * - id
 * - command
 * - language
 * - assign
 * - parameter
 */
function smarty_function_link($params, &$smarty)
{
	if(!isset($params['item'])) {
		if(!isset($params['url']) && (!isset($params['id']) && !isset($params['command']))) {
			$smarty->trigger_error("link: missing 'id' and 'command' or 'item' attribute");
			return;
		}
	}
	
	if(isset($params['item'])) {
		$link = LinkHelper::getCMSLinkFromItem($params['item']);
		if($params['item']->getItemtypeID() == _BIGACE_ITEM_MENU)
			$link->setCommand(_BIGACE_CMD_MENU);
	} 
	else {
		$link = new CMSLink();
		if(isset($params['url']))
			$link->setUniqueName($params['url']);
		else {
			$link->setItemID($params['id']);
			$link->setCommand($params['command']);
		}
	}

	if(isset($params['language']))
		$link->setLanguageID($params['language']);

	if(isset($params['parameter'])) {
		$params = explode(',', $params['parameter']);
		foreach($params AS $p) {
			$att = explode('=', $p);
			$link->addParameter($att[0], $att[1]);
		}
	}

	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], LinkHelper::getUrlFromCMSLink($link));
		return;
	}
	
	return LinkHelper::getUrlFromCMSLink($link);
}
