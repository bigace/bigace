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
	
/**
 * This Logger can be seen as Abstract implementation and holds all Common methods.
 * It simply ECHOs the enabled log messages and is NOT meant for debugging/development, NOT for production systems.
 *
 * It takes a log level within the constructor. This level may be changed during runtime, calling:
 * <code>
 * $GLOBALS['LOGGER']->setLogLevel($logLevel);
 * </code>
 *
 * If no parameter is passed within the constructor, the level will be fetched from the System Configuration:
 * <code>
 * $GLOBALS['_BIGACE']['log']['level']
 * </code>
 *
 * The possible LogLevel are:
 * - E_ERROR
 * - E_WARNING
 * - E_PARSE
 * - E_NOTICE
 * - E_CORE_ERROR
 * - E_CORE_WARNING
 * - E_COMPILE_ERROR
 * - E_COMPILE_WARNING
 * - E_USER_ERROR
 * - E_USER_WARNING
 * - E_USER_NOTICE
 * - E_STRICT (PHP5 - 2048) 
 * - E_DEBUG (BIGACE specific 1024)
 * - E_SQL  (BIGACE specific 1025)
 *  
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage logger
 */
class Logger
{	
	/**
	 * definition of all log level that create a full variable dump
	 * @access private
	 */
    var $user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
    
    var $logLevel = E_ALL;
    
	/**
	 * definition of all possible Log Level
	 * @access private
	 */
    private $ERROR_LEVEL = array (
                        E_ERROR             => "PHP Error",
                        E_WARNING           => "PHP Warning",
                        E_PARSE             => "Parsing Error",
                        E_NOTICE            => "Script",
                        E_CORE_ERROR        => "Core Error",
                        E_CORE_WARNING      => "Core Warning",
                        E_COMPILE_ERROR     => "Compile Error",
                        E_COMPILE_WARNING   => "Compile Warning",
                        E_USER_ERROR        => "Error",
                        E_USER_WARNING      => "Warning",
                        E_USER_NOTICE       => "Info",
                        E_DEBUG             => "Debug",
                        E_SQL               => "SQL",
                        E_STRICT            => "Runtime Notice"
	);
                 
    /**
     * Create a new Logger instance with the given Log Level.
     *
     * @param int the LogLevel
     */
    function Logger($logLevel = null)
    {
        if ($logLevel == null && isset($GLOBALS['_BIGACE']['log']['level'])) {
            $logLevel = $GLOBALS['_BIGACE']['log']['level'];
        }
        $this->setLogLevel($logLevel);
    }
    
    /**
     * Returns an array with all error level
     */
    function getErrorLevel() {
        return $this->ERROR_LEVEL;
    }
        
    /**
     * Sets the Level, that defines which Messages will be dumped.
     * @see config entry $GLOBALS['_BIGACE']['log']['level']
     * @final
     */
    function setLogLevel($level) {
        $this->logLevel = $level;
    }
    
	/**
	 * Returns the current LogLevel.
	 * @return int the LogLevel
	 */
    function getLogLevel() {
        return $this->logLevel;
    }
    
    /**
     * Returns the Description for the given Mode.
     * @access protected
     */
    function getDescriptionForMode($mode) {
        if ( isset($this->ERROR_LEVEL[$mode]) ) {
            return $this->ERROR_LEVEL[$mode];
        }
        return $mode;
    }
    
    /**
     * Logs an audit message.
     * @param $msg the audit message to log 
     */
    function logAudit($msg) {
    	$this->logInfo($msg);
    }
    
    /**
     * Messages of this Type will always be logged!
     * 
     * @param String the Error Message
     */
    function logError($msg, $stacktrace = false) {
        $this->log(E_USER_ERROR, $msg, $stacktrace);
    }

    /**
     * Messages of this Type are used for information messages.
     * 
     * @param String the Info Message
     */
    function logInfo($msg) {
        $this->log(E_USER_NOTICE, $msg);
    }

    /**
     * Messages of this Type are most often used for development or error search!
     * 
     * @param String the Debug Message
     */
    function logDebug($msg) {
        $this->log(E_DEBUG, $msg);
    }
    
