<?php
/*
Plugin Name: Filemanager
Plugin URI: http://wiki.bigace.de/bigace:extensions:editor:filemanager
Description: Replaces the default dialogs to select CMS URLs and images.
Author: Kevin Papst
Version: 0.9
Author URI: http://www.kevinpapst.de/
*/

if(!defined('_BIGACE_ID'))
    die('Ooops');

Hooks::add_filter('dialog_setting_images', 'filemanager_image_settings', 10, 3);
Hooks::add_filter('dialog_setting_links', 'filemanager_link_settings', 10, 3);

function filemanager_image_settings($vars, $id = null, $language = null)
{
	$param = "";
	if(!is_null($id)) {
		$param = "&parent=".$id.'&'.$param;
	}
    if(is_null($language)) {
        $language = _ULC_;
    }
    $param .= "&language=".$language;

	$vars = array(
		'url'    => _BIGACE_DIR_ADDON_WEB . 'filemanager/index.php?itemtype=4&'.bigace_session_name() . "=" . bigace_session_id() . $param,
		'width'  => '800',
		'height' => '500'
	);

	return $vars;
}


function filemanager_link_settings($vars, $id = null, $language = null)
{
	$param = "";
	if(!is_null($id)) {
		$param = "&parent=".$id.'&'.$param;
	}
    if(is_null($language)) {
        $language = _ULC_;
    }
    $param .= "&language=".$language;

	$vars = array(
		'url'    => _BIGACE_DIR_ADDON_WEB . 'filemanager/index.php?'.bigace_session_name() . "=" . bigace_session_id() . $param,
		'width'  => '800',
		'height' => '500'
	);

	return $vars;
}
