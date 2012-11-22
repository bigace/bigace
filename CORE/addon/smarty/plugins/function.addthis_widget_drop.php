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
 * AddThis Social Bookmarking Widget (drop-down)
 * Help your visitor promote your website or blog. 
 * Put the AddThis Social Bookmarking Widget on your site or blog, so any visitor can easily bookmark it. 
 * The widget works with all popular bookmarking services.
 * See http://www.addthis.com for further information.
 * 
 * Parameter:
 * 'username' = for statistic purpose
 * 'link'     = the URL to be saved
 * 'title     = name for the link
 */
function smarty_function_addthis_widget_drop($params, &$smarty)
{
	$uid = isset($params['username']) ? $params['username'] : "";
	$link = isset($params['link']) ? $params['link'] : get_permalink();
	$title = isset($params['title']) ? $params['title'] : $GLOBALS['MENU']->getName();

    $badge  = "<script type=\"text/javascript\">\n";
    $badge .= "  addthis_url    = '".urlencode($link)."';\n";   
    $badge .= "  addthis_title  = '".urlencode($title)."';\n"; 
    $badge .= "  addthis_pub    = '$uid';\n";
    $badge .= "</script><script type=\"text/javascript\" src=\"http://s7.addthis.com/js/addthis_widget.php?v=12\" ></script>\n";

	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $badge);
		return;
	}
	
	return $badge;
}
