<?php
/**
 * This script displays the user registration formular.
 *
 * Copyright (C) Kevin Papst.
 *
 * For further information go to http://www.bigace.de/
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @author Kevin Papst
 * @package bigace.application.auth
 */

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

import('classes.util.links.LoginFormularLink');
import('classes.util.links.PasswordLink');
import('classes.util.LinkHelper');
import('classes.logger.LogEntry');
import('classes.smarty.BigaceSmarty');
import('classes.smarty.SmartyTemplate');
import('classes.core.ServiceFactory');
import('classes.group.GroupAdminService');
import('classes.util.formular.LanguageSelect');

require_once(_BIGACE_DIR_LIBS . 'sanitize.inc.php');

$username = '';
$email = '';
$error = '';
$success = false;

loadLanguageFile("bigace");
$LANGUAGE = new Language( _ULC_ );

if(isset($_POST['password']) && $_POST['password'] == 'do')
{

    // ---------- email check -----------------------
    if(isset($_POST['email']) && trim($_POST['email']) != '') {
        $email = sanitize_email($_POST['email']);
    }

    // ---------- password check --------------------
    if(isset($_POST['username']) && trim($_POST['username']) != '') {
        $username = sanitize_plain_text($_POST['username']);
    }

    if($email == "" && $username == "") {
        $error = 'password_notfound';
    }

    if($error == '')
    {
        $realEmail = "";
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        //check that username is not already in use!
        $princ = $PRINCIPALS->lookup($username);

        if ($princ == null) {
            $princ = $PRINCIPALS->lookupByAttribute('email', $email);
        }

        if ($princ != null)
        {
        	$allowAdminReset = ConfigurationReader::getValue('authentication', 'admin.password.reset', false);
        	if($princ->getID() != _AID_ && ($princ->getID() != _BIGACE_SUPER_ADMIN || $allowAdminReset))
        	{
	            $username = $princ->getName();
	            $realEmail = $princ->getEmail();

	            if(strlen($realEmail) > 5)
	            {
	                $email = $realEmail;
	                $newPass = getRandomString();

	                if(strlen($newPass) > 8) {
	                    $newPass = substr($newPass, 0, 8);
	                }

	                $PRINCIPALS->setParameter($princ, PRINCIPAL_PARAMETER_PASSWORD, $newPass);

	                $loginLink = new LoginFormularLink();
	                $siteName = ConfigurationReader::getConfigurationValue('authentication', 'welcome.email.sitename', $_SERVER['HTTP_HOST']);
	                $emailFrom = ConfigurationReader::getConfigurationValue('authentication', 'welcome.email.from', '');
	                if($emailFrom == "")
					    $emailFrom = ConfigurationReader::getConfigurationValue("community", "contact.email", '');

	                $subject = sprintf(getTranslation('password_email_subject', 'Your password at %s'), $siteName);

	                // email, username, password, sitename, login link, home link
	                loadLanguageFile("login", _ULC_);
	                $emailText = sprintf(getTranslation('password_email_msg'), $email, $username, $newPass, $siteName,
	                                     LinkHelper::getUrlFromCMSLink($loginLink), BIGACE_HOME);

	                import('classes.email.TextEmail');
	                $emailObject = new TextEmail();
		            $emailObject->setTo($email);
	                $emailObject->setContent($emailText);
	                $emailObject->setFromName($siteName);
	                $emailObject->setFromEmail($emailFrom);
	                $emailObject->setSubject($subject);
	                $emailObject->setCharacterSet($LANGUAGE->getCharset());

	                $le = new LogEntry(LOGGER_LEVEL_INFO,"Created new password for " . $username,LOGGER_NAMESPACE_AUTHENTICATION);
	                $GLOBALS['LOGGER']->logEntry($le);

	                if($emailObject->sendMail()) {
	                    $success = true;
	                }
	                else {
	                   	$ae = new LogEntry(LOGGER_LEVEL_ERROR,'Could not send password reminder email to '.$email);
	                	$GLOBALS['LOGGER']->logEntry($ae);
	                	$error = 'login_mailfailed';
	                }
	            }
	            else {
	                $error = 'password_noemail';
	            }
        	}
            else {
	            // fake not existing user
            	$error = 'password_notfound';
            }
        }
        else {
            $error = 'password_notfound';
        }
    }

}

$REGISTER_TPL = new SmartyTemplate( ConfigurationReader::getConfigurationValue('templates', 'auth.password', 'AUTH-PASSWORD') );

$link = new PasswordLink();

$loginSmarty = BigaceSmarty::getSmarty();
$loginSmarty->assign('CHARSET', $LANGUAGE->getCharset());
$loginSmarty->assign('AUTH_DIR', _BIGACE_DIR_PUBLIC_WEB . 'system/');
$loginSmarty->assign('LANGUAGE', $LANGUAGE);
$loginSmarty->assign('HOME', BIGACE_HOME);
$loginSmarty->assign('CANCEL', $GLOBALS['_BIGACE']['PARSER']->getItemID());
$loginSmarty->assign('ACTION', LinkHelper::getUrlFromCMSLink($link));
$loginSmarty->assign('USERNAME', sanitize_plain_text($username));
$loginSmarty->assign('EMAIL', sanitize_plain_text($email));
$loginSmarty->assign('ERROR', $error);

$loginSmarty->assign('SUCCESS', $success);
$loginSmarty->display( $REGISTER_TPL->getFilename() );

?>