<?php
/*
Plugin Name: News Plugin
Plugin URI: http://wiki.bigace.de/bigace:extensions:addon:news
Description: This plugins brings a News administration page, RSS feed and several templates and includes for easy blog-like news integration for your website. Please read the <a href="http://wiki.bigace.de/bigace:extensions:addon:news" target="_blank">News Documentation</a> on how to install and configure this Plugin properly.
Author: Kevin Papst
Version: 1.7
Author URI: http://www.kevinpapst.de/
$Id$
*/

if(!defined('_BIGACE_ID'))
    die('Ooops');

Hooks::add_filter('admin_menu', 'news_admin_menu', 10, 1);
Hooks::add_filter('metatags_more', 'news_metatags', 10, 2);
//Hooks::add_action('page_header', 'news_pageheader', 1);
Hooks::add_action('news_plugin', 'news_plugin_version', 10, 1);

define('NEWS_ROOT_ID', ConfigurationReader::getConfigurationValue("news", "root.id"));

function news_plugin_version()
{
    return "1.7";
}

// activates the news admin menu
function news_admin_menu($menu)
{
    $menu['menu']['childs']['news'] = array(
            'permission'    => 'news.edit,news.create,news.delete,news.categories',
            'menu.translate'=> true,
            'pluginpath'    => 'news'
    );
    return $menu;
}

function news_metatags($values, $item)
{
    /*
    if($item->getParentID() == NEWS_ROOT_ID)
        $values[] = '<link rel="pingback" href="'.BIGACE_HOME.'xmlrpc.php" />';
    */
    $values[] = '<link rel="alternate" type="application/rss+xml" title="News Feed" href="'.LinkHelper::url("plugins/news/rss.php").'" />';
    return $values;
}

function news_pageheader($item)
{
    if($item->getParentID() == NEWS_ROOT_ID)
        header( 'X-Pingback: ' . BIGACE_HOME.'xmlrpc.php' );
}
