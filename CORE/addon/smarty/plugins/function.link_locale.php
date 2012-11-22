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

import('classes.util.links.SessionLanguageLink');
import('classes.util.LinkHelper');

/**
 * Creates an URL to switch the Session language.
 * 
 * Parameter:
 * - locale
 * - id
 */
function smarty_function_link_locale($params, &$smarty)
{
	if(!isset($params['locale'])) {
		$smarty->trigger_error("link_locale: missing 'locale' attribute");
		return;
	}
	$link = new SessionLanguageLink($params['locale']);
    if(isset($params['id']))
    	$link->setItemID($params['id']);
    return LinkHelper::getUrlFromCMSLink($link);
}
