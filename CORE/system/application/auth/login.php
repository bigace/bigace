<?php
//
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS for MySQL                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) Kevin Papst                                              |
// | Web           http://www.bigace.de                                     |
// | Mirror        http://bigace.sourceforge.net/                           |
// | Sourceforge   http://sourceforge.net/projects/bigace/                  |
// +------------------------------------------------------------------------+
// | This source file is subject to version 2 or (at your option) any later |
// | version, of the GNU General Public License as published by the Free    |
// | Software Foundation, available at:                                     |
// | http://www.gnu.org/licenses/gpl.html                                   |
// +------------------------------------------------------------------------+
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// +------------------------------------------------------------------------+
//

/**
 * This command displays a login formular OR performs a login for a username and password.
 *
 * Pass the parameter <code>_REDIRECT_CMD</code> and <code>_REDIRECT_ID</code> to build a url to redirect to.
 * If none of them is submitted, we will redirect to the current menu ID.
 *
 * To login a user pass the following $_POST variables:
 * - $_POST['UID']
 * - $_POST['PW']
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.command
 */

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

/**
 * Constant for the Redirect Command POST Parameter.
 */
define('_REDIRECT_COMMAND', 'REDIRECT_CMD');
/**
 * Constant for the Redirect Item ID POST Parameter.
 */
define('_REDIRECT_ID', 'REDIRECT_ID');
/**
 * Constant for the Password POST Parameter.
 */
define('_PARAM_PASSWORD', 'PW');
/**
 * Constant for the Username POST Parameter.
 */
define('_PARAM_USERNAME', 'UID');

import('classes.core.ServiceFactory');
import('classes.language.Language');
import('classes.util.CMSLink');
import('classes.util.LinkHelper');
import('classes.logger.LogEntry');
import('classes.exception.ExceptionHandler');
import('classes.exception.AuthenticationException');
import('classes.util.ApplicationLinks');
import('classes.smarty.BigaceSmarty');
import('classes.smarty.SmartyTemplate');

require_once(_BIGACE_DIR_LIBS.'sanitize.inc.php');

// load translations
loadLanguageFile('login', _ULC_);

// next variables are meant to be displayed in the formular
$formError = null;
session_regenerate_id();

if(isset($_POST) && count($_POST) > 0)
{
    $userLoginName = "";
    if (!isset($_POST[_PARAM_USERNAME]) || strlen(trim($_POST[_PARAM_USERNAME])) == 0) {
        $formError = getTranslation("login_error_username");
    }
    else {
        $userLoginName = sanitize_user($_POST[_PARAM_USERNAME]);
    }

    $userLoginPass = "";
    if (!isset($_POST[_PARAM_PASSWORD]) || strlen(trim($_POST[_PARAM_PASSWORD])) == 0) {
        $formError = getTranslation("login_error_password");
    }
    else {
        $userLoginPass = sanitize_plain_text($_POST[_PARAM_PASSWORD]);
    }

    if(is_null($formError))
    {
        $services = ServiceFactory::get();
        $AUTHENTICATOR = $services->getAuthenticator();

        $auth = $AUTHENTICATOR->authenticate($userLoginName, $userLoginPass);

        unset($userLoginName);
        unset($userLoginPass);

        if ($auth === AUTHENTICATE_UNKNOWN)
        {
            $formError = getTranslation("login_error");
        }
        else
        {
            $le = new LogEntry(LOGGER_LEVEL_INFO,'User "'.$auth->getName().'" logged in',LOGGER_NAMESPACE_AUTHENTICATION);
	        $GLOBALS['LOGGER']->logEntry($le);

            $GLOBALS['_BIGACE']['SESSION']->setUserByID( $auth->getID() );

            $didLang = false;
            if(get_option('login', 'login.with.user.language')) {
                $GLOBALS['_BIGACE']['SESSION']->setLanguage( $auth->getLanguageID() );
                $didLang = true;
            } else if(isset($_POST['language'])) {
                $logLang = new Language($_POST['language']);
                if($logLang->isValid()) {
                    $GLOBALS['_BIGACE']['SESSION']->setLanguage( $logLang->getID() );
                    $didLang = true;
                }
            }

            if(!$didLang) {
                $GLOBALS['_BIGACE']['SESSION']->setLanguage( $auth->getLanguageID() );
            }

            unset ($newlang);
            unset ($auth);

            // append all values to the redirect url, that are not system or login specific
            $values = array();
            foreach ($_POST AS $key => $val)
            {
                if (is_array($val))
                {
                    foreach ($val AS $a => $b) {
                        if ($a != _PARAM_USERNAME && $a != _PARAM_PASSWORD) {
                            $values[$key.'['.$a.']'] = $b;
                        }
                    }
                }
                else if (!isSystemParameter($key) && $key != _REDIRECT_COMMAND && $key != _PARAM_USERNAME && $key != _PARAM_PASSWORD && $key != 'submit' && $key != "language")
                {
                    $values[$key] = $val;
                }
            }

            // building a URL by using possible submitted and default values
            $id = $GLOBALS['_BIGACE']['PARSER']->getItemID();
	        $cmd = _BIGACE_CMD_MENU;

            if (isset($_POST[_REDIRECT_ID])) {
                $id = $_POST[_REDIRECT_ID];
            }
            else if (isset($_GET[_REDIRECT_ID])) {
                $id = $_GET[_REDIRECT_ID];
            }

            if(isset($_POST[_REDIRECT_COMMAND])) {
                $cmd = $_POST[_REDIRECT_COMMAND];
            }
            else if(isset($_GET[_REDIRECT_COMMAND])) {
                $cmd = $_GET[_REDIRECT_COMMAND];
            }

	        if($cmd == _BIGACE_CMD_MENU) {
		        import('classes.menu.MenuService');
		        $ms = new MenuService();
		        $menu = $ms->getMenu($id, $GLOBALS['_BIGACE']['PARSER']->getLanguage());
		        $link = LinkHelper::getCMSLinkFromItem($menu);
	        }
	        else {
		        $link = new CMSLink();
		        $link->setItemID($id);
		        $link->setCommand($cmd);
	        }

	        foreach($values AS $key => $val)
		        $link->addParameter($key, $val);

	        $link->setUseSSL(false);

            $GLOBALS['LOGGER']->logDebug('Authenticate and redirect: '.LinkHelper::getUrlFromCMSLink($link));
            header("Location: " . LinkHelper::getUrlFromCMSLink($link));
            exit;
        }
    }
} // end login by post


