<?php
/*
Plugin Name: SEO Plugin
Plugin URI: http://wiki.bigace.de/bigace:extensions:seo
Description: This plugins brings an XML Sitemap and input fields to administrate html metatags for pages. It hooks into the Smarty TAG {metatags} , so you need to use this in your templates.
Author: Kevin Papst
Version: 0.7
Author URI: http://www.kevinpapst.de/
*/


if(!defined('_BIGACE_ID'))
    die('Ooops');

Hooks::add_filter('metatags', 'seo_metatags', 10, 2);
Hooks::add_filter('edit_item_meta', 'seo_item_attributes', 10, 2);
Hooks::add_filter('create_item_meta', 'seo_create_item_attributes', 10, 2);
Hooks::add_action('update_item', 'update_seo_item', 10, 5);

function seo_metatags($values, $item)
{
    import('classes.item.ItemProjectService');

    $ips = new ItemProjectService(_BIGACE_ITEM_MENU);
    $meta_vals = $ips->getAllText($item->getID(), $item->getLanguageID());

    $author = (isset($meta_vals['meta_author']) && strlen(trim($meta_vals['meta_author'])) > 0 ? $meta_vals['meta_author'] : (isset($values['author']) ? $values['author'] : ''));
    $robots = (isset($meta_vals['meta_robots']) ? $meta_vals['meta_robots'] : (isset($values['robots']) && strlen($values['robots']) > 0 ? $values['robots'] : 'index,follow'));
    $title  = (isset($meta_vals['meta_title']) && strlen(trim($meta_vals['meta_title'])) > 0 ? $meta_vals['meta_title'] : (isset($values['title']) ? $values['title'] : $item->getName()));

    $values['author'] = $author;
    $values['robots'] = $robots;
    $values['title'] = $title;

    return $values;
}

// used when a menu is created
function seo_create_item_attributes($values, $itemtype)
{
    if($itemtype == _BIGACE_ITEM_MENU)
    {
        $values['Meta Tags'] = add_seo_attributes('index,follow','','');
    }
    return $values;
}

// used both: when a menu is created or edited
function add_seo_attributes($meta_robots,$meta_title,$meta_author)
{
    $robots = array('index,follow','noindex,nofollow','index,nofollow','noindex,follow');

    $robotSelect = '';
    foreach($robots AS $rob)
        $robotSelect .= '<option value="'.$rob.'"'.(($meta_robots == $rob) ? " selected" : '').'>'.$rob.'</option>';

    return '
            <table border="0">
                <col width="170"/>
                <col />
                <tr>
                    <td><label for="meta_title">Title</label></td>
                    <td><input type="text" id="meta_title" value="'.$meta_title.'" name="meta_title" /></td>
                </tr>
                <tr>
                    <td><label for="meta_author">Author</label></td>
                    <td><input type="text" id="meta_author" value="'.$meta_author.'" name="meta_author" /></td>
                </tr>
                <tr>
                    <td><label for="meta_robots">Robots</label></td>
                    <td>
                    <select id="meta_robots" name="meta_robots">
                        '.$robotSelect.'
                    </select>
                    </td>
                </tr>
            </table>
    ';
}

// used when a menu is edited
function seo_item_attributes($values, $item)
{
    if($item->getItemtypeID() == _BIGACE_ITEM_MENU)
    {
        import('classes.item.ItemProjectService');

        $ips = new ItemProjectService(_BIGACE_ITEM_MENU);
        $meta_vals = $ips->getAllText($item->getID(), $item->getLanguageID());
        $meta_robots = isset($meta_vals['meta_robots']) ? $meta_vals['meta_robots'] : 'index,follow';
        $meta_title = isset($meta_vals['meta_title']) ? $meta_vals['meta_title'] : '';
        $meta_author = isset($meta_vals['meta_author']) ? $meta_vals['meta_author'] : '';

        $values['Meta Tags'] = add_seo_attributes($meta_robots,$meta_title,$meta_author);
    }

    return $values;
}



// update item which was submitted with the general item attribute admin screen
function update_seo_item($itemtype, $id, $langid, $val, $timestamp)
{
    import('classes.item.ItemAdminService');
    if($itemtype == _BIGACE_ITEM_MENU)
    {
        if(isset($_POST['meta_title']) || isset($_POST['meta_author']) || isset($_POST['meta_robots']))
        {
            $ias = new ItemAdminService(_BIGACE_ITEM_MENU);

            if(isset($_POST['meta_robots']))
                $ias->setProjectText($id, $langid, 'meta_robots', $_POST['meta_robots']);
            if(isset($_POST['meta_author']))
                $ias->setProjectText($id, $langid, 'meta_author', $_POST['meta_author']);
            if(isset($_POST['meta_title']))
                $ias->setProjectText($id, $langid, 'meta_title',  $_POST['meta_title']);
        }
    }
}
