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
 * @subpackage logger
 */

import('classes.logger.DBLogger');
require_once(_BIGACE_DIR_ADDON.'firephp/lib/FirePHPCore/fb.php');

/**
 * This Logger uses the FirePHP framework to send all messages
 * to your FireBug extension in Firefox.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage logger
 */
class FirePHPLogger extends DBLogger
{
	

    /**
     * Pass a Namespace for your Log Instance if desired.
     * Otherwise you log into the default namespace.
     * 
     * @param String the Namespace
     */
    function FirePHPLogger($namespace = '') 
    {
		ob_start();
    	$this->DBLogger(null);
    }
    
    /**
     * @access private
     */
    function insertFullLogEntry($level, $message, $filename = '', $linenum = '', $stacktrace = '') 
    {
    	// if it is a string, it might be a FirePHP loglevel
        if (is_string($level) || $this->isModeEnabled($level))
        {	
        	if(!isset($GLOBALS['_BIGACE']['SESSION'])) {
        		echo '<span style="color:red"><b>' . $message . '</b></span><br/>';		
        	}
        	else {
                if($linenum != '' && $filename != '')
    				fb(' ['.$level.'] ' . $this->getDescriptionForMode($level) . ': ' . $message . ' at ' . $linenum . '/' . $filename, $this->mapLevel($level));
                else
    				fb(' ['.$level.'] ' . $this->getDescriptionForMode($level) . ': ' . $message , $this->mapLevel($level));        	
        	}
        }
    }
    
    /**
     * Maps a loglevel to a FirePHP level. 
     */
    private function mapLevel($logLevel) 
    {
    	// if this was a FirePHP loglevel, return it
    	switch($logLevel) {
	        case FirePHP::LOG:
	        case FirePHP::INFO:
	        case FirePHP::WARN:
	        case FirePHP::ERROR:
	        case FirePHP::DUMP:
	        case FirePHP::TRACE:
	        case FirePHP::EXCEPTION:
	        case FirePHP::TABLE:
	        	return $logLevel;
    	}
    	
		// otherwise convert it, using the following rules
    	switch($logLevel)
    	{
    		case E_STRICT:
    		case E_DEBUG:
    		case E_SQL:
    			return FirePHP::INFO;
    			break;
    		case E_USER_WARNING:
    		case E_USER_NOTICE:
    			return FirePHP::WARN;
    			break;
    		case E_ERROR:
    		case E_WARNING:
    		case E_PARSE:
    		case E_NOTICE:
    		case E_CORE_ERROR:
    		case E_CORE_WARNING:
    		case E_COMPILE_ERROR:
    		case E_COMPILE_WARNING:
    		case E_USER_ERROR:
    			return FirePHP::ERROR;
    			break;
    	}
    	return FirePHP::ERROR;
    }

}

?>