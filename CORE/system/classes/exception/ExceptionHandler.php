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
 * @subpackage exception
 */

/**
 * Class used for handling Exceptions.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage exception
 */
class ExceptionHandler
{

    private static function displayExceptionSmarty(CoreException $exception)
    {
        import('classes.util.ApplicationLinks');
        import('classes.smarty.BigaceSmarty');
        import('classes.smarty.SmartyTemplate');
        $smarty = BigaceSmarty::getSmarty();

        $tplName = get_option("templates", $exception->getCode(), "MAINTENANCE");

        $ERROR_TPL = new SmartyTemplate( $tplName );
        $smarty->assign('USER', $GLOBALS['_BIGACE']['SESSION']->getUser());
        $smarty->assign('HOME_URL', ApplicationLinks::getHomeURL());
        $smarty->assign('MESSAGE', $exception->getMessage());
        $smarty->display($ERROR_TPL->getFilename());
    }

    private static function displayExceptionPHP(CoreException $exception)
    {
        import('classes.layout.Layout');

    	$LAYOUT = new Layout($exception->getLayout(), 'error-' . $exception->getCode());
    	if($LAYOUT->existsKey())
    	{
    		include_once($LAYOUT->getFullUrl());

    		// Do not use a redirect here. Results in a Endless loop of redirects in the
    		// case that the top level menu is not readable for anonymous user!
            //header("Location: " . createMenuLink(_BIGACE_TOP_LEVEL . $LAYOUT->toString()));
            //exit();

            return true;
    	}
    	return false;
    }

    /**
     * Processes the Exception by including the desired Exception Template.
     * This is found by calling <code>new Layout('ERROR', 'error-' . $exception->getCode())</code>.
     *
     * @param CoreException the CoreException that should be processed
     * @access public
     */
    public static function processCoreException(CoreException $exception)
    {
    	// if the argument was a Subclass of Exception or at least a Exception itself, process it
    	if (ExceptionHandler::isException($exception))
    	{
            $worked = false;

    	    if ($exception->isSmarty()) {
        	    $worked = ExceptionHandler::displayExceptionSmarty($exception);
    	    }
    	    else {
        	    $worked = ExceptionHandler::displayExceptionPHP($exception);
    	    }

	    	if($worked === false)
	    	{
		    	$GLOBALS['LOGGER']->logError('FATAL: Could not find Error Template ('.$exception->getLayout() . ', error-' . $exception->getCode().') for Core Exception (' . $exception->toString(). ')');
	    		die('Could not process CoreException. Missing Error Template!');
	    	}

            if($exception->logException()) {
                if($exception instanceof NotFoundException) {
                    $GLOBALS['LOGGER']->logInfo($exception->toString(), true);
                } else {
                    $GLOBALS['LOGGER']->logError($exception->toString(), true);
                }
            }
    	}
    	else
    	{
	    	$GLOBALS['LOGGER']->logError('processCoreException() was called with wrong parameter: ' . print_r($exception,true));
    	}
    }

    /**
     * This kind of Exception should only be used by the KERNEL.
     * Error messages that are thrown via this handler can NOT be customized for each Consumer!
     *
     * @param CoreException the CoreException that should be processed
     * @access public
     */
    public static function processSystemException($exception)
    {
    	if (ExceptionHandler::isException($exception))
    	{
    		$filename = _BIGACE_DIR_PUBLIC.'system/error/error_'.$exception->getCode().'.php';
    		if (file_exists($filename))
    		{
    			include_once($filename);
    		}
	    	else
	    	{
		    	$GLOBALS['LOGGER']->logError('FATAL: Could not find Error Template ('.$exception->getLayout() . ', error-' . $exception->getCode().') at URL ('.$filename.') for System Exception (' . $exception->toString(). ')');
	    		die('Could not process SystemException. Missing Error Template!');
	    	}

            if($exception->logException()) {
    	    	//$GLOBALS['LOGGER']->logError('SystemException:  ' . $exception->toString());
            	$GLOBALS['LOGGER']->logError($exception->toString());
            }
    	}
    	else
    	{
	    	$GLOBALS['LOGGER']->logError('processSystemException() was called with wrong parameter: ' . print_r($exception,true));
    	}
    }


	/**
	 * Display a Exception that was caused within the Administration.
	 * Do NOT use this method outside the Administration environment.
	 *
	 * @param CoreException the Exception to Display
	 */
	public static function processAdminException($exception)
	{
    	if (ExceptionHandler::isException($exception, 'AdministrationException') && defined('_VALID_ADMINISTRATION'))
		{
			$filename = 'Error'.$exception->getCode().'.tpl.html';
			if($GLOBALS['LOGGER']->isDebugEnabled())
			{
				$GLOBALS['LOGGER']->logDebug('Fetched AdminException: ' . $exception->getCode());
				$GLOBALS['LOGGER']->logDebug('Trying to load Template File: ' . $filename);
			}

			if (!isset($GLOBALS['TEMPLATE_SERVICE']))
			{
				$GLOBALS['LOGGER']->logError('FATAL: Could not process Exception - TemplateService is not initialized!');
			}
			else
			{
				$url = 'javascript:history.back()';
				if($exception->getURL() != '')
					$url = $exception->getURL();

			    $tpl = $GLOBALS['TEMPLATE_SERVICE']->addTemplateDirectory($GLOBALS['_BIGACE']['style']['class']->getFileSystemDirectory().'error/');
			    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile($filename, false, true);

				$tpl->setVariable("MESSAGE", $exception->getMessage());
				$tpl->setVariable("LINK_TITLE", 'Admin Index');
				$tpl->setVariable("LINK_URL", createAdminLink(_ADMIN_ID_MAIN));
				$tpl->setVariable("BACK_URL", $url);
				$tpl->setVariable("HOME", BIGACE_HOME);
				$tpl->show();
			}
		}
		else
		{
	    	$GLOBALS['LOGGER']->logError('displayAdminException() was called with wrong parameter: ' . print_r($exception,true));
		}

        if($exception->logException()) {
        	$GLOBALS['LOGGER']->logError($exception->toString(), true);
        }
	}

	/**
	 * Checks if the given Object is a Exception we can work with!
	 *
	 * @param Exception the Exception to check
	 * @param String Name of the Class to check
	 * @return boolean true if it is of correct Object type, otherwise false
	 * @access private
	 */
	public static function isException($exception, $classToCheck = 'CoreException')
	{
		return (is_subclass_of($exception, $classToCheck) || strcasecmp(get_class($exception),$classToCheck) == 0);
	}

}

?>