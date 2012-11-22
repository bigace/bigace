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


import('classes.util.links.LogoutLink');
import('classes.util.LinkHelper');

/**
 * Returns the URL to logout a User.
 * 
 * Parameter:
 * - id
 * - language
 * - cmd
 */
function smarty_function_link_logout($params, &$smarty)
{
    $link = new LogoutLink();
    if(isset($params['id']))
    	$link->setItemID($params['id']);
    if(isset($params['language']))
    	$link->setLanguageID($params['language']);
    
    if(isset($params['cmd']))
    	$link->setRedirectCommand($params['cmd']);
    
    return LinkHelper::getUrlFromCMSLink($link);
}
