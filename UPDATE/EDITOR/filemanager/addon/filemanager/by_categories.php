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
import('classes.category.Category');
import('classes.category.CategoryService');

import('classes.menu.Menu');
import('classes.menu.MenuService');
import('classes.image.Image');
import('classes.image.ImageService');
import('classes.file.File');
import('classes.file.FileService');

$selectedCategory = (isset($_GET['showCatID']) ? $_GET['showCatID'] : null);

$selfLink = "by_categories.php?itemtype=".$itemtype.'&'.$parameter;

showHtmlHeader();

if($selectedCategory == null)
{
    $categoryID = (isset($_GET['catID']) ? $_GET['catID'] : _BIGACE_TOP_LEVEL);

    $tpl = getTemplateService();
    $tpl->assign("LISTING_WIDTH", '100%');

    $catService = new CategoryService();
    $category = $catService->getCategory($categoryID);

    $cssClass = "row1";

    // Current Category
    $name = '<b>'.$category->getName().'</b>';
    $parent = $category->getParent();

    // show wayhome unless we are top-level
    if($category->getID() != _BIGACE_TOP_LEVEL) {
        $wayhome = '<a href="'.$selfLink.'&catID=' . $category->getID().'">' . $name . '</a>';
        if ($category->getID() != _BIGACE_TOP_LEVEL)
        {
            $wayhome = '<a href="'.$selfLink.'&catID=' . $parent->getID().'">' . $parent->getName() .'</a> &gt; ' . $wayhome;
            while ($parent->getID() > _BIGACE_TOP_LEVEL) {
                if($parent->getID() > _BIGACE_TOP_LEVEL)
                    $parent = $parent->getParent();
                $wayhome = '<a href="'.$selfLink.'&catID=' . $parent->getID().'">' . $parent->getName() . '</a> &gt; ' . $wayhome;
            }
        }

        $tpl->assign("WAYHOME", $wayhome);
    }

    $catEnum = $catService->getItemsForCategory($itemtype, $category->getID());

    $tlink = '';
    if($catEnum->count() > 0)
        $tlink = '<a href="'.$selfLink.'&catID=' . $category->getID().'">'.getTranslation('show_linked').'</a>';

    $url = "";
    if ($category->getID() != _BIGACE_TOP_LEVEL)
        $url = $selfLink.'&catID='.$category->getParentID();

    $entries = array();
    if($category->getID() != _BIGACE_TOP_LEVEL) {
        $entries[] = array(
            'CSS' => $cssClass,
            "CATEGORY_CHILD_URL" => "",
            "CATEGORY_PARENT_URL" => $url,
            "CATEGORY_NAME" => $name,
            "ACTION_LINKED" => $tlink,
            "AMOUNT" => $catEnum->count()
        );
    }

    $enum = $category->getChilds();
    $val = $enum->count();

    for ($i = 0; $i < $val; $i++)
    {
        $cssClass = ($cssClass == "row1") ? "row2" : "row1";

        $temp = $enum->next();
        $name = $temp->getName();
        $url = "";
        if ($temp->hasChilds()) { // category
            $url = $selfLink.'&catID='.$temp->getID();
        }

        $tlink = '';

        // count menus
        if($itemtype == _BIGACE_ITEM_MENU)
        {
            $catEnum = $catService->getItemsForCategory(_BIGACE_ITEM_MENU, $temp->getID());
            if($catEnum->count() > 0)
                $tlink .= ' <a class="preview" href="'.$selfLink.'&showCatID='. $temp->getID().'">'.$catEnum->count().' '.getTranslation('cat_menu').'</a>';
            else
                $tlink .= ' ' . $catEnum->count().' '.getTranslation('cat_menu');
        }

        // count images
        if($itemtype == _BIGACE_ITEM_IMAGE)
        {
            $catEnum = $catService->getItemsForCategory(_BIGACE_ITEM_IMAGE, $temp->getID());
            if($catEnum->count() > 0)
                $tlink .= ' <a class="preview" href="'.$selfLink.'&showCatID='. $temp->getID().'">'.$catEnum->count().' '.getTranslation('cat_image').'</a>';
            else
                $tlink .= ' ' . $catEnum->count().' '.getTranslation('cat_image');
        }

        // count files
        if($itemtype == _BIGACE_ITEM_FILE)
        {
            $catEnum = $catService->getItemsForCategory(_BIGACE_ITEM_FILE, $temp->getID());
            if($catEnum->count() > 0)
                $tlink .= ' <a class="preview" href="'.$selfLink.'&showCatID='. $temp->getID().'">'.$catEnum->count().' '.getTranslation('cat_file').'</a>';
            else
                $tlink .= ' ' . $catEnum->count().' '.getTranslation('cat_file');
        }

        $entries[] = array(
            'CSS' => $cssClass,
            "CATEGORY_CHILD_URL" => $url,
            "CATEGORY_PARENT_URL" => "",
            "CATEGORY_NAME" => $name,
            "ACTION_LINKED" => $tlink,
            "AMOUNT" => $catEnum->count()
        );
    }

    $tpl->assign("entries", $entries);
    $tpl->display("FM_CategoryMenu.tpl");
}
else
{
    $items = array();
    $itemGetter = new Itemtype($itemtype);
    $catService = new CategoryService();
    $catEnum = $catService->getItemsForCategory($itemtype, $selectedCategory);
    for($i=0; $i < $catEnum->count(); $i++)
    {
        $temp = $catEnum->next();
        $items[] = $itemGetter->getClass($temp['itemid']);
    }

    render_listing($itemtype,$items);
}

showHtmlFooter();