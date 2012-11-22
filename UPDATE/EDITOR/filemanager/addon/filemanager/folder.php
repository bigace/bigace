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
if(!defined('_BIGACE_FILEMANAGER')) die('An error occured.');

import('classes.menu.Menu');
import('classes.menu.MenuService');

$selectedID = ((defined('GALLERY_PARENT') && !is_null(GALLERY_PARENT)) ? GALLERY_PARENT : _BIGACE_TOP_LEVEL);
$ms = new MenuService();
$mm = $ms->getMenu($selectedID,_ULC_);

require_once(_BIGACE_DIR_ADMIN.'styling.php');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="stylesheet" href="<?php echo $GLOBALS['_BIGACE']['style']['class']->getCSS(); ?>" type="text/css">
        <style type="text/css">
        h1 {
            font-size: 14px;
            text-decoration:none;
            font-weight:bold;
        }
        ul {
            margin:0px;
            padding-right:0px;
            padding-left:25px;
            list-style-position:outside;
            list-style-image:url(images/bullet_go.png)
        }
        body {
            margin:10px;
        }
        </style>
        <script type="text/javascript">
            function changeLanguage(selectBox) {
                var locale = selectBox.options[selectBox.selectedIndex].value;
                var allLinks = document.getElementsByTagName('a');
                for(var i = 0; i < allLinks.length; i++) {
                    allLinks[i].href = allLinks[i].href.replace(/&language=..&/, "&language="+locale+"&");
                }
            }
        </script>
    </head>
    <body class="FileArea">
    <select name="changeLanguage" onchange="changeLanguage(this)">
    <?php
       $enum = new LanguageEnumeration();
       $a = $enum->count();
       for($i=0; $i < $a; $i++) {
           $temp = $enum->next();
           echo '<option value="'.$temp->getLocale().'"';
           if($temp->getLocale() == $language) {
               echo ' selected';
           }
           echo '>'.$temp->getName(_ULC_).'</option>';
       }
    ?>
    </select>
    <?php
        if(defined('GALLERY_PARENT') && !is_null(GALLERY_PARENT)) {
            echo '<h1>'.getTranslation('dashboard').'</h1>';
            if($itemtype == null)
                echo '<ul><li><a target="main" href="by_parent.php?'.$parameter.'">'.getTranslation('dashboard_all').'</a></li></ul>';
            else
                echo '<ul><li><a target="main" href="by_parent.php?itemtype='.$itemtype.'&'.$parameter.'">'.getTranslation('dashboard_all').'</a></li></ul>';
        }

        if($allow_menu_browsing || $allow_menu_categories || $allow_menu_search)
        {
            echo '<h1>'.getTranslation('item_1').'</h1>';
            echo '<ul>';
            if($allow_menu_browsing) {
                echo '<li><a target="main" href="by_itemtype.php?itemtype=1&'.$parameter.'">'.getTranslation('choose_menu').'</a></li>';
            }
            if($allow_menu_categories) {
                echo '<li><a target="main" href="by_categories.php?itemtype=1&'.$parameter.'">'.getTranslation('category_menu').'</a></li>';
            }
            if($allow_menu_search) {
                echo '<li><a target="main" href="search.php?itemtype=1&'.$parameter.'">'.getTranslation('search_menu').'</a></li>';
            }
            echo '</ul>';
        }

        if($allow_image_browsing || $allow_image_categories || $allow_image_search || $allow_image_upload)
        {
            echo '<h1>'.getTranslation('item_4').'</h1>';
            echo '<ul>';
            if($allow_image_browsing) {
                echo '<li><a target="main" href="by_itemtype.php?itemtype=4&'.$parameter.'">'.getTranslation('choose_image').'</a></li>';
            }
            if($allow_image_categories) {
                echo '<li><a target="main" href="by_categories.php?itemtype=4&'.$parameter.'">'.getTranslation('category_menu').'</a></li>';
            }
            if($allow_image_search) {
                echo '<li><a target="main" href="search.php?itemtype=4&'.$parameter.'">'.getTranslation('search_image').'</a></li>';
            }
            if($allow_image_upload) {
                echo '<li><a target="main" href="upload.php?itemtype=4&'.$parameter.'">'.getTranslation('upload_image').'</a></li>';
            }
            echo '</ul>';
        }

        if($allow_file_browsing || $allow_file_categories || $allow_file_search || $allow_file_upload)
        {
            echo '<h1>'.getTranslation('item_5').'</h1>';
            echo '<ul>';
            if($allow_file_browsing) {
                echo '<li><a target="main" href="by_itemtype.php?itemtype=5&'.$parameter.'">'.getTranslation('choose_file').'</a></li>';
            }
            if($allow_file_categories) {
                echo '<li><a target="main" href="by_categories.php?itemtype=5&'.$parameter.'">'.getTranslation('category_menu').'</a></li>';
            }
            if($allow_file_search) {
                echo '<li><a target="main" href="search.php?itemtype=5&'.$parameter.'">'.getTranslation('search_file').'</a></li>';
            }
            if($allow_file_upload) {
                echo '<li><a target="main" href="upload.php?itemtype=5&'.$parameter.'">'.getTranslation('upload_file').'</a></li>';
            }
            echo '</ul>';
        }

    ?>
    </body>
</html>