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
 * BIGACE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 */

/**
* Error Page that should be used when anonymous User try to access functions
* that are accessible for logged in User only (like Administration).
* 
* TOSO find out where this is used.
*/


/**
 * This script displays a HTML representation of the 996 Error Code.
 * It sends a 403 Forbidden Header and displays a "No anonymous User allowed" screen.
 */

import('classes.util.LinkHelper');
import('classes.util.CMSLink');
import('classes.util.ApplicationLinks');
import('classes.util.links.LoginFormularLink');
import('classes.util.links.AuthenticateLink');

include_once(dirname(__FILE__) . '/error_environment.php');

header("HTTP/1.0 403 Forbidden");

$redirectLink = new LoginFormularLink();
$redirectLink->setRedirectID($GLOBALS['_BIGACE']['PARSER']->getItemID());
$redirectLink->setRedirectCommand($GLOBALS['_BIGACE']['PARSER']->getCommand());
$loginLink = LinkHelper::getUrlFromCMSLink($redirectLink); 

$authLink = new AuthenticateLink();
$authLink->setItemID($GLOBALS['_BIGACE']['PARSER']->getItemID());
$authURL = LinkHelper::getUrlFromCMSLink($authLink);

$tpl = loadErrorTemplate();

$tpl->setVariable('TITLE', '403 Forbidden - User access permitted');
$tpl->setVariable('ERROR_TITLE', 'Error: 403');
$tpl->setVariable('MESSAGE', 'Permission denied.');
$tpl->setVariable('LOGIN_URL', $loginLink);
$tpl->setVariable('AUTHENTICATE_URL', $authURL);
$tpl->setVariable('HOME_URL', ApplicationLinks::getHomeURL());

// TODO translate
if (!$GLOBALS['_BIGACE']['SESSION']->isAnonymous()) 
	$tpl->setVariable('EXTENDED_ERROR_HTML', 'You are not allowed to access this page.'); 
else
	$tpl->setVariable('EXTENDED_ERROR_HTML', 'Please login to access this page.');

$tpl->show();

?>