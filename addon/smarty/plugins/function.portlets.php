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
 * Loads portlets.
 * 
 * Parameter: 
 * - assign   = the Variable Name that is used to bind Portlets in Smarty Context
 * - name 	= the Name of the Portlet Column (comma separated list, default: loads all Portlets for Item/Language)
 * - lang 	= the language to fetch Portlets for (default: current language)
 * - id 		= the Menu ID to fetch Portlets for (default: current Menu ID)
 */
function smarty_function_portlets($params, &$smarty)
{
	if(!isset($params['assign'])) {
		$smarty->trigger_error("portlets: missing 'assign' attribute");
		return;
	}	
	$id = (isset($params['id']) ? $params['id'] : $GLOBALS['_BIGACE']['PARSER']->getItemID());	
	$lang = (isset($params['lang']) ? $params['lang'] : $GLOBALS['_BIGACE']['PARSER']->getLanguage());	
	$name = (isset($params['name']) ? $params['name'] : null);

	$portlets = array();
	$services = ServiceFactory::get();
	$portletService = $services->getService('portlet');

	if($name != null) {
		$name = explode(',', $name);
		foreach($name  AS $pkey)
			$portlets[$pkey] = $portletService->getPortlets(_BIGACE_ITEM_MENU, $id, $lang, $pkey);
	}
	else {
		$portlets = $portletService->getPortlets(_BIGACE_ITEM_MENU, $id, $lang);
	}
	$smarty->assign_by_ref($params['assign'], $portlets);
	return;
}