    /**
     * Only SQL querys should be logged by this function, they have the lowest priority.
     * ONLY activate this level for Development or Error Search, NEVER on productive Systems! 
     * 
     * @param String the SQL Command
     */
    function logSQL($msg, $sql = '') {
        $this->log(E_SQL, ($msg .= ($sql == '') ? '' : ' ['.$sql.']') );
    }

    /**
     * Log a message for a special mode, use this if you wanna use your own level/mode!
     * 
     * @param int the Log Level
     * @param String the Log Message
     */
    function log($mode, $msg, $stacktrace = false) {
        if ($this->isModeEnabled($mode))
            echo '['.$this->getDescriptionForMode($mode).'] ' . $msg;
    }
    
    /**
     * Returns if Debugging is enabled.
     * 
     * Wrap Debug Calls within an if block for performance issues:
     * 
     * <code>
     * if ($LOGGER->isDebugEnabled())
     * {
     * 	  $LOGGER->logDebug('foo');
     * 	  $LOGGER->logDebug('bar');
     * }
     * </code>
     * 
     * @return boolean true if Debug is enabled, otherwise false 
     */
    function isDebugEnabled() {
        return $this->isModeEnabled(E_DEBUG);
    }
    
    /**
     * Returns if the given Mode is enabled.
     * 
     * Wrap a list of Logger Calls for performance issues inside an if block, 
     * if you are sure that the given Level might be deactivated!
     * 
     * <code>
     * if ($LOGGER->isModeEnabled(E_SQL))
     * {
     * 	  $LOGGER->log(E_SQL, 'SELECT * FROM foo');
     * 	  $LOGGER->log(E_SQL, 'SELECT * FROM bar');
     * }
     * </code>
     * 
     * @param int the Mode to check
     * @return boolean true if the Mode is enabled, otherwise false 
     */
    function isModeEnabled($mode) 
    {
    	if($mode <= E_USER_ERROR) return true; // always log errors
    	return ($mode & $this->logLevel); // <= to show all with lower level
    }
    
    /**
     * Logs a full LogEntry (which must be an object of the type classes.logger.LogEntry).
     * @param $entry
     * @return unknown_type
     */
    function logEntry($entry) {
    	$this->log($entry->getLevel(), $entry->toString());
    }

    /**
     * Callback function for the PHP logging mechanism.
     */
    function logScriptError($errno, $errmsg, $filename, $linenum, $vars)
    {
       if ($this->isModeEnabled($errno))
       {
            $err = "Script Error:\n";
            $err .= " Type: " . $this->getDescriptionForMode($errno) . "\n";
            $err .= " Msg:  " . $errmsg . "\n";
            $err .= " File: " . $filename . "\n";
            $err .= " Line: " . $linenum;

            if (in_array($errno, $this->user_errors) && $vars != null && count($vars) > 0) {
               $err .= "\t<vartrace>" . serialize($vars) . "</vartrace>\n";
            }
            $err .= "\n";

            foreach(debug_backtrace() AS $backtraceEntry)
            {
                $err .= $this->formatBacktrace($backtraceEntry);
            }

            // save to the error log, and e-mail me if there is a critical user error
            $this->log($errno, $err);
        }
    }

    /**
     * Formats one Entry of a Backtrace Entry. 
     * This method will called in a forach loop, for all entry from
     * debug_backtrace().
     * @return String the formatted backtrace entry
     */
    function formatBacktrace($backtrace)
    {
        $err = '   ';
        foreach($backtrace AS $key => $value)
        {
            switch ($key)
            {
                case 'file':
                    $err .= ' File "' . $value . '"';
                    break;
                case 'class':
                    $err .= ' Class "' . $value . '"';
                    break;
                case 'type':
                    $err .= ' Type "' . $value . '"';
                    break;
                case 'line':
                    $err .= ' at "' . $value . '"';
                    break;
                case 'function':
                    $err .= ' by "' . $value . '"';
                    break;
                case 'args':
                    $err .= ' with "' . (is_array($value) ? print_r($value, true) : $value). '"';
                    break;
                case 'object':
                    break;
                default:
                    $err .= $key . ' => '.$value;
                    break;
            }
        }
        $err .= "\n";
        return $err;
    }

    /**
     * Clean up all resources that might be used by this Logger!
     */
    function finalize() {
    }

}

?>