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

// do not let people use this
if(!ConfigurationReader::getConfigurationValue('authentication', 'allow.self.registration', false)) {
    import('classes.exception.ExceptionHandler');
    import('classes.exception.CoreException');
    ExceptionHandler::processCoreException( new CoreException(403, 'Self-registration was accessed, but it is deactivated!') );
    die();
}

import('classes.util.links.ActivationLink');
import('classes.util.links.RegistrationLink');
import('classes.util.LinkHelper');
import('classes.logger.LogEntry');
import('classes.exception.AuthenticationException');
import('classes.smarty.BigaceSmarty');
import('classes.smarty.SmartyTemplate');
import('classes.core.ServiceFactory');
import('classes.group.GroupAdminService');
import('classes.util.formular.LanguageSelect');

define('USERNAME_MINCHARS', (int)ConfigurationReader::getConfigurationValue('authentication', 'username.minimum.length', 5));
define('PASSWORD_MINCHARS', (int)ConfigurationReader::getConfigurationValue('authentication', 'password.minimum.length', 5));
define('CAPTCHA_MINCHARS', (int)ConfigurationReader::getConfigurationValue('authentication', 'captcha.minimum.length', 4));
define('CAPTCHA_MAXCHARS', (int)ConfigurationReader::getConfigurationValue('authentication', 'captcha.maximum.length', 6));

require_once(_BIGACE_DIR_LIBS . 'sanitize.inc.php');

$username = '';
$email = '';
$language = '';
$error = '';
$success = false;
$supportCaptcha = function_exists('imagejpeg');

loadLanguageFile("bigace");
$LANGUAGE = new Language( _ULC_ );

$additionalFields = array();
$addFldStr = ConfigurationReader::getConfigurationValue('authentication', 'registration.additional.fields', "");

if(strlen(trim($addFldStr)) > 3)
{
	$allFileds = explode(",", $addFldStr);
	foreach($allFileds AS $oneField)
	{
		$allFiledsParams = explode("|", $oneField);
		if(count($allFiledsParams) >= 3 && $allFiledsParams[0] != "" && $allFiledsParams[1] != "" && $allFiledsParams[2] != "") {
			$thisOne = array(
				'title'	=> $allFiledsParams[0],
				'name'	=> $allFiledsParams[1],
				'type'	=> $allFiledsParams[2],
				'desc'	=> (isset($allFiledsParams[3]) ? $allFiledsParams[3] : ""),
				'value'	=> (isset($allFiledsParams[4]) ? $allFiledsParams[4] : ""),
			);
			$additionalFields[] = $thisOne;
		}
		else {
			$GLOBALS['LOGGER']->logError("Wrong field config for registration: " . $oneField);
		}
	}
}


