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
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Oleg Selin, Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage portlets
 */

import('api.portlet.TranslatedPortlet');
import('classes.item.Item');
import('classes.item.ItemEnumeration');

class XspfPlayerPortlet extends TranslatedPortlet
{
    function XspfPlayerPortlet()
    {
        $this->loadBundle('xspfplayerportlet');
        $this->setParameter( 'slim', false );
        $this->setParameter( 'width', '400' );
        $this->setParameter( 'xspftitle', 'My music' );
        $this->setParameter( 'playlist_url', '' );
    }

    function getTitle()
    {
        return $this->getTranslation('title', 'Audio Player');
    }

    function getIdentifier()
    {
        return 'XspfPlayerPortlet';
    }

    function getHtml()
    {
        $url   = urlencode($this->getPlaylistURL());
        $width = $this->getParameter('width');

        if ($this->getParameter('slim')){
            return '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="'.$width.'" height="15" data="'._BIGACE_DIR_ADDON_WEB.'audioplayer/xspf_player_slim.swf?playlist_url='.$url.'&amp;xn_auth=no&amp;autoload=true" id="audioplayer">
                            <param name="movie" value="'._BIGACE_DIR_ADDON_WEB.'audioplayer/xspf_player_slim.swf?playlist_url='.$url.'&amp;xn_auth=no&amp;autoload=true" />
                            <param name="quality" value="high" />
                            <param name="bgcolor" value="#e6e6e6" />
                            <param name="play" value="true" />
                            <param name="loop" value="false" />
                            <param name="wmode" value="window" />
                            <param name="scale" value="showall" />
                            <param name="menu" value="false" />
                            <param name="devicefont" value="false" />
                            <param name="salign" value="" />
                            <param name="allowScriptAccess" value="always" />
                            <!--[if !IE]>-->
                                <object type="application/x-shockwave-flash" data="'._BIGACE_DIR_ADDON_WEB.'audioplayer/xspf_player_slim.swf?playlist_url='.$url.'&amp;xn_auth=no&amp;autoload=true" width="'.$width.'" height="15" class="audioplayer">
                                    <param name="movie" value="'._BIGACE_DIR_ADDON_WEB.'audioplayer/xspf_player_slim.swf?playlist_url='.$url.'&amp;xn_auth=no&amp;autoload=true" />
                                    <param name="quality" value="high" />
                                    <param name="bgcolor" value="#e6e6e6" />
                                    <param name="play" value="true" />
                                    <param name="loop" value="false" />
                                    <param name="wmode" value="window" />
                                    <param name="scale" value="showall" />
                                    <param name="menu" value="false" />
                                    <param name="devicefont" value="false" />
                                    <param name="salign" value="" />
                                    <param name="allowScriptAccess" value="always" />
                                </object>
                            <!--<![endif]-->
                    </object>';
        }
        // else
        return '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="'.$width.'" data="'._BIGACE_DIR_ADDON_WEB.'audioplayer/xspf_player.swf?playlist_url='.$url.'&amp;xn_auth=no&amp;autoload=true" class="audioplayer">
                            <param name="movie" value="'._BIGACE_DIR_ADDON_WEB.'audioplayer/xspf_player.swf?playlist_url='.$url.'&amp;xn_auth=no&amp;autoload=true" />
                            <param name="quality" value="high" />
                            <param name="bgcolor" value="#e6e6e6" />
                            <param name="play" value="true" />
                            <param name="loop" value="false" />
                            <param name="wmode" value="window" />
                            <param name="scale" value="showall" />
                            <param name="menu" value="false" />
                            <param name="devicefont" value="false" />
                            <param name="salign" value="" />
                            <param name="allowScriptAccess" value="always" />
                            <!--[if !IE]>-->
                                <object type="application/x-shockwave-flash" data="'._BIGACE_DIR_ADDON_WEB.'audioplayer/xspf_player.swf?playlist_url='.$url.'&amp;xn_auth=no&amp;autoload=true" width="'.$width.'" class="audioplayer">
                                    <param name="movie" value="'._BIGACE_DIR_ADDON_WEB.'audioplayer/xspf_player.swf?playlist_url='.$url.'&amp;xn_auth=no&amp;autoload=true" />
                                    <param name="quality" value="high" />
                                    <param name="bgcolor" value="#e6e6e6" />
                                    <param name="play" value="true" />
                                    <param name="loop" value="false" />
                                    <param name="wmode" value="window" />
                                    <param name="scale" value="showall" />
                                    <param name="menu" value="false" />
                                    <param name="devicefont" value="false" />
                                    <param name="salign" value="" />
                                    <param name="allowScriptAccess" value="always" />
                                </object>
                            <!--<![endif]-->
                    </object>';
    }

    function getParameterType($key)
    {
        switch($key) {
            case 'slim':
                return PORTLET_TYPE_BOOLEAN;
            default:
                return PORTLET_TYPE_STRING;
        }
    }

    function getParameterName($key) {
        switch($key) {
            case 'slim':
                return $this->getTranslation('slim', 'Use slim interface');
            case 'width':
                return $this->getTranslation('width', 'Set player width');
            case 'xspftitle':
                return $this->getTranslation('xspftitle', 'Set playlist title');
            case 'playlist_url':
                return $this->getTranslation('playlist_url', 'XSPF playlist URL (without http://)');
        }
    }

    function getPlaylistURL()
    {
        if (trim(strlen($this->getParameter('playlist_url'))) > 0) {
            $url = trim($this->getParameter('playlist_url'));
            if (stripos($url, 'http://') === false)
                $url = 'http://' . $url;
            return $url;
        }

        $link = new CMSLink();
        $link->setCommand('audioplayer');
        $link->setItemID(_BIGACE_TOP_LEVEL);
        $link->setFilename('playlist.xspf');
        $link->addParameter('title', $this->getParameter('xspftitle'));

        return LinkHelper::getUrlFromCMSLink($link);
    }
}