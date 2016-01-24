<?php
//
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS for MySQL                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) Kevin Papst                                              |
// | Web           http://www.bigace.de                                     |
// | Mirror        http://bigace.sourceforge.net/                           |
// | Sourceforge   http://sourceforge.net/projects/bigace/                  |
// +------------------------------------------------------------------------+
// | This source file is subject to version 2 or (at your option) any later |
// | version, of the GNU General Public License as published by the Free    |
// | Software Foundation, available at:                                     |
// | http://www.gnu.org/licenses/gpl.html                                   |
// +------------------------------------------------------------------------+
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// +------------------------------------------------------------------------+
//

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

/**
 * This command performs a logout for the current User and destroys its session.
 * The Session data will not be available afterwards, even if you know the Session ID.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.command
 */

import('classes.right.RightService');
import('classes.util.LinkHelper');
import('classes.util.CMSLink');
import('classes.logger.LogEntry');

try {
$le = new LogEntry(LOGGER_LEVEL_INFO,'User "'.$GLOBALS['_BIGACE']['SESSION']->getUser()->getName().'" logged out',LOGGER_NAMESPACE_AUTHENTICATION);
$GLOBALS['LOGGER']->logEntry($le);
} catch (Exception $e) {}

// kill session and send header afterwards to clear everything
$GLOBALS['_BIGACE']['SESSION']->destroy();
/*
unset($GLOBALS['_SERVER']['PHP_AUTH_USER']);
unset($GLOBALS['_SERVER']['PHP_AUTH_PW']);
header ('HTTP/1.0 401 Unauthorized');
*/

$ID = $GLOBALS['_BIGACE']['PARSER']->getItemID();

$RIGHT_SERVICE = new RightService();
$RIGHT = $RIGHT_SERVICE->getMenuRight( _AID_ , $ID );
if (!$RIGHT->canRead()) {
    $ID = _BIGACE_TOP_LEVEL;
}
unset($RIGHT_SERVICE);

if(isset($_GET['REDIRECT_CMD'])) {
	$link = new CMSLink();
	$link->setItemID($ID);
	$link->setCommand($_GET['REDIRECT_CMD']);
}
else {
	import('classes.menu.MenuService');
	$ms = new MenuService();
	$menu = $ms->getMenu($ID, $GLOBALS['_BIGACE']['PARSER']->getLanguage());
	$link = LinkHelper::getCMSLinkFromItem($menu);
}

header("Location: " . LinkHelper::getUrlFromCMSLink($link));

exit;

?>