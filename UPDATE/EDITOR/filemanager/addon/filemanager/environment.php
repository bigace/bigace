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
define('_BIGACE_FILEMANAGER', 'true');

// initialize a proper bigace session.
include_once(realpath(dirname(__FILE__).'/../../system/libs/').'/init_session.inc.php');

if($GLOBALS['_BIGACE']['SESSION']->isAnonymous())
{
    import('classes.util.LinkHelper');
    import('classes.util.links.LoginFormularLink');
    $lfl = new LoginFormularLink();
    $lfl->setRedirectURL(_BIGACE_DIR_ADDON_WEB.'filemanager/index.php');
    header('Location: ' . LinkHelper::getUrlFromCMSLink($lfl));
}

Translations::loadGlobal('bigace');
Translations::loadGlobal('imagebrowser');
Translations::loadGlobal('filemanager');

import('classes.smarty.BigaceSmarty');
import('classes.language.LanguageEnumeration');

//import('classes.template.TemplateService');
//import('classes.administration.AdminStyleService');

$itemtype = isset($_GET['itemtype']) ? $_GET['itemtype'] : null;
if($itemtype != _BIGACE_ITEM_MENU && $itemtype != _BIGACE_ITEM_IMAGE && $itemtype != _BIGACE_ITEM_FILE)
    $itemtype = null;
//echo '###'.$itemtype.'###';

$parameter = bigace_session_name() . "=" . bigace_session_id();

$language = isset($_GET['language']) ? $_GET['language'] : _ULC_;
$locale = new Language($language);
if(!$locale->isValid()) {
    $language = _ULC_;
}
$parameter .= '&language='.$language;

if(isset($_GET['parent'])) {
    define('GALLERY_PARENT', $_GET['parent']);
    $parameter .= '&parent='.GALLERY_PARENT;
}

$allow_menu_browsing = false;
$allow_menu_categories = false;
$allow_menu_search = false;

$allow_image_browsing = false;
$allow_image_categories = false;
$allow_image_search = false;
$allow_image_upload = false;

$allow_file_browsing = false;
$allow_file_categories = false;
$allow_file_search = false;
$allow_file_upload = false;

if($itemtype == null || $itemtype == _BIGACE_ITEM_MENU) {
    $allow_menu_browsing = true;
    $allow_menu_categories = true;
    $allow_menu_search = false;
}

if($itemtype == null || $itemtype == _BIGACE_ITEM_IMAGE) {
    $allow_image_browsing = true;
    $allow_image_categories = true;
    $allow_image_search = false;
    $allow_image_upload = true;
}

if($itemtype == null || $itemtype == _BIGACE_ITEM_FILE) {
    $allow_file_browsing = true;
    $allow_file_categories = true;
    $allow_file_search = false;
    $allow_file_upload = true;
}

function getFilemanagerUrl($file, $params)
{
    $link =  $file . '?' . $GLOBALS['parameter'];
    foreach($params AS $key => $value)
        $link .= '&' . $key . '=' . $value;
    return $link;
}

function getTemplateService()
{
    /*
    $STYLE_SERVICE = new AdminStyleService();
    $style = $STYLE_SERVICE->getConfiguredStyle();

    $ts = new TemplateService();
    $ts->addTemplateDirectory(dirname(__FILE__));
    setBigaceTemplateValue('PUBLIC_DIR', _BIGACE_DIR_PUBLIC_WEB);
    setBigaceTemplateValue('BIGACE_VERSION', _BIGACE_ID);
    setBigaceTemplateValue('STYLE_DIR', $style->getWebDirectory());
    return $ts;
    */
    return BigaceSmarty::getPluginSmarty();
}