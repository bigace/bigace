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
import('classes.util.applications');

/**
 * @access private
 */
define('TOOLPORTLET_PARAM_CSS', 	'css');
/**
 * @access private
 */
define('TOOLPORTLET_PARAM_STATUS', 	'login');
/**
 * @access private
 */
define('TOOLPORTLET_PARAM_HOME', 	'home');
/**
 * @access private
 */
define('TOOLPORTLET_PARAM_TITLE', 	'title');

/**
 * This portlets shows the Application Links in a List.
 * The default CSS class is "toolPortlet".
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage portlets
 */
class ToolPortlet extends TranslatedPortlet
{
    private $application;

    function ToolPortlet()
    {
        // load translations
        $this->loadBundle('ToolPortlet');

        $this->setCSS("toolPortlet");
        $this->setHomeID('');
        $this->setShowLogin(true);

        $APPS = new applications();

        // configure the application links
        $APPS->setShowText(true);
        $APPS->setPreDelimiter("<li>");
        $APPS->setPostDelimiter("</li>\n");

        $this->application = $APPS;
    }

    function getIdentifier() {
        return 'ToolPortlet';
    }

    /**
     * Set the Title of this Portlet.
     */
    function setTitle($title) {
        $this->setParameter(TOOLPORTLET_PARAM_TITLE, $title);
    }

    /**
     *  Returns the Title of this Portlet.
     */
    function getTitle() {
        return $this->getParameter(TOOLPORTLET_PARAM_TITLE, $this->getTranslation('title', 'Tools'));
    }

    function getHtml() {
        $app = $this->application;

        // prepare status html
        if($this->getHomeID() != '')
        	$app->setHomeID($this->getHomeID());
        if(!$this->getShowLogin())
            $app->hide($app->STATUS);

        return '<ul class="'.$this->getCSS().'">' . "\n" . $app->getAllLink() . "\n" . '</ul>';
    }

    function needsJavascript() {
        return true;
    }

    function getJavascript() {
        $app = $this->application;
        return $app->getAllJavascript();
    }

    function getParameterType($key) {
        switch ($key) {
            case TOOLPORTLET_PARAM_CSS:
                return PORTLET_TYPE_STRING;
            case TOOLPORTLET_PARAM_STATUS:
                return PORTLET_TYPE_BOOLEAN;
            case TOOLPORTLET_PARAM_HOME:
                return PORTLET_TYPE_MENUID_OPTIONAL;
            case TOOLPORTLET_PARAM_TITLE:
                return PORTLET_TYPE_TEXT;
        }
        return PORTLET_TYPE_STRING;
    }

    // --------------------------------------------------

    function getShowLogin() {
    	return $this->getParameter(TOOLPORTLET_PARAM_STATUS, true);
    }

    /**
     * Decide if the Link to the Login Formular should be displayed.
     * @param boolean show TRUE if you want to see the Link, otherwise false
     */
    function setShowLogin($show = true) {
        $this->setParameter(TOOLPORTLET_PARAM_STATUS, $show);
    }

    function getHomeID() {
    	return $this->getParameter(TOOLPORTLET_PARAM_HOME, '');
    }

    /**
     * Set the Home ID if different from <code>_BIGACE_TOP_LEVEL</code>.
     */
    function setHomeID($id) {
        $this->setParameter(TOOLPORTLET_PARAM_HOME, $id);
    }

    function getCSS() {
        return $this->getParameter(TOOLPORTLET_PARAM_CSS, '');
    }

    /**
     * Set the CSS Class for the used List.
     */
    function setCSS($css = '') {
        $this->setParameter(TOOLPORTLET_PARAM_CSS, $css);
    }

}