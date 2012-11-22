<?php
/*
Plugin Name: SimpleAds Plugin
Plugin URI: http://wiki.bigace.de/bigace:extensions:simpleads
Description: This plugins brings a content parser for displaying Ads within your page content [ad]simplead-id[/ad] and a admin menu to manage your Ads.
Author: Kevin Papst
Version: 1.0
Author URI: http://www.kevinpapst.de/
$Id$
*/

if(!defined('_BIGACE_ID'))
    die('Ooops');

Hooks::add_filter('admin_menu', 'simpleads_admin_menu', 10, 1);
Hooks::add_filter('parse_content', 'simpleads_parse_content', 10, 2);
Hooks::add_action('simpleads_plugin', 'simpleads_plugin_version', 10, 2);

function simpleads_plugin_version()
{
    return "1.0";
}

function simpleads_admin_menu($menu)
{
    $menu['extensions']['childs']['simpleads'] = array(
            'permission'    => 'simple.ads',
            'menu.translate'=> true,
            'pluginpath'    => 'simpleads'
    );
    return $menu;
}

function simpleads_parse_content($content, $MENU)
{
    $search = '/\[ad\](.+)\[\/ad\]/i';
    $find = preg_match($search, $content);
    if ($find !== false && $find > 0) {
        preg_match_all($search, $content, $results, PREG_SET_ORDER);
        import('classes.util.SimpleAdsForm');
        $form = new SimpleAdsForm();
        foreach($results as $ad) {
            $entry = $form->get_generic_entry($ad[1]);
            if ($entry !== null && isset($entry['value'])) {
                $content = str_replace($ad[0], $entry['value'], $content);
            } else {
                if (count($entry) == 0) {
                    $content = str_replace(
                        $ad[0], 
                        '<span style="color:red; font-weight: bold;">Could not find ad with name "'.$ad[1].'"</span>', 
                        $content
                    );
                } else {
                    $content = str_replace(
                        $ad[0], 
                        '<span style="color:red; font-weight: bold;">Found more than one ad with the name "'.$ad[1].'" - change the name!</span>', 
                        $content
                    );
                }
            }
        }
    }
    return $content;
}
