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


	require_once(dirname(__FILE__).'/public/index.php');

	// If you want to redirect to your top level menu instead of diplaying it directly, 
	// uncomment the next lines.
	
/*
	// Redirect to your TOP_LEVEL Page
	require_once(dirname(__FILE__).'/system/libs/init_session.inc.php');

	import('classes.util.LinkHelper');
	import('classes.menu.MenuService');
	$service = new MenuService();
	$topLevel = $service->getMenu(_BIGACE_TOP_LEVEL, $GLOBALS['_BIGACE']['DEFAULT_LANGUAGE']);
	$link = LinkHelper::getCMSLinkFromItem($topLevel);

	$urlToRedirect = LinkHelper::getUrlFromCMSLink($link);
	header("Location: " . $urlToRedirect); 
*/
