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
 * @subpackage portlets
 */

import('api.portlet.TranslatedPortlet');

/**
 * @access private
 */
define('NAVI_PARAM_CSS',        'css');
/**
 * @access private
 */
define('NAVI_PARAM_ID',         'id');
/**
 * @access private
 */
define('NAVI_PARAM_LEVEL',      'level');
/**
 * @access private
 */
define('NAVI_PARAM_HOME',       'home');
/**
 * @access private
 */
define('NAVI_PARAM_LANGUAGE',   'language');
/**
 * @access private
 */
define('NAVI_PARAM_TITLE', 'title');

/**
 * Shows a Navigation of one Level.
 * The Portlet can be configured with multiple values.
 * The default CSS class is "navigationPortlet".
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage portlets
 */
class NavigationPortlet extends TranslatedPortlet
{
    private $html = null;
    private $ms = null;

    function NavigationPortlet()
    {
        // load translations
        $this->loadBundle('NavigationPortlet');

        //$this->setTitle('Menu');
        $this->setCSS("navigationPortlet");
        $this->setLanguageID('');
        $this->setStartID('');
        $this->setIncludeHome(false);
        $this->setLevel(3);
    }

    function getIdentifier() {
        return 'NavigationPortlet';
    }

    function getParameterType($key) {
        switch ($key) {
            case NAVI_PARAM_ID:
                return PORTLET_TYPE_MENUID_OPTIONAL;
            case NAVI_PARAM_LANGUAGE:
                return PORTLET_TYPE_LANGUAGE_OPTIONAL;
            case NAVI_PARAM_CSS:
                return PORTLET_TYPE_STRING;
            case NAVI_PARAM_TITLE:
                return PORTLET_TYPE_TEXT;
            case NAVI_PARAM_HOME:
                return PORTLET_TYPE_BOOLEAN;
            case NAVI_PARAM_LEVEL:
                return PORTLET_TYPE_INT;
        }
        return PORTLET_TYPE_STRING;
    }


    /**
     * Set the Title of this Portlet.
     */
    function setTitle($title) {
        $this->setParameter(NAVI_PARAM_TITLE, $title);
    }

    /**
     *  Returns the Title of this Portlet.
     */
    function getTitle() {
        return $this->getParameter(NAVI_PARAM_TITLE, $this->getTranslation('title', 'Menu'));
    }

     function displayPortlet() {
        return (strlen($this->_getCachedHtml()) > 0);
    }

    /**
     * @access private
     */
    function _getCachedHtml()
    {
        if ($this->html == null)
        {
        	$this->ms = new MenuService();
            $all = $this->buildMenuLevel($this->getStartID(), $this->getLanguageID(), $this->getLevel());
            if (strlen($all) > 0) {
                $this->html = '<ul class="'.$this->getCSS().'">';
                if($this->getIncludeHome()) {
                	$startItem = $GLOBALS['MENU_SERVICE']->getMenu($this->getStartID(), $this->getLanguageID());
            		$this->html .= '<li'.($GLOBALS['_BIGACE']['PARSER']->getItemID() == $startItem->getID() ? ' class="active"' : '').'><a href="' . LinkHelper::getUrlFromCMSLink( LinkHelper::getCMSLinkFromItem($startItem) ) . '" title="'.$startItem->getName().'">' . $startItem->getName() . '</a>';
                }
                $this->html .= "\n" . $all . '</ul>';
            } else {
                $this->html = '';
            }
        }
        return $this->html;
    }

    function getHtml() {
        return $this->_getCachedHtml();
    }

    function needsJavascript() {
        return false;
    }

    function buildMenuLevel($id, $langid, $level)
    {
        $link = '';
        $menu_info = $this->ms->getLightTreeForLanguage($id, $langid);

        for ($i=0; $i < $menu_info->count(); $i++)
        {
            $menu = $menu_info->next();
            $link .= '<li'.($GLOBALS['_BIGACE']['PARSER']->getItemID() == $menu->getID() ? ' class="active"' : '').'><a href="' . LinkHelper::getUrlFromCMSLink( LinkHelper::getCMSLinkFromItem($menu) ) . '" title="'.$menu->getName().'">' . $menu->getName() . '</a>';
            if ($menu->hasChildren() && $level > 1) {
                $link .= '<ul>' . $this->buildMenuLevel($menu->getID(), $menu->getLanguageID(), ($level-1)) . '</ul>';
            }
            $link .= "</li>\n";
        }
        return $link;
    }

    function getCSS() {
        return $this->getParameter(NAVI_PARAM_CSS, '');
    }

    /**
     * Set the CSS Class for the Menu List.
     */
    function setCSS($css = '') {
        $this->setParameter(NAVI_PARAM_CSS, $css);
    }

    /**
     * Set the ID for the Tree to be fetched.
     * If none (empty String) is passed, it uses the current Menu ID.
     */
    function setStartID($id = '') {
        $this->setParameter( NAVI_PARAM_ID, $id );
    }

    function getStartID() {
        $id = $this->getParameter( NAVI_PARAM_ID );
        if ($id == '')
            return $GLOBALS['_BIGACE']['PARSER']->getItemID();
        return $id;
    }

    /**
     * Sets the Language for the Tree to be fetched.
     * If none (empty String) is passed, it uses the current Menu Language ID.
     */
    function setLanguageID($id = '') {
        $this->setParameter( NAVI_PARAM_LANGUAGE, $id );
    }

    function getLanguageID() {
        $id = $this->getParameter( NAVI_PARAM_LANGUAGE );
        if ($id == '')
            return _ULC_;
        return $id;
    }

    function setIncludeHome($includeHome) {
        $this->setParameter(NAVI_PARAM_HOME, $includeHome);
    }

    function getIncludeHome() {
        return $this->getParameter(NAVI_PARAM_HOME, false);
    }

    function setLevel($level) {
        $this->setParameter(NAVI_PARAM_LEVEL, $level);
    }

    function getLevel() {
        return $this->getParameter(NAVI_PARAM_LEVEL, 3);
    }

}
