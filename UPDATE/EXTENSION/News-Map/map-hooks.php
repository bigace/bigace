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
 */
 
/**
 * Hooks for the News-Map Plugin.
 *
 */

if(!defined('_BIGACE_ID'))
    die('Not authorized.');

import('classes.item.ItemProjectService');
import('classes.item.ItemAdminService');
import('classes.menu.MenuService');


Hooks::add_action('create_news_posting', 'save_news_with_gps', 10, 3);
Hooks::add_filter('update_item', 'update_news_with_gps', 10, 5);

// todo - fix for new way of adding meta values
Hooks::add_filter('edit_item_meta', 'fix_news_value_gps', 10, 2);


// todo - fix for new way of adding meta values
// make sure, the input fields are displayed in every news item
function fix_news_value_gps($project_values_text, $item)
{
    $rootID = ConfigurationReader::getConfigurationValue("news", "root.id");
    $seenWidth = false;
    $seenHeight = false;
    
    if($rootID == $item->getParentID())
    {
        foreach($project_values_text AS $prjEntry)
        {
            if($prjEntry['name'] == 'gps_width')
                $seenWidth = true;
            else if($prjEntry['name'] == 'gps_length')
                $seenHeight = true;
        }
        $ips = new ItemProjectService(_BIGACE_ITEM_MENU);
        
        if(!$seenWidth) {
            $value = '';
            
            if($ips->existsProjectText($item->getID(), $item->getLanguageID(), 'gps_width'))
                $value = $ips->getProjectText($item->getID(), $item->getLanguageID(), 'gps_width');
            
            $project_values_text[] = style_news_value_gps(array(
                'type' => 'string',
                'name' => 'gps_width',
                'value' => $value
            ), $item);
        }
        
        if(!$seenHeight) {
            $value = '';
            
            if($ips->existsProjectText($item->getID(), $item->getLanguageID(), 'gps_length'))
                $value = $ips->getProjectText($item->getID(), $item->getLanguageID(), 'gps_length');

            $project_values_text[] = style_news_value_gps(array(
                'type' => 'string',
                'name' => 'gps_length',
                'value' => $value
            ), $item);
        }
    }
    return $project_values_text;
}

// save news that were submitted with the xmlrpc api
function save_news_with_gps($id, $langid, $content_struct)
{
    $rootID = ConfigurationReader::getConfigurationValue("news", "root.id");
    $ms = new MenuService();
    $menu = $ms->getMenu($id, $langid);
    
    if($menu->getParentID() == $rootID && isset($content_struct['gps_width']) && isset($content_struct['gps_length']))
    {
        $ias = new ItemAdminService(_BIGACE_ITEM_MENU);
        $ias->setProjectText($id, $langid, 'gps_width', $content_struct['gps_width']);
        $ias->setProjectText($id, $langid, 'gps_length', $content_struct['gps_length']);
    }
}

// update item which was submitted with the general item attribute admin screen
function update_news_with_gps($itemtype, $id, $langid, $val, $timestamp)
{
    if($itemtype == _BIGACE_ITEM_MENU)
    {
        $rootID = ConfigurationReader::getConfigurationValue("news", "root.id");
        $ms = new MenuService();
        $menu = $ms->getMenu($id, $langid);

        if($menu->getParentID() == $rootID && isset($_POST['gps_width']) && isset($_POST['gps_length']))
        {
            $ias = new ItemAdminService(_BIGACE_ITEM_MENU);
            $ias->setProjectText($id, $langid, 'gps_width', $_POST['gps_width']);
            $ias->setProjectText($id, $langid, 'gps_length', $_POST['gps_length']);
        }
    }
}
