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
 * This is the main script for all command calls.
 *
 * Have a look at 'system/libs/init_session.inc.php' to know
 * how a Session is correct initialized.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 */

require_once (dirname(__FILE__).'/../system/libs/init_session.inc.php');

if(ConfigurationReader::getConfigurationValue('system', 'send.pragma.no.cache', true))
{
    $now = gmdate('D, d M Y H:i:s') . ' GMT';
    header('Expires: ' . $now);   // rfc2616 - Section 14.21
    //header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . $now);
    //header('Last-Modified: ' . gmdate( "D, d M Y H:i:s" ) . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate, pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
    header('Pragma: no-cache'); // HTTP/1.0
    unset($now);
}

$bg_uo = $GLOBALS['_BIGACE']['SESSION']->getUser();

if($bg_uo->isValidUser())
{
	if($bg_uo->isActive())
	{
		unset($bg_uo);	
	
        // LOG STATISTIC ACCESS IF ENABLED
        if(ConfigurationReader::getConfigurationValue('system', 'write.statistic', false) && $GLOBALS['_BIGACE']['PARSER']->getCommand() != 'admin')
        {
        	import('classes.statistic.StatisticWriter');

        	$stat = new StatisticWriter();

            $referer = '';
            if(function_exists('apache_request_headers')) {
                $headers = apache_request_headers();
                $referer = isset($headers['Referer']) ? $headers['Referer'] : '';
            }

        	$stat->writeStatistic($GLOBALS['_BIGACE']['PARSER']->getCommand(),$GLOBALS['_BIGACE']['PARSER']->getItemID(),$_SERVER['REMOTE_ADDR'],
        	                      (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''), $referer, $GLOBALS['_BIGACE']['SESSION']->getSessionID(), $GLOBALS['_BIGACE']['SESSION']->getUserID(), _CID_);
        	unset($referer);
        	unset($headers);
        	unset($stat);
        }

        // PROCESSING COMMAND INTERPRETER
        $cmd = $GLOBALS['_BIGACE']['PARSER']->getCommand();
        $cmd2 = preg_replace('/[^a-z0-9]/', '', $cmd); // sanitize command
        $COMMAND_FILE = _BIGACE_DIR_ROOT . '/system/command/' . $cmd2 . '.cmd.php';
        if(strcmp($cmd,$cmd2) == 0 && file_exists($COMMAND_FILE)) 
        {
            include_once ($COMMAND_FILE);
            $GLOBALS['LOGGER']->logDebug('Executed Command "'. $cmd2 . '" for URL "' . $GLOBALS['_BIGACE']['PARSER']->getLink() . '"');
        }
        else /* Command could not be found */
    	{
			import('classes.exception.ExceptionHandler');
			import('classes.exception.NotFoundException');
		    ExceptionHandler::processCoreException( new NotFoundException(404, 'CMD not found ['. $cmd2 .'] using ['.$_SERVER['REQUEST_URI'].']') );
    	}
        unset ($COMMAND_FILE);
	}
	else
	{
		// User is DEACTIVATED, deny access - only if user is deactivated while being logged in
		import('classes.exception.ExceptionHandler');
		import('classes.exception.AuthenticationException');

	    ExceptionHandler::processCoreException( new AuthenticationException('deactivated', 'User is deactivated!') );
	}
}
else
{
	// User is INVALID - only if user is deleted while being logged in
	import('classes.exception.ExceptionHandler');
	import('classes.exception.AuthenticationException');

    ExceptionHandler::processCoreException( new AuthenticationException(401, 'Unknown or invalid User!') );
}

?>