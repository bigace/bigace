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

import('classes.util.LinkHelper');
import('classes.util.CMSLink');
import('classes.menu.Menu');

/**
 * This class provides basic methods to build URLs to the BIGACE Applications.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util
 */
class ApplicationLinks
{
    /**
     * Gets the URL for the Home Link.
     * @param String id the Home ID (default is_BIGACE_TOP_LEVEL)
     */
    static function getHomeURL($id = null) {

    	if (is_null($id) || $id == '')
        	$id = _BIGACE_TOP_LEVEL;
		
        $topLevel = new Menu($id, ITEM_LOAD_LIGHT, _ULC_);

        return LinkHelper::getUrlFromCMSLink(LinkHelper::getCMSLinkFromItem($topLevel));
    }

    /**
     * Build the URL for displaying the Login Formular.
     * @param String id the Menu ID to redirect after a Login (default is current Menu)
     */
    static function getLoginFormURL($id = '', $params = array())
    {
        import('classes.util.links.LoginFormularLink');
        $link = new LoginFormularLink();
        if($id != '') {
            $link->setItemID($id);
        }
        return LinkHelper::getUrlFromCMSLink($link, $params);
    }

    /**
     * Build the URL for performing a Login.
     * @param String id the Menu ID to redirect after a successful Login
     */
    static function getLoginURL($id = '', $params = array())
    {
        import('classes.util.links.AuthenticateLink');
        $link = new AuthenticateLink();
        if($id != '') {
            $link->setItemID($id);
        }
        return LinkHelper::getUrlFromCMSLink($link, $params);
    }

    /**
     * Build the URL for a logout.
     * @param String id the Menu ID to redirect after the Logout (default is current Menu)
     */
    static function getLogoutURL($id = '')
    {
        import('classes.util.links.LogoutLink');
        $link = new LogoutLink();
        if($id != '') {
            $link->setItemID($id);
        }
        return LinkHelper::getUrlFromCMSLink($link);
    }

    /**
     * Gets the URL for opening the BIGACE Administration Console.
     * @param String id the Admin ID (default is _BIGACE_TOP_LEVEL)
     */
    static function getAdministrationURL($id = '')
    {
        import('classes.util.links.AdministrationLink');
        if ($id == '')
        	$id = null;
        $link = new AdministrationLink($id);
        return LinkHelper::getUrlFromCMSLink($link);
    }

    /**
     * Gets the URL to the Editor.
     * @param String id the Menu ID to edit
     * @param String langid the Language ID
     * @param array params further URL Parameter to append
     */
    static function getEditorURL($id = '', $langid = '', $params = array())
    {
        return ApplicationLinks::getEditorTypeURL(null,$id,$langid,$params);
    }

    /**
     * Gets the URL to the Editor.
     * The Default Editor is used if none is given, it is configured in your Consumer Config.
     * @param String type the Editor type (required)
     * @param String id the Menu ID to edit (optional, if not passed global menu id is used)
     * @param String langid the Language ID (optional, if not passed global menu language id is used)
     * @param array params further URL Parameter to append
     */
    static function getEditorTypeURL($type = null, $id = '', $langid = '', $params = array())
    {
        import('classes.util.links.EditorLink');
        
        if ($id == '')$id = $GLOBALS['MENU']->getID();
        if ($langid == '') $langid = $GLOBALS['MENU']->getLanguageID();

        $link = new EditorLink($id, $langid);
        if($type != null) 
        	$link->setEditor($type);

        return LinkHelper::getUrlFromCMSLink($link, $params);
    }

    /**
     * Gets the URL for opening the Default Search.
     * @param String id the Menu ID used to open the Search with
     */
    static function getSearchURL($id = '', $langid = '')
    {
        import('classes.util.links.SearchLink');
        $link = new SearchLink();
        if($id != '')
            $link->setItemID($id);
        if ($langid != '')
            $link->setLanguageID($langid);
        return LinkHelper::getUrlFromCMSLink($link);
    }

    /**
     * Gets the URL for opening the Portlet Administration.
     * @param String    id      the Menu ID used to open the Administration for
     * @param array     params  extended URL Parameter
     */
    static function getPortletAdminURL($id = '', $params = array())
    {
        import('classes.util.links.PortletAdminLink');
        $link = new PortletAdminLink();
        if($id != '')
            $link->setItemID($id);
        $link->setLanguageID($GLOBALS['MENU']->getLanguageID());
        return LinkHelper::getUrlFromCMSLink($link, $params);
    }

    /**
     * Gets the URL to display several Information about the requested Item.
     * If you want to display the currents Menu Information, leave everything empty.
     * Default is <code>_BIGACE_ITEM_MENU, $GLOBALS['MENU']->getID(), $GLOBALS['MENU']->getLanguageID()</code>.
     *
     * @param String itemtype the Itemtype
     * @param String id the Menu ID to edit
     * @param String langid the Language ID
     */
    static function getItemInfoURL($itemtype = '', $id = '', $langid = '')
    {
        import('classes.util.links.ItemInfoLink');
        $link = new ItemInfoLink();
        if ($itemtype != '')
            $link->setInfoItemtype($itemtype);
        if ($id != '')
            $link->setInfoItemID($id);
        if ($langid != '')
            $link->setInfoItemLanguage($langid);
        return LinkHelper::getUrlFromCMSLink($link);
    }

    /**
     * Gets the URL to change the Session Language.
     * Pass a Language String as Locale (for example 'de' or 'en').
     *
     * @param String locale the Language to switch to
     * @param String id the Menu ID to jump to (default is current Menu ID)
     */
    static function getChangeSessionLanguageURL($locale, $id = '')
    {
        import('classes.util.links.SessionLanguageLink');
        $link = new SessionLanguageLink();
        if($id == '') {
            $id = $GLOBALS['_BIGACE']['PARSER']->getItemID();
        }
        $link->setItemID($id);
        $link->setSessionLanguage($locale);
        return LinkHelper::getUrlFromCMSLink($link);
    }

    /**
     * Get the URL to request Ajax XML Information about a special Item.
     */
    static function getAjaxItemInfoURL($itemtype = '', $id = '', $langid = '')
    {
        import('classes.util.links.AjaxItemInfoLink');
        $link = new AjaxItemInfoLink();

        if($id != '')
            $link->setItemID($id);
        if ($langid != '')
            $link->setLanguageID($langid);
        if ($itemtype == '')
            $itemtype = _BIGACE_ITEM_MENU;

        return LinkHelper::getUrlFromCMSLink($link, array('itemtype' => $itemtype));
    }

}