if(isset($_POST['register']) && $_POST['register'] == 'do') {

    // validate captcha values if they are used
    if($supportCaptcha)
    {
	    if(!isset($_POST['captcha']) || !isset($_POST['validate'])) {
	        $error = 'register_enter_captcha';
	    }
	    else {
    		$captcha = ConfigurationReader::getValue("system", "captcha", null);
			if($captcha == null) {
		        $error = 'register_captcha_failed';
	        	$GLOBALS['LOGGER']->logError("Captcha failed, wrong configuration: 'system/captcha'");
			}
			else {
			    if($captcha->validate($_POST['captcha'],$_POST['validate']) === false) {
			        $error = 'register_captcha_failed';
			    }
			}
	    }
    }

    // password check
    if(!isset($_POST['pwdrecheck']) || !isset($_POST['password']) || trim($_POST['password']) == '') {
        $error = 'login_password_check';
    } else if($_POST['pwdrecheck'] != $_POST['password']) {
        $error = 'register_password_match';
    } else if(strlen(trim($_POST['password'])) < PASSWORD_MINCHARS) {
        $error = 'register_password_short';
    }

    //email check
    if(!isset($_POST['email']) || trim($_POST['email']) == '') {
        $error = 'register_enter_email';
    } else {
        $email = sanitize_email($_POST['email']);
    }

    //language check
    if(!isset($_POST['language']) || trim($_POST['language']) == '') {
        $error = 'register_enter_language';
    } else {
        $language = $_POST['language'];
    }

    //username check
    if(!isset($_POST['username']) || trim($_POST['username']) == '') {
        $error = 'login_username_check';
    } else if(strlen(trim($_POST['username'])) < USERNAME_MINCHARS) {
        $error = 'register_username_short';
        $username = $_POST['username'];
    } else if(strcmp($_POST['username'], sanitize_user($_POST['username'], true)) !== 0) {
        $error = 'login_username_check';
        $username = sanitize_user($_POST['username'], true);
    } else {
    	$username = sanitize_user($_POST['username'], true);
    }

    if($error == '') {
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        //check that username is not already in use!
        $princ = $PRINCIPALS->lookup($username);
        if ($princ == null) {
            //check that email adress is not already in use!
            $princ = $PRINCIPALS->lookupByAttribute('email', $email);
            if ($princ == null) {
	            $newGroup = ConfigurationReader::getConfigurationValue('authentication', 'default.group.registration', 0);
	            $newPass  = $_POST['password'];

	            // everything was correct, now lets create a new user!
	            $newPrincipal = $PRINCIPALS->createPrincipal($username, $newPass, $language);
	            if($newPrincipal != null) {
	                $activationCode = '';
	                do {
	                    //check that the activation code is not already existing, otherwise recreate!
	                    $activationCode = getRandomString();
	                    $princ = $PRINCIPALS->lookupByAttribute('activation', $activationCode);
	                } while ($princ != null);

	                $PRINCIPALS->setParameter($newPrincipal, PRINCIPAL_PARAMETER_ACTIVE, false);
	                $PRINCIPALS->setParameter($newPrincipal, PRINCIPAL_PARAMETER_EMAIL, $email);
	                $PRINCIPALS->setParameter($newPrincipal, PRINCIPAL_PARAMETER_LANGUAGE, $language);
	                $PRINCIPALS->setAttribute($newPrincipal, 'activation', $activationCode);

					foreach($additionalFields AS $checkField) {
						if(isset($_POST[$checkField['name']]))
					        $PRINCIPALS->setAttribute($newPrincipal, $checkField['name'], $_POST[$checkField['name']]);
					}
	                // assign newly created user to configured groups
	                $groupAdmin = new GroupAdminService();
	                $groupAdmin->addToGroup($newGroup, $newPrincipal->getID());
	                // assign to anonymous group if configured
	                if($newGroup != 0 && ConfigurationReader::getConfigurationValue('authentication', 'anonymous.group.registration', true))
	                    $groupAdmin->addToGroup(0, $newPrincipal->getID());

	                $success = true;

	                $activateLink = new ActivationLink($activationCode);
                    $siteName = ConfigurationReader::getConfigurationValue('authentication', 'welcome.email.sitename', $_SERVER['HTTP_HOST']);
                    $emailFrom = ConfigurationReader::getConfigurationValue('authentication', 'welcome.email.from', '');
                    if($emailFrom == "")
						$emailFrom = ConfigurationReader::getConfigurationValue("community", "contact.email", '');

                    $subject = sprintf(getTranslation('register_email_subject', 'Confirm your registration at %s'), $siteName);

	                // 1 = username
	                // 2 = password
	                // 3 = email
	                // 4 = activation link
	                // 5 = activation code
	                // 6 = language
	                // 7 = character set
	                // 8 = sitename
	                // 9 = home
	                loadLanguageFile("login", $language);
	                $emailText = sprintf(getTranslation('email_register'), $username, $newPass, $email, LinkHelper::getUrlFromCMSLink($activateLink),
	                                        $activationCode, $newLanguage, $LANGUAGE->getCharset(), $siteName, BIGACE_HOME);

	                import('classes.email.TextEmail');
	                $emailObject = new TextEmail();
	                // send email about registration to admin if 'register_notify_email' is set
					$notify_email = ConfigurationReader::getConfigurationValue('authentication', 'send.notification.to', false);
					if (preg_match("/^[^@]*@[^@]*\.[^@]*$/", $notify_email)) {
						loadLanguageFile("login", _ULC_);
						$notifyText = sprintf(getTranslation('register_notify_email'), $username, date("r"));
						$emailObject->setTo($notify_email);
						$emailObject->setContent($notifyText);
						$emailObject->setFromName($siteName);
						$emailObject->setFromEmail($emailFrom);
						$emailObject->setSubject(sprintf(getTranslation('login_register', '%s - New account has been created'), $siteName));
						$emailObject->setCharacterSet("utf-8");

						if(!$emailObject->sendMail())
						{
							$ae = new LogEntry(LOGGER_LEVEL_ERROR,'Could not send notification email to admin for user: ' . $username);
							$GLOBALS['LOGGER']->logEntry($ae);
							$error = 'login_mailfailed';
						}
					}

	                // $emailObject = new TextEmail();
        	        $emailObject->setTo($email);
                    $emailObject->setContent($emailText);
                    $emailObject->setFromName($siteName);
                    $emailObject->setFromEmail($emailFrom);
                    $emailObject->setSubject($subject);
                    $emailObject->setCharacterSet($LANGUAGE->getCharset());

	                $le = new LogEntry(LOGGER_LEVEL_INFO,"Created user: " . $username . "(".$email.")",LOGGER_NAMESPACE_AUTHENTICATION);
	                $GLOBALS['LOGGER']->logEntry($le);

	                if(!$emailObject->sendMail())
	                {
	                	$ae = new LogEntry(LOGGER_LEVEL_ERROR,'Could not send registration email to '.$email.' for user: ' . $username);
	                	$GLOBALS['LOGGER']->logEntry($ae);
                		$error = 'login_mailfailed';
	                }
	            }
            }
            else {
                $error = 'register_email_exists';
            }
        } else {
            $error = 'register_username_exists';
        }

    }

}

