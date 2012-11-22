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

import('classes.comments.TrackbackLink');

/**
 * Creates an trackback URL to the given item.
 * 
 * Parameter:
 * - item
 * - assign
 */
function smarty_function_link_trackback($params, &$smarty)
{
	if(!isset($params['item'])) {
		$smarty->trigger_error("link_trackback: missing 'item' attribute");
		return;
	}	

    $link = new TrackbackLink($params['item']);

	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], LinkHelper::getUrlFromCMSLink($link));
		return;
	}	
    
    return LinkHelper::getUrlFromCMSLink($link);
}
