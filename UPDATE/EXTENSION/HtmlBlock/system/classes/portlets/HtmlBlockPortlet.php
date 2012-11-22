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

class HtmlBlockPortlet extends TranslatedPortlet
{
    function HtmlBlockPortlet()
    {
        $this->loadBundle('htmlblockportlet');
        $this->setParameter( 'title', 'Html Block' );
        $this->setParameter( 'css', '' );
        $this->setParameter( 'code', '' );
    }

    function getTitle()
    {
        if ($this->getParameter('title') !== '') {
            return  $this->unescape($this->getParameter('title'));
        } else {
            return 'Html Block';
        }
    }

    function getIdentifier()
    {
        return 'HtmlBlockPortlet';
    }

    function getHtml()
    {
        if ($this->getParameter('css') !== '') {
            $content =  '<div class="'.$this->getParameter('css').'">'.$this->getParameter('code').'</div>';
        } else {
            $content =  $this->getParameter('code');
        }
        return urldecode(json_decode(str_replace('%u', '\\u', json_encode($content)))); //FIX: Probably vunerability
    }

    function getParameterType($key)
    {
        switch($key) {
            case 'code':
                return PORTLET_TYPE_HTML;
            default:
                return PORTLET_TYPE_STRING;
        }
    }

    function getParameterName($key) {
        switch($key) {
            case 'title':
                return $this->getTranslation('title', 'Block title');
            case 'css':
                return $this->getTranslation('css', 'Block css class');
            case 'code':
                return $this->getTranslation('code', 'HTML code to display in block');
        }
    }

    //script from http://zizi.kxup.com/
    //javascript unesape
    function unescape($str) {
        $str = rawurldecode($str);
        $str = utf8_encode($str);
        preg_match_all("/(?:%u.{4})|&#x.{4};|&#\d+;|.+/U",$str,$r);
        $ar = $r[0];
        //print_r($ar);
        foreach($ar as $k=>$v) {
            if(substr($v,0,2) == "%u")
                $ar[$k] = iconv("UCS-2","UTF-8",pack("H4",substr($v,-4)));
            elseif(substr($v,0,3) == "&#x")
                $ar[$k] = iconv("UCS-2","UTF-8",pack("H4",substr($v,3,-1)));
            elseif(substr($v,0,2) == "&#") {
                echo substr($v,2,-1)."<br>";
                $ar[$k] = iconv("UCS-2","UTF-8",pack("n",substr($v,2,-1)));
            }
        }
        return join("",$ar);
    }
}