if ($success) {
	$LANGUAGE = new Language($language);
	$GLOBALS['_BIGACE']['SESSION']->setLanguage($LANGUAGE);
}

$REGISTER_TPL = new SmartyTemplate( ConfigurationReader::getConfigurationValue('templates', 'auth.register', 'AUTH-REGISTER') );

$link = new RegistrationLink();

$loginSmarty = BigaceSmarty::getSmarty();
$loginSmarty->assign('AUTH_DIR', _BIGACE_DIR_PUBLIC_WEB . 'system/');
$loginSmarty->assign('CHARSET', $LANGUAGE->getCharset());
$loginSmarty->assign('LANGUAGE', $LANGUAGE);
$loginSmarty->assign('HOME', BIGACE_HOME);
$loginSmarty->assign('CANCEL', $GLOBALS['_BIGACE']['PARSER']->getItemID());
$loginSmarty->assign('ACTION', LinkHelper::getUrlFromCMSLink($link));
$loginSmarty->assign('USERNAME', $username);
$loginSmarty->assign('USERNAME_LENGTH', USERNAME_MINCHARS);
$loginSmarty->assign('PASSWORD_LENGTH', PASSWORD_MINCHARS);
$loginSmarty->assign('EMAIL', $email);
$loginSmarty->assign('ERROR', $error);
$loginSmarty->assign('ADDITIONAL_FIELDS', $additionalFields);
$loginSmarty->assign('USE_CAPTCHA', $supportCaptcha);
$loginSmarty->assign('CAPTCHA_MIN', CAPTCHA_MINCHARS);
$loginSmarty->assign('CAPTCHA_MAX', CAPTCHA_MAXCHARS);
$loginSmarty->assign('SUCCESS', $success);
$loginSmarty->display( $REGISTER_TPL->getFilename() );

?>