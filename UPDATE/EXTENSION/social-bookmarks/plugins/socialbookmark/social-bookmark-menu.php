<?php
/*
Plugin Name: Social Bookmarks
Plugin URI: http://wiki.bigace.de/bigace:extensions:addon:socialbookmark
Description: This plugins displays a beautyful social bookmark menu.
Author: Kevin Papst
Version: 0.6
Author URI: http://www.kevinpapst.de/
$Id$
*/

if(!defined('_BIGACE_ID'))
    die('Ooops');

Hooks::add_action('social_bookmark_plugin', 'social_bookmark_version', 10, 1);
Hooks::add_filter('metatags_more', 'social_bookmark_metatags', 10, 2);
Hooks::add_filter('parse_content', 'social_bookmark_parse_content', 10, 2);

function social_bookmark_version()
{
    return "0.6";
}

function social_bookmark_metatags($values, $item)
{
    $values[] = '
    <link rel="stylesheet" href="'.BIGACE_URL_PLUGINS.'socialbookmark/social-bookmark-menu.css" type="text/css" media="screen, projection" >
    <!--[if IE]>
    <style type="text/css">
    ul.socials a {
    color:#fff;
    }
    </style>
    <![endif]-->
    <!--[if lt IE 7]>
    <script src="'.BIGACE_URL_PLUGINS.'socialbookmark/IE7.js" type="text/javascript"></script>
    <![endif]-->
    ';
    return $values;
}

function social_bookmark_parse_content($content, $MENU)
{
    if (stripos($content, '[socialbookmarks]') !== false) {
        $url   = urlencode(LinkHelper::itemUrl($MENU));
        $title = $MENU->getName();
        $html  = '
        <ul class="socials">';
            if (ConfigurationReader::getValue('social.bookmarks', 'digg')) $html .= '
            <li class="digg"><a target="_blank" rel="nofollow" href="http://digg.com/submit?phase=2&amp;url='.$url.'&amp;title='.$title.'" title="Digg this!"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'reddit')) $html .= '
            <li class="reddit"><a target="_blank" rel="nofollow" href="http://reddit.com/submit?url='.$url.'&amp;title='.$title.'" title="Share this on Reddit"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'stumble')) $html .= '
            <li class="stumble"><a target="_blank" rel="nofollow" href="http://www.stumbleupon.com/submit?url='.$url.'&amp;title='.$title.'" title="Stumble upon something good? Share it on StumbleUpon"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'delicious')) $html .= '
            <li class="delicious"><a target="_blank" rel="nofollow" href="http://del.icio.us/post?url='.$url.'&amp;title='.$title.'" title="Share this on del.icio.us"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'technorati')) $html .= '
            <li class="technorati"><a target="_blank" rel="nofollow" href="http://technorati.com/faves?add='.$url.'" title="Share this on Technorati"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'furl')) $html .= '
            <li class="furl"><a target="_blank" rel="nofollow" href="http://www.furl.net/storeIt.jsp?t='.$title.'&amp;u='.$url.'" title="Share this on Furl"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'facebook')) $html .= '
            <li class="facebook"><a target="_blank" rel="nofollow" href="http://www.facebook.com/share.php?u='.$url.'&amp;t='.$title.'" title="Share this on Facebook"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'myspace')) $html .= '
            <li class="myspace"><a target="_blank" rel="nofollow" href="http://www.myspace.com/Modules/PostTo/Pages/?u='.$url.'&amp;t='.$title.'" title="Post this to MySpace"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'yahoo')) $html .= '
            <li class="yahoo"><a target="_blank" rel="nofollow" href="http://myweb2.search.yahoo.com/myresults/bookmarklet?t='.$title.'&amp;u='.$url.'" title="Save this to Yahoo MyWeb"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'script-style')) $html .= '
            <li class="script-style"><a target="_blank" rel="nofollow" href="http://scriptandstyle.com/submit?url='.$url.'&amp;title='.$title.'" title="Submit this to Script &amp; Style"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'blinklist')) $html .= '
            <li class="blinklist"><a target="_blank" rel="nofollow" href="http://www.blinklist.com/index.php?Action=Blink/addblink.php&amp;Url='.$url.'&amp;Title='.$title.'" title="Share this on Blinklist"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'mixx')) $html .= '
            <li class="mixx"><a target="_blank" rel="nofollow" href="http://www.mixx.com/submit?page_url='.$url.'&amp;title='.$title.'" title="Share this on Mixx"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'designfloat')) $html .= '
            <li class="designfloat"><a target="_blank" rel="nofollow" href="http://www.designfloat.com/submit.php?url='.$url.'&amp;title='.$title.'" title="Submit this to DesignFloat"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'vkontakte')) $html .= '
            <li class="vkontakte"><a target="_blank" rel="nofollow" href="http://vkontakte.ru/share.php?url='.$url.'&amp;title='.$title.'" title="Опубликовать ВКонтакте"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'odnoklassniki')) $html .= '
            <li class="odnoklassniki"><a target="_blank" rel="nofollow" href="http://www.odnoklassniki.ru/dk?st.cmd=addShare&amp;st._surl='.$url.'" title="Поделиться с друзьями на Одноклассниках"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'mailru')) $html .= '
            <li class="mailru"><a target="_blank" rel="nofollow" href="http://connect.mail.ru/share?url='.$url.'&amp;title='.$title.'" title="Мне нравится! В Мой Мир!"> </a></li>';
            if (ConfigurationReader::getValue('social.bookmarks', 'yaru')) $html .= '
            <li class="yaru"><a target="_blank" rel="nofollow" href="http://my.ya.ru/posts_add_link.xml?title='.$title.'&amp;URL='.$url.'" title="Поделиться на Я.ру"> </a></li>';
        $html .= '
        </ul>
        <div class="socialclear">&nbsp;</div>
        ';
        $content = str_ireplace('[socialbookmarks]', $html, $content);
    }
    return $content;
}
