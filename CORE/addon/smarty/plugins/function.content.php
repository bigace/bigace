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

import('classes.menu.ContentService');

/**
 * Load additional content for a menu.
 *
 * Parameter:
 * item			= the menu to fetch the content for (or 'id' and 'language')
 * id 			=
 * language 	=
 * target 		= target media, might be rendered different in the future
 * assign		= name of the template variable to be assigned to
 * name			=
 */
function smarty_function_content($params, &$smarty)
{
	// will not work with missing name
	if(!isset($params['name'])) {
		$smarty->trigger_error("content: missing 'name' attribute");
		return;
	}
	// or item attributes
	if(!isset($params['item'])) {
		if(!isset($params['id']) || !isset($params['language'])) {
			$smarty->trigger_error("content: missing 'item' or 'id' and 'language' attributes");
			return;
		}
	}

	$id = (isset($params['item']) ? $params['item']->getID() : $params['id']);
	$lang = (isset($params['item']) ? $params['item']->getLanguageID() : $params['language']);
	$name = $params['name'];

	$html = null;

	// whether we return the saved content or content for a different target media
	if(isset($params['target'])) {
    	$html = get_content($id,$lang,$name,$params['target'],$smarty);
	}
	else {
	    $html = get_content($id,$lang,$name,$smarty);
	}

	// if designer wants it to be assigned, do him that favour ;)
	if (isset($params['assign'])) {
		$smarty->assign($params['assign'], $html);
		return;
	}

	return $html;
}
