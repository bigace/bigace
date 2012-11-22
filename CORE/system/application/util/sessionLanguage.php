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

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

/**
 * This Application changes the Session Language and redirects to the Menu with the ID:
 * <code>$GLOBALS['_BIGACE']['PARSER']->getItemID()</code>
 *
 * The language will be switched to:
 * <code>$GLOBALS['_BIGACE']['PARSER']->getLanguage()</code>
 */

// import required classes
import('classes.util.CMSLink');
import('classes.util.LinkHelper');
import('classes.item.MasterItemType');
import('classes.item.ItemService');
import('classes.menu.MenuService');

// switch the language
$language = extractVar('LANGID',$GLOBALS['_BIGACE']['PARSER']->getLanguage());
$id = $GLOBALS['_BIGACE']['PARSER']->getItemID();
$GLOBALS['_BIGACE']['SESSION']->setLanguage( $language );

$cmd = extractVar('returnCmd','');
$item = null;

if($cmd != '')
{
	$itemtype = new MasterItemType();
	$it = $itemtype->getItemTypeForCommand($cmd);
	$service = new ItemService($it);
	$item = $service->getItem($id, ITEM_LOAD_FULL, $language);
}
else
{
	$service = new MenuService();
	$item = $service->getMenu($id, $language);
}

header("HTTP/1.1 301 Moved Permanently");
header( "Location: " . LinkHelper::getUrlFromCMSLink( LinkHelper::getCMSLinkFromItem($item) ));
exit;
