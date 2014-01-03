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
 * @package bigace.classes
 * @subpackage util
 */

import('classes.util.CMSLink');

/**
 * This class has static methods, that can be used to create and parse BIGACE URLs.
 * These links are all related to the top Class: <code>classes.util.CMSLink</code>
 *
 * Whenever you link to a Page, inside your Modul or to BIGACE Applications... you
 * should use the methods of this class, to make sure the link works properly
 * in all future versions!
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util
 */
class LinkHelper
{

    /**
     * Create a URL to a unique name.
     *
     * @param $uniqueURL the unique name to link to
     * @param $params the parameter to add to the url
     * @return string the full URL to the unique name
     */
    public static function url($uniqueURL, $params = array()) {
        $link = new CMSLink();
        $link->setUniqueName($uniqueURL);
        return LinkHelper::getUrlFromCMSLink($link, $params);
    }

    /**
     * Shorthand for LinkHelper::getUrlFromCMSLink(LinkHelper::getCMSLinkFromItem($item,$params)).
     * @param $item the item to create the URL for
     * @param $params array of parameter to add to the URÖ
     * @return string the full URL to the unique name
     */
    public static function itemUrl($item, $params = array()) {
        return LinkHelper::getUrlFromCMSLink(LinkHelper::getCMSLinkFromItem($item), $params);
    }

    /**
     * Gets the URL for the given CMSLink.
     * This method takes care about the Rewrite settings.
     *
     * If you want to link to an Item (e.g. a Menu), you can call something like this:
     * <code>
     * $link = LinkHelper::getCMSLinkFromItem($menu);
     * LinkHelper::getUrlFromCMSLink($link);
     * </code>
     *
     * @param CMSLink cmsLink the CMSLink to get the URL for
     * @param array params extended URL Parameter (like http://url?foo=bar)
	 * @return null if the first argument is of wrong class type
     */
    public static function getUrlFromCMSLink($cmsLink, $params = array(), $unique = true)
    {
        if (is_subclass_of($cmsLink, 'CMSLink') || strcasecmp(get_class($cmsLink),'CMSLink') == 0)
        {
            $lang = $cmsLink->getLanguageID();
            if(is_null($lang)) $lang = $GLOBALS['_BIGACE']['SESSION']->getLanguageID();

            $id = $cmsLink->getItemID();
            if(is_null($id)) $id = _BIGACE_TOP_LEVEL;

            $action = $cmsLink->getAction();
            if(!is_null($action)) $id .= '_t' . $action;

            $subAction = $cmsLink->getSubAction();
            if(!is_null($subAction)) $id .= '_k' . $subAction;

            $cmd = $cmsLink->getCommand();
            if(is_null($cmd)) $cmd = _BIGACE_CMD_MENU;

            // merge probably added parameter from the link
            if($cmsLink->getParameter() != null)
                $params = array_merge($cmsLink->getParameter(), $params);

            // if we use NO url rewriting we create a parameterized url
            if (BIGACE_URL_REWRITE == 'false') {
                return LinkHelper::createLink($cmsLink->getBaseURL() . _CID_DIR_PATH . 'public/index.php?cmd=' . $cmd . '&id=' . $id . '_l' . $lang, $params);
            }

            $un = $cmsLink->getUniqueName();
            if($unique && !is_null($un) && strlen($un)>0) {
            	if(strlen($un)>0 && $un{0} == '/') { $un = substr($un,1); }
                return LinkHelper::createLink($cmsLink->getBaseURL() . _CID_DIR_PATH . $un, $params);
            }
            else {
                // otherwise we simulate a path
                $name = $cmsLink->getFileName();
                if (is_null($name)) $name = 'index.html';

                return LinkHelper::createLink($cmsLink->getBaseURL() . _CID_DIR_PATH . 'bigace/' . $cmd . '/' . $id . '_l'.$lang.'/' . $name, $params);
            }
        }
        return null;
    }

    /**
     * Appends Parameter to a given URL. It takes care about the ? separator and appends the Session ID if required (in case of inaccepted Cookies).
     *
     * @param String adress the URL (if empty a link like ?foo=bar will be created)
     * @param array params the URL Parameter as key-value mapped array
     * @return String the created URL
     */
    public static function createLink($adress, $params = array())
    {
        $i = 0;
        $link = $adress;
        if (count($params) > 0 || (bigace_session_started() && count($_COOKIE) == 0))
        {
            if (strpos ($adress, "?") === false) {
                $link .= '?';
            } else {
                $link .= '&';
            }
            if (bigace_session_started() && count($_COOKIE) == 0) {
                $link .= bigace_session_name() . "=" . bigace_session_id();
                $i++;
            }
            foreach ($params AS $key => $value) {
                if ($i != 0) {
                    $link .= '&';
                }
                $link .= $key . '=' . $value;
                $i++;
            }
        }
        unset ($i);
        return $link;
    }

    /**
     * Creates an CMSLink instance for the given Item.
     * @param Item item the Item to link to
     * @return null if the first argument is of wrong class type
     */
    public static function getCMSLinkFromItem($item)
    {
        $link = null;
        if (is_subclass_of($item, 'Item') || strcasecmp(get_class($item),'Item') == 0)
        {
            $link = new CMSLink();
            $link->setLanguageID($item->getLanguageID());
            $link->setCommand($item->getCommand());
            $link->setItemID($item->getID());
            $link->setUniqueName($item->getUniqueName());
            if($item->getItemtypeID() != _BIGACE_ITEM_MENU)
                $link->setFilename($item->getOriginalName());
        }
        return $link;
    }
}