// display formular
$LANGUAGE = new Language(_ULC_);

$hiddenParams = array();

if (isset($_GET['REDIRECT_ID'])) {
    $hiddenParams[] = '<input type="hidden" name="REDIRECT_ID" value="'.sanitize_plain_text($_GET['REDIRECT_ID']).'">';
}

if (isset($_GET['REDIRECT_CMD'])) {
    $hiddenParams[] = '<input type="hidden" name="REDIRECT_CMD" value="'.sanitize_plain_text($_GET['REDIRECT_CMD']).'">';
}

foreach ($_POST AS $key => $val) {
    if (is_array($val)) {
        foreach ($val AS $a => $b) {
            $hiddenParams[] = '<input type="hidden" name="'.sanitize_plain_text($key).'['.sanitize_plain_text($a).']'.'" value="'.sanitize_plain_text($b).'">';
        }
    }
    else if (!isSystemParameter($key)) {
        $hiddenParams[] = '<input type="hidden" name="'.sanitize_plain_text($key).'" value="'.sanitize_plain_text($val).'">';
    }
}

$minchars = (int)ConfigurationReader::getConfigurationValue('authentication', 'username.minimum.length', 5);

header( "Content-Type:text/html; charset=" . $LANGUAGE->getCharset() );

$LOGIN_TPL = new SmartyTemplate( ConfigurationReader::getConfigurationValue('templates', 'auth.login', 'AUTH-LOGIN') );

$loginSmarty = BigaceSmarty::getSmarty();
$loginSmarty->assign('AUTH_DIR', _BIGACE_DIR_PUBLIC_WEB . 'system/');
$loginSmarty->assign('LANGUAGE', $LANGUAGE);
$loginSmarty->assign('ERROR', $formError);
$loginSmarty->assign('CHARSET', $LANGUAGE->getCharset());
$loginSmarty->assign('CANCEL', $GLOBALS['_BIGACE']['PARSER']->getItemID());
$loginSmarty->assign('ACTION', ApplicationLinks::getLoginURL());
$loginSmarty->assign('HIDDEN', $hiddenParams);
$loginSmarty->assign('USERNAME_LENGTH', $minchars);

$loginSmarty->display( $LOGIN_TPL->getFilename() );
