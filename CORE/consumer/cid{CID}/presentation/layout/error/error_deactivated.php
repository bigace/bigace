<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.<br>Copyright (C) Kevin Papst.
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
 * This script displays a HTML representation of the 401 Error Code.
 */

import('classes.util.ApplicationLinks');
include_once(dirname(__FILE__).'/error_environment.php');

header("HTTP/1.0 401 Unauthorized");

$id = _BIGACE_TOP_LEVEL;
if (isset($GLOBALS['_BIGACE']['PARSER'])) {
    $id = $GLOBALS['_BIGACE']['PARSER']->getItemID();
}

$tpl = loadErrorTemplate();

$tpl->setVariable('TITLE', '401 Unauthorized');
$tpl->setVariable('ERROR_TITLE', 'Error: 401');
$tpl->setVariable('MESSAGE', $exception->getMessage());
$tpl->setVariable('LOGIN_URL', ApplicationLinks::getLoginFormURL($id));
$tpl->setVariable('AUTHENTICATE_URL', ApplicationLinks::getLoginURL($GLOBALS['_BIGACE']['PARSER']->getItemID()));
$tpl->setVariable('HOME_URL', ApplicationLinks::getHomeURL());
$tpl->setVariable('EXTENDED_ERROR_HTML', 'This Account is deactivated, please contact your Administrator!');
$tpl->show();

?>