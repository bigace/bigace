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

import('classes.util.links.AuthenticateLink');
import('classes.util.LinkHelper');

/**
 * Returns the URL to perform a Login.
 * 
 * Parameter:
 * - id
 * - language
 */
function smarty_function_link_auth($params, &$smarty)
{
	$link = new AuthenticateLink();

	if(isset($params['id']))
    	$link->setItemID($params['id']);
    if(isset($params['language']))
    	$link->setLanguageID($params['language']);
    
    return LinkHelper::getUrlFromCMSLink($link);
}
