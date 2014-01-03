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
 * @package bigace.editor
 */

/**
 * Defines cross-editor dialog settings.
 * These settings can be changed by plugins using filter.
 */
import('classes.util.LinkHelper');
import('classes.util.links.ImageChooserLink');
import('classes.util.links.MenuChooserLink');

function get_image_dialog_settings($id = null, $language = null)
{

	if(is_null($id)) {
		$id = _BIGACE_TOP_LEVEL;
	}
	if(is_null($language)) {
	    $language = _ULC_;
	}

	$imageLink = new ImageChooserLink();
	$imageLink->setItemID($id);
    $imageLink->addParameter('brwLang', $language);

	$vars = array(
		'url' => LinkHelper::getUrlFromCMSLink($imageLink),
		'width' => '760',
		'height' => '500'
	);

    $vars = Hooks::apply_filters('dialog_setting_images', $vars, $id, $language);

	return $vars;
}

function get_link_dialog_settings($id = null, $language = null)
{
	if(is_null($id)) {
		$id = _BIGACE_TOP_LEVEL;
	}
    if(is_null($language)) {
        $language = _ULC_;
    }

	$menulinkDialogLink = new MenuChooserLink();
	$menulinkDialogLink->setCommand('application');
	$menulinkDialogLink->setItemID($id);
	$menulinkDialogLink->setAction('util');
	$menulinkDialogLink->setSubAction('jstree');
	$menulinkDialogLink->setJavascriptCallback('setCmsUrl');
    $menulinkDialogLink->addParameter('brwLang', $language);

	$url = LinkHelper::getUrlFromCMSLink($menulinkDialogLink, array('w'=>'s'));
	$width = '400';

	if(file_exists(_BIGACE_DIR_ADDON .'FCKeditor/editor/filemanager/browser/bigace/browser.php'))
	{
		$url = _BIGACE_DIR_ADDON_WEB .'FCKeditor/editor/filemanager/browser/bigace/browser.php?Connector=connectors/php/connector.php&brwLang=' . $language;
		$width = '600' ;
	}

	$vars = array(
		'url' => $url,
		'width' => $width,
		'height' => 'screen.height * 0.5'
	);

    $vars = Hooks::apply_filters('dialog_setting_links', $vars, $id, $language);

	return $vars;
}
