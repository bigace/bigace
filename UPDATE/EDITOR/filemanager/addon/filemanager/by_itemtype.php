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
 * @package bigace.addon.filemanager
 */
require_once(dirname(__FILE__).'/environment.php');
require_once(dirname(__FILE__).'/listings.php');
if(!defined('_BIGACE_FILEMANAGER')) die('An error occured.');
if($itemtype == null) die('No Itemtype selected.');

import('classes.util.IOHelper');

import('classes.util.CMSLink');
import('classes.util.LinkHelper');

import('classes.item.ItemRequest');
import('classes.item.SimpleItemTreeWalker');

import('classes.menu.Menu');
import('classes.menu.MenuService');
import('classes.image.Image');
import('classes.image.ImageService');
import('classes.file.File');
import('classes.file.FileService');

$selectedID = (isset($_GET['selectedID']) ? $_GET['selectedID'] : null);
if($itemtype == _BIGACE_ITEM_MENU && is_null($selectedID))
    $selectedID = _BIGACE_TOP_LEVEL;

$selfLink = "by_itemtype.php?itemtype=".$itemtype.'&'.$parameter;

$items = array();

// add parent for menus, so user can choose it and navigate back
if($itemtype == _BIGACE_ITEM_MENU) {
    $service = new MenuService();
    $topLevel = $service->getMenu($selectedID, $language);
    $items[] = $topLevel;
}


$req = new ItemRequest($itemtype, $selectedID);
$req->setLanguageID($language);
$req->setTreetype(ITEM_LOAD_LIGHT);
$req->setOrderBy(ORDER_COLUMN_POSITION);
$req->setOrder($req->_ORDER_ASC);
$req->setFlagToExclude($req->FLAG_ALL_EXCEPT_TRASH);
$itemWalker = new SimpleItemTreeWalker($req);

$a = $itemWalker->count();

for ($i=0; $i < $a; $i++)
{
    $temp = $itemWalker->next();
    $items[] = $temp;
}

showHtmlHeader();
render_listing($itemtype,$items);
showHtmlFooter();