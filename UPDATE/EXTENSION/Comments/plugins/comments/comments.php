<?php
/*
Plugin Name: Comments Plugin
Plugin URI: http://wiki.bigace.de/bigace:extensions:addon:comments
Description: This plugins brings per-page configuration for comments, trackback support, an administration page and a template include to display comment forms. Please read the <a href="http://wiki.bigace.de/bigace:extensions:addon:comments" target="_blank">Comments Documentation</a> on how to install and configure this Plugin properly.
Author: Kevin Papst
Version: 1.8
Author URI: http://www.kevinpapst.de/
$Id$
*/

if(!defined('_BIGACE_ID'))
    die('Ooops');

Hooks::add_filter('admin_menu', 'comment_admin_menu', 10, 1);
Hooks::add_filter('edit_item_meta', 'comment_item_attributes', 10, 2);
Hooks::add_filter('create_item_meta', 'comment_create_item_attributes', 10, 2);
Hooks::add_action('update_item', 'update_comment_item', 10, 5);
Hooks::add_action('comments_plugin', 'comment_plugin_version', 10, 1);
Hooks::add_filter('metatags_more', 'comment_metatags', 10, 2);

function comment_plugin_version()
{
    return "1.6.1";
}

// activates the comment admin menu
function comment_admin_menu($menu)
{
    $menu['extensions']['childs']['comments'] = array(
            'permission'    => 'comments.activate,comments.edit,comments.delete',
            'menu.translate'=> true,
            'pluginpath'    => 'comments'
    );
    return $menu;
}

function comment_metatags($values, $item)
{
    $values[] = '<link rel="alternate" type="application/rss+xml" title="Comments Feed" href="'.LinkHelper::url("plugins/comments/rss.php").'" />';
    return $values;
}

function comment_create_item_attributes($values, $itemtype)
{
    if($itemtype == _BIGACE_ITEM_MENU)
    {
    	$allowTrackbacks = get_option("comments", "allow.trackbacks", true);
		$values['Comments'] = '
                <table border="0">
                    <col width="170"/>
                    <col />
                <tr>
                    <td valign="top">Allow Comments</td>
                    <td>
                        <input type="radio" id="allow_comments_yes" name="allow_comments" value="'.intval(true).'" />
                        <label for="allow_comments_yes">Yes</label>
                        &nbsp;&nbsp;&nbsp;
                        <input type="radio" id="allow_comments_no" name="allow_comments" value="'.intval(false).'" checked />
                        <label for="allow_comments_no">No</label>
                    </td>
                </tr>';

		if($allowTrackbacks)
		{
			$values['Comments'] .= '
	                <tr>
	                    <td valign="top">Allow Trackbacks/Pings</td>
	                    <td>
	                        <input type="radio" id="allow_trackbacks_yes" name="allow_trackbacks" value="'.intval(true).'" />
	                        <label for="allow_trackbacks_yes">Yes</label>
	                        &nbsp;&nbsp;&nbsp;
	                        <input type="radio" id="allow_trackbacks_no" name="allow_trackbacks" value="'.intval(false).'" checked />
	                        <label for="allow_trackbacks_no">No</label>
	                    </td>
	                </tr>';
		}

		$values['Comments'] .= '
                </table>
        ';
    }
	return $values;
}

function comment_item_attributes($values, $item)
{
    if($item->getItemtypeID() == _BIGACE_ITEM_MENU)
    {
        import('classes.comments.CommentService');

        $cs = new CommentService();
        $commentsAllowed = $cs->activeComments(_BIGACE_ITEM_MENU, $item->getID(), $item->getLanguageID());
        $trackbacksAllowed = $cs->activeRemoteComments(_BIGACE_ITEM_MENU, $item->getID(), $item->getLanguageID());

    	$allowTrackbacks = get_option("comments", "allow.trackbacks", true);

    	$values['Comments'] = '
                <table border="0">
                    <col width="170"/>
                    <col />
	                <tr>
	                    <td valign="top">Allow Comments</td>
	                    <td>
	                        <input type="radio" id="allow_comments_yes" name="allow_comments" value="'.intval(true).'" '.($commentsAllowed ? 'checked ' : '').'/>
	                        <label for="allow_comments_yes">Yes</label>
	                        &nbsp;&nbsp;&nbsp;
							<input type="radio" id="allow_comments_no" name="allow_comments" value="'.intval(false).'" '.(!$commentsAllowed ? 'checked ' : '').'/>
	                        <label for="allow_comments_no">No</label>
	                    </td>
                </tr>';

		if($allowTrackbacks)
		{
			$values['Comments'] .= '
	                <tr>
	                    <td valign="top">Allow Trackbacks/Pings</td>
	                    <td>
	                        <input type="radio" id="allow_trackbacks_yes" name="allow_trackbacks" value="'.intval(true).'" '.($trackbacksAllowed ? 'checked ' : '').'/>
	                        <label for="allow_trackbacks_yes">Yes</label>
	                        &nbsp;&nbsp;&nbsp;
	                        <input type="radio" id="allow_trackbacks_no" name="allow_trackbacks" value="'.intval(false).'" '.(!$trackbacksAllowed ? 'checked ' : '').'/>
	                        <label for="allow_trackbacks_no">No</label>
	                    </td>
	                </tr>';
		}

		$values['Comments'] .= '
               </table>
        ';
    }

    return $values;
}

// update item which was submitted with the general item attribute admin screen
function update_comment_item($itemtype, $id, $langid, $val, $timestamp)
{
    if(isset($_POST['allow_comments']))
    {
    	import('classes.item.ItemAdminService');
    	$ias = new ItemAdminService($itemtype);
        $ias->setProjectNum($id, $langid, 'allow_comments', $_POST['allow_comments']);
	    if(isset($_POST['allow_trackbacks']))
	        $ias->setProjectNum($id, $langid, 'allow_trackbacks', $_POST['allow_trackbacks']);
    }
}
