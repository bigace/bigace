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

import('classes.logger.Logger');

/**
 * This Logger saves its Message to the Database.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage logger
 */
class DBLogger extends Logger
{
    private $mynamespace = '';

    /**
     * Pass a Namespace for your Log Instance if desired.
     * Otherwise you log into the default namespace.
     *
     * @param String the Namespace
     */
    function DBLogger($namespace = '')
    {
        $this->Logger(null);
        $this->mynamespace = $namespace;
    }

    /**
     * Log a message for a special mode.
     *
     * @param int the Log Level
     * @param String the Log Message
     */
    function log($mode, $msg, $stacktrace = false) {
    	if ($this->isModeEnabled($mode)) {
    		if($stacktrace) {
    			$err = '';
	            foreach(debug_backtrace() AS $backtraceEntry) {
	                $err .= $this->formatBacktrace($backtraceEntry);
	            }
        		$this->insertFullLogEntry($mode, $msg, '', '', $err);
    		}
        	$this->insertFullLogEntry($mode, $msg);
        }
    }

    /**
     * Logs a full LogEntry (which must be an object of the type classes.logger.LogEntry).
     * @param $entry
     * @return unknown_type
     */
    function logEntry($entry) {
    	$this->insertFullLogEntry($entry->getLevel(), $entry->toString(), $entry->getFilename(), $entry->getLinenum(), '', $entry->getNamespace());
    }

    function logAudit($msg) {
    	$this->insertFullLogEntry(E_USER_NOTICE, $msg, '', '', '', LOGGER_NAMESPACE_AUDIT);
    }

    /**
     * @access private
     */
    function insertFullLogEntry($level, $message, $filename = '', $linenum = '', $stacktrace = '', $namespace = '')
    {
        if ($this->isModeEnabled($level))
        {
        	if(!isset($GLOBALS['_BIGACE']['SESSION'])) {
/*                import('classes.logger.FileLogger');
                $log = new FileLogger('core');
                $log->setLogLevel($this->logLevel);
                $log->logScriptError($level, $message . ' | ' . $stacktrace, $filename, $linenum, array()); */
        		echo '<!-- ' . $message . " -->\n";
        	}
        	else {
	            $values = array(
                        'USERID'     => $GLOBALS['_BIGACE']['SESSION']->getUserID(),
                        'TIMESTAMP'  => time(),
                        'NAMESPACE'  => (($namespace == '') ? $this->mynamespace : $namespace),
                        'LEVEL'      => $level,
                        'MESSAGE'    => $message,
                        'FILE'       => str_replace(_BIGACE_DIR_ROOT, '', $filename),
                        'LINE'       => ($linenum == '' ? null : $linenum),
                        'STACKTRACE' => $stacktrace,
                );
	            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('logging_insert');
	            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
	            $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        	}
        }
    }

    function logScriptError($errno, $errmsg, $filename, $linenum, $vars)
    {
       if ($this->isModeEnabled($errno))
       {
            $err = "Script Error:\n";

            foreach(debug_backtrace() AS $backtraceEntry)
            {
                $err .= $this->formatBacktrace($backtraceEntry);
            }

            if (in_array($errno, $this->user_errors) && $vars != null && count($vars) > 0) {
               $err .= "\n";
               $err .= "\t<vartrace>" . serialize($vars) . "</vartrace>\n";
            }

            $this->insertFullLogEntry($errno, $errmsg, $filename, $linenum, $err);
        }
    }


}

?>