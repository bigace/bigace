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

if(!defined('_BIGACE_FILEMANAGER')) die('An error occured.');

require_once(_BIGACE_DIR_ADMIN.'styling.php');


function render_thumbnails($items)
{
}

function render_listing($itemtype, $items, $folder = true, $languageChooser = true)
{
    if(!is_array($items) || count($items) == 0)
    {
        echo '<b>'.getTranslation('no_items_available_'.$itemtype, getTranslation('no_items_available')).'</b>';
        return;
    }

    $tpl = getTemplateService();

    $cssClass = "row1";
    $entries = array();
    $wayhome = '';

    $i = 0;
    foreach($items AS $item)
    {
        $folder = "";
        if($itemtype == _BIGACE_ITEM_MENU)
        {
            if($wayhome == null && $item->getID() != _BIGACE_TOP_LEVEL)
            {
                $titem = $item->getParent();
                $name = '<b>'.$titem->getName().'</b>';
                $parent = $titem->getParent();
                $wayhome = '<a href="'.getFilemanagerUrl('by_itemtype.php', array('itemtype'=>'1', 'selectedID' => $titem->getID())).'">' . $name . '</a>';
                if ($titem->getID() != _BIGACE_TOP_LEVEL)
                {
                    $wayhome = '<a href="'.getFilemanagerUrl('by_itemtype.php', array('itemtype'=>'1', 'selectedID' => $parent->getID())).'">' . $parent->getName() .'</a> &gt; ' . $wayhome;
                    while ($parent->getID() > _BIGACE_TOP_LEVEL) {
                        if($parent->getID() > _BIGACE_TOP_LEVEL)
                            $parent = $parent->getParent();
                        $wayhome = '<a href="'.getFilemanagerUrl('by_itemtype.php', array('itemtype'=>'1', 'selectedID' => $parent->getID())).'">' . $parent->getName() . '</a> &gt; ' . $wayhome;
                    }
                }
            }

            $extension = 'html';
            if ($i == 0) {
                if ($item->getID() > _BIGACE_TOP_LEVEL) {
                    $folder = '<a href="'.getFilemanagerUrl('by_itemtype.php', array('itemtype'=>'1', 'selectedID' => $item->getParentID())).'">..</a>';
                }
            } else if ($item->hasChildren()) {
                $folder = '<a href="'.getFilemanagerUrl('by_itemtype.php', array('itemtype'=>'1', 'selectedID' => $item->getID())).'"><img border="0" src="images/folder.png"></a>';
            }
            $i++;
        }
        else {
            $extension = getFileExtension(strtolower($item->getOriginalName()));
        }

        $uuu = LinkHelper::getCMSLinkFromItem($item);
        $uuu->setUseSSL(false);

        $entries[] = array(
            "FOLDER" => $folder,
            "CSS" => $cssClass,
            "ITEM" => $item,
            "ITEM_ID" => $item->getID(),
            "ITEM_LANGUAGE" => $item->getLanguageID(),
            "ITEM_TYPE" => $item->getItemtypeID(),
            "ITEM_NAME" => prepareJSName($item->getName()),
            "ITEM_URL" => LinkHelper::getUrlFromCMSLink($uuu),
            "ITEM_FILENAME" => $item->getOriginalName(),
            "ITEM_MIMETYPE" => $extension
        );

        $cssClass = ($cssClass == "row1") ? "row2" : "row1";
    }

    if($languageChooser) {
        $enum = new LanguageEnumeration();
        $a = $enum->count();
        $items = array();
        for($i=0; $i < $a; $i++) {
           $items[] = $enum->next();
        }
        $tpl->assign("LANGUAGES", $items);
        $tpl->assign("ITEM_LANGUAGE", $GLOBALS['language']);
    }

    $tpl->assign("ITEMS", $entries);

    if($itemtype == _BIGACE_ITEM_MENU) {
        $tpl->assign("WAYHOME", $wayhome);
        $tpl->display("FM_MenuListing.tpl");
    }
    else if($itemtype == _BIGACE_ITEM_IMAGE) {
        $tpl->display("FM_ImageListing.tpl");
    }
    else {
        $tpl->display("FM_ItemListing.tpl");
    }
}

function render_search($items)
{
}


function prepareJSName($str) {
    $str = htmlspecialchars($str);
    $str = str_replace('"', '&quot;', $str);
    //$str = addSlashes($str);
    //$str = str_replace("'", '\%27', $str);
    $str = str_replace("'", '&#039;', $str);
    return $str;
}

function showHtmlHeader()
{
    $lang = new Language($GLOBALS['_BIGACE']['SESSION']->getLanguageID());

    $ts = getTemplateService();
    $ts->assign("LANGUAGE", $lang);
    $ts->assign("CSS", $GLOBALS['_BIGACE']['style']['class']->getCSS());
    $ts->display('FM_BrowserHtmlHeader.tpl');
}


function showHtmlFooter()
{
    echo '

        </body>
    </html>
    ';
}