<?php
/*
Plugin Name: Vimeo Plugin
Plugin URI: http://wiki.bigace.de/bigace:extensions:vimeo
Description: This plugins brings a content parser, which replaces [vimeo]video-id[/vimeo] tags within your pages content.
Author: Kevin Papst
Version: 1.0
Author URI: http://www.kevinpapst.de/
$Id$
*/

if(!defined('_BIGACE_ID'))
    die('Ooops');

Hooks::add_filter('parse_content', 'vimeo_parse_content', 10, 2);
Hooks::add_action('vimeo_plugin', 'vimeo_plugin_version', 10, 2);

function vimeo_plugin_version()
{
    return "1.1";
}

function vimeo_parse_content($content, $MENU)
{
    $search = '/\[vimeo\](.+)\[\/vimeo\]/i';
    $find = preg_match($search, $content);
    if ($find !== false && $find > 0) {
        
        $title  = ConfigurationReader::getConfigurationValue('vimeo','title','YouTube video player');
        $class  = ConfigurationReader::getConfigurationValue('vimeo','class','youtube-player');
        $width  = ConfigurationReader::getConfigurationValue('vimeo','width','400');
        $height = ConfigurationReader::getConfigurationValue('vimeo','height','225');
        
        $code = '<iframe title="'.$title.'" class="'.$class.'" type="text/html" width="'.$width.
                '" height="'.$height.'" src="http://player.vimeo.com/video/${1}" frameborder="0"></iframe>';

        $content = preg_replace($search, $code, $content);
    }
    return $content;
}
