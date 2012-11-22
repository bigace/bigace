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
 * Shows a Skype Javascript, that displays the Status of the configured Person.
 * The person must enable "Share State on the Web" in their privacy.
 * See http://www.skype.com/share/buttons/ for further information.
 * 
 * Parameter:
 * - uid	= the Skype User ID (required!)
 * - link	= 
 * - mode	= 
 * - title	= 
 */
function smarty_function_skype_online($params, &$smarty)
{
	if(!isset($params['uid'])) {
		$smarty->trigger_error("skype_online: missing 'uid' attribute");
		return;
	}
	$uid = $params['uid'];
	$link = isset($params['link']) ? ((bool)$params['uid']) : true;
	$mode = isset($params['mode']) ? $params['mode'] : 'call';
	$title = isset($params['title']) ? $params['title'] : $uid;
	
	echo '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>';
    if($link)
    	echo '<a href="skype:'.$uid.'?'.$mode.'">';
	echo '<img src="http://mystatus.skype.com/balloon/'.$uid.'" style="border: none;" width="150" height="60" alt="'.$title.'" /></a>';
}
