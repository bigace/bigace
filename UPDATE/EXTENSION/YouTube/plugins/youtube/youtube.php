<?php
/*
Plugin Name: YouTube Plugin
Plugin URI: http://wiki.bigace.de/bigace:extensions:youtube
Description: This plugins brings a content parser, which replaces [youtube]youtube-id[/youtube] and [youtubeimg]youtube-id[/youtubeimg] tags within your pages content.
Author: Kevin Papst
Version: 1.2
Author URI: http://www.kevinpapst.de/
$Id$
*/

if(!defined('_BIGACE_ID'))
    die('Ooops');

Hooks::add_filter('parse_content', 'youtube_parse_content', 10, 2);
Hooks::add_action('youtube_plugin', 'youtube_plugin_version', 10, 2);

function youtube_plugin_version()
{
    return "1.2";
}

function youtube_parse_content($content, $MENU)
{
    $search = '/\[youtube\](.+)\[\/youtube\]/i';
    $find = preg_match($search, $content);
    if ($find !== false && $find > 0) {
        $title  = ConfigurationReader::getConfigurationValue('youtube','title','YouTube video player');
        $class  = ConfigurationReader::getConfigurationValue('youtube','class','youtube-player');
        $width  = ConfigurationReader::getConfigurationValue('youtube','width','480');
        $height = ConfigurationReader::getConfigurationValue('youtube','height','390');
        $code = '<iframe title="'.$title.'" class="'.$class.'" type="text/html" width="'.$width.
                '" height="'.$height.'" src="http://www.youtube.com/embed/${1}" frameborder="0" allowFullScreen></iframe>';

        $content = preg_replace($search, $code, $content);
    }

    $search2 = '/\[youtubeimg type=\"(.+)\"\](.+)\[\/youtubeimg\]/iU';
    $find2 = preg_match($search2, $content);
    if ($find2 !== false && $find2 > 0) {
        $code = '<img class="youtubeimg" src="http://img.youtube.com/vi/${2}/${1}.jpg" alt="" />';

        $content = preg_replace($search2, $code, $content);
    }

    return $content;
}