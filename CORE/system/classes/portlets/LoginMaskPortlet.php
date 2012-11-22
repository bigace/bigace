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
import('classes.util.ApplicationLinks');

/**
 * @access private
 */
define('LOGIN_PARAM_TITLE', 'title');

/**
 * This portlets shows a Login Form.
 * It uses the Public Directory <code>_BIGACE_DIR_PUBLIC_WEB.'system/images/'</code>
 * by default.
 * It will only be displayed if the current User is not logged in!
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage portlets
 */
class LoginMaskPortlet extends TranslatedPortlet
{
    /**
     * @access private
     */
    var $publicDir = '';

    function LoginMaskPortlet()
    {
        // load translations
        $this->loadBundle('LoginMaskPortlet');

        //$this->setTitle( $this->getTranslation('title') );
        $this->setImageDirectory(_BIGACE_DIR_PUBLIC_WEB.'system/images/');
    }

    /**
     * Sets the Directory where the Images are located.
     */
    function setImageDirectory($pubDir)
    {
        $this->publicDir = $pubDir;
    }

    function getIdentifier() {
        return 'LoginMaskPortlet';
    }

    /**
     * Set the Title of this Portlet.
     */
    function setTitle($title) {
        $this->setParameter(LOGIN_PARAM_TITLE, $title);
    }

    /**
     *  Returns the Title of this Portlet.
     */
    function getTitle() {
        return $this->getParameter(LOGIN_PARAM_TITLE, $this->getTranslation('title'));
    }

    function getParameterType($key) {
        switch ($key) {
            case LOGIN_PARAM_TITLE:
                return PORTLET_TYPE_TEXT;
        }
        return PORTLET_TYPE_STRING;
    }

    function getHtml() {
        return '<div class="loginPortlet">
            <form action="' . ApplicationLinks::getLoginURL($GLOBALS['MENU']->getID()) . '" method="post" name="loginForm" onSubmit="javascript:return checkLogin();">
                <img src="' . $this->publicDir . 'login.gif" border="0"> <b>' . $this->getTranslation('login') . '</b>
                <br><img height="4" width="1" src="' . $this->publicDir . 'empty.gif">
                <table cellpadding="0" cellspacing="0" border="0" width="90%">
                    <tbody>
                	<tr>
                		<td>' . $this->getTranslation('name') . '</td><td align="right"><input size="6" name="UID" type="text" value=""></td>
                	</tr>
                    <tr>
                      <td>' . $this->getTranslation('password'). '</td><td align="right"><input size="6" name="PW" type="password"></td>
                	</tr>
                	<tr>
                    	<td colspan="2" align="right">
                			<button class="loginSubmit" type="submit">' . $this->getTranslation('submit') . '</button>
                	    </td>
                    </tr>
                    </tbody>
                </table>
            </form></div>';
    }

    function needsJavascript() {
        return true;
    }

    function getJavascript() {
        return "
            <script type=\"text/javascript\">
                function checkLogin()
                {
                    if (document.loginForm.UID.value == '')
                    {
                        alert('" . $this->getTranslation('enter_name') . "');
                        document.loginForm.UID.focus();
                        return false;
                    }

                    if (document.loginForm.PW.value == '')
                    {
                        alert('" . $this->getTranslation('enter_password') . "');
                        document.loginForm.PW.focus();
                        return false;
                    }

                    return true;
                }
            </script>
        ";
    }

    function displayPortlet() {
        return $GLOBALS['_BIGACE']['SESSION']->isAnonymous();
    }

}