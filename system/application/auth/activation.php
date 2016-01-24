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
    ExceptionHandler::processCoreException( new CoreException(403, 'Self-activation was accessed, but it is deactivated!') );
    die();
}

import('classes.util.links.ActivationLink');
import('classes.util.LinkHelper');
import('classes.smarty.BigaceSmarty');
import('classes.smarty.SmartyTemplate');
import('classes.core.ServiceFactory');

$username = '';
$email = '';
$error = '';
$success = false;

$LANGUAGE = new Language( $GLOBALS['_BIGACE']['PARSER']->getLanguage() );

if(isset($_GET['activation']) || isset($_POST['activation'])) {

	$code = (isset($_GET['activation']) ? $_GET['activation'] : (isset($_POST['activation']) ? $_POST['activation'] : null));

	if($code != null && $code != '') {
	    $services = ServiceFactory::get();
	    $PRINCIPALS = $services->getPrincipalService();
	    $princ = $PRINCIPALS->lookupByAttribute('activation',$code);
	    if (count($princ) > 0) {
            $princ = $princ[0];
	    	if($princ->isActive()) {
	    		$error = 'activate_error_active';
	    	} else {
		        $PRINCIPALS->setParameter($princ, PRINCIPAL_PARAMETER_ACTIVE, true);
                $PRINCIPALS->setAttribute($princ, 'activation', getRandomString());
                $success = true;
                $username = $princ->getName();
	    	}
	    } else {
	    	$error = 'activate_error_notfound';
	    }
	}
}

$ACTIVATION_TPL = new SmartyTemplate( ConfigurationReader::getConfigurationValue('templates', 'auth.activate', 'AUTH-ACTIVATE') );

$link = new ActivationLink();

$loginSmarty = BigaceSmarty::getSmarty();
$loginSmarty->assign('AUTH_DIR', _BIGACE_DIR_PUBLIC_WEB . 'system/');
$loginSmarty->assign('CHARSET', $LANGUAGE->getCharset());
$loginSmarty->assign('HOME', BIGACE_HOME);
$loginSmarty->assign('ACTION', LinkHelper::getUrlFromCMSLink($link));
$loginSmarty->assign('USERNAME', $username);
$loginSmarty->assign('ERROR', $error);
$loginSmarty->assign('SUCCESS', $success);
$loginSmarty->display( $ACTIVATION_TPL->getFilename() );

?>