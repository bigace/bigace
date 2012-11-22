<?php
/*
Plugin Name: Mp3 Plugin
Plugin URI: http://wiki.bigace.de/bigace:extensions:portlet:audioplayer
Description: This plugins brings a content parser, which replaces [mp3]file.mp3[/mp3] tags within your pages content with audio player.
Author: Oleg Selin, Kevin Papst
Author URI: http://www.bigace.de/
Version: 1.1
$Id$
*/

if(!defined('_BIGACE_ID'))
    die('Ooops');

import('classes.item.ItemEnumeration');
import('classes.util.ContentTags');

Hooks::add_filter('parse_content', 'mp3_parse_content', 10, 1);
Hooks::add_action('mp3tag_plugin', 'mp3_plugin_version', 10, 2);

function mp3_plugin_version()
{
    return "1.1";
}

function mp3_parse_content($content, $MENU)
{
    // Create new instance of ContentTag
    $mp3tags = new ContentTags($content);

    // Set tag we wanna work with
    $mp3tags->SetTag('mp3');

    // Fill up tagInfo structure (TAG_ID=>(tag_caption, intag_text, tag_start_pos, tag_ens_pos, replacer_for_this_tag))
    $tagsinfo = $mp3tags->GetTagsInfo();

    // Fetch all audio items from database by Kevin (c) ;)
    $sqlString = 'SELECT a.* FROM {DB_PREFIX}item_{ITEMTYPE} a WHERE a.cid={CID} AND (a.mimetype={MIMETYPE1} OR a.mimetype={MIMETYPE2})';
    $values = array(
        'ITEMTYPE'    => _BIGACE_ITEM_FILE,
        'CID'         => _CID_,
        'MIMETYPE1'    => 'audio/mp3',
        'MIMETYPE2'    => 'audio/mpeg'
    );

    $sql     = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
    $result  = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
    $mp3list = new ItemEnumeration($result, _BIGACE_ITEM_FILE);

    // Can't cycle all items via ItemEnumeration->next(), so build mp3names array
    for ($j=0; $j < $mp3list->count(); $j++) {
        $mp3file = $mp3list->next();
        $mp3names[$mp3file->getUniqueName()] = $mp3file;
    }

    // Move thru all finded tags ('mp3') and set $replacer for all tag in tagInfo structure with appropriate xpsf player code
    for ($i = 0; $i < count($tagsinfo); $i++) {
        $search = $tagsinfo[$i]['text'];
        if (isset($mp3names[$search])) {
            $mp3     = $mp3names[$search];
            $mp3Url  = urlencode(LinkHelper::itemUrl($mp3));
            $mp3Name = urlencode($mp3->getName());

            $replacer = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="200" height="15" data="'._BIGACE_DIR_ADDON_WEB.'audioplayer/xspf_player_slim.swf?song_url='.$mp3Url.'&amp;song_title='.$mp3Name.'" class="mpegplayer">
                            <param name="movie" value="'._BIGACE_DIR_ADDON_WEB.'audioplayer/xspf_player_slim.swf?song_url='.$mp3Url.'&amp;song_title='.$mp3Name.'" />
                            <param name="quality" value="high" />
                            <param name="bgcolor" value="#e6e6e6" />
                            <param name="play" value="true" />
                            <param name="loop" value="false" />
                            <param name="wmode" value="window" />
                            <param name="scale" value="showall" />
                            <param name="menu" value="false" />
                            <param name="devicefont" value="false" />
                            <param name="salign" value="" />
                            <param name="allowScriptAccess" value="sameDomain" />
                            <!--[if !IE]>-->
                                <object type="application/x-shockwave-flash" data="'._BIGACE_DIR_ADDON_WEB.'audioplayer/xspf_player_slim.swf?song_url='.$mp3Url.'&amp;song_title='.$mp3Name.'" width="200" height="15" class="mpegplayer">
                                    <param name="movie" value="'._BIGACE_DIR_ADDON_WEB.'audioplayer/xspf_player_slim.swf?song_url='.$mp3Url.'&amp;song_title='.$mp3Name.'" />
                                    <param name="quality" value="high" />
                                    <param name="bgcolor" value="#e6e6e6" />
                                    <param name="play" value="true" />
                                    <param name="loop" value="false" />
                                    <param name="wmode" value="window" />
                                    <param name="scale" value="showall" />
                                    <param name="menu" value="false" />
                                    <param name="devicefont" value="false" />
                                    <param name="salign" value="" />
                                    <param name="allowScriptAccess" value="sameDomain" />
                                </object>
                            <!--<![endif]-->
                        </object>';
            $mp3tags->SetReplacer($replacer, $i);
        }
    }

    // Return $content with tags replaced by it $replacer
    return $mp3tags->ReplaceTags();
}