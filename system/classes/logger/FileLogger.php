<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.<br>Copyright (C) Kevin Papst.
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

loadClass('logger', 'Logger');
loadClass('util', 'IOHelper');

/**
 * This Logger saves Message to a file.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage logger
 */
class FileLogger extends Logger
{
	/**
	 * The File handle for logging purpose.
	 * @access private
	 */
	var $_file;

    /**
     * Pass a PREFIX for your file.
     * If you do not pass a Prefix, your Log messsages might be shared 
     * with other Logger instances.
     * 
     * @param int the LogLevel
     * @param String the Prefix for the File.
     */
    function FileLogger($logDefinition = '') 
    {
        $this->Logger(null);
    	$dir = _BIGACE_DIR_ROOT . '/misc/logging/';
        if ($logDefinition != '') {
            if (isset($GLOBALS['_BIGACE']['log']['level_'.$logDefinition])) {
                $this->setLogLevel($GLOBALS['_BIGACE']['log']['level_'.$logDefinition]);
            }
            $logDefinition .= '_';
        }
    	$filename = $logDefinition . 'log_' . date('Y_m_d',time()) . '.txt';
    	$this->initLogger($dir, $filename);
    }
    
    /**
     * @access protected
     */
    function initLogger($dir, $filename)
    {
    	if (!file_exists($dir)) {
    		IOHelper::createDirectory($dir);
    	}
    	$fullfile = $dir . $filename;
    	$oldumask = umask(_BIGACE_DEFAULT_UMASK_FILE);
        $this->_file = fopen($fullfile, "a+");
	    umask($oldumask); 
    }
    
    /**
     * Log a message for a special mode, use this if you wanna use your own level/mode!
     * Overwriten to Log all messages to the desired Log File.
     * 
     * @param int the Log Level
     * @param String the Log Message
     */
    function log($mode, $msg) {
    	if ($this->isModeEnabled($mode)) {
        	$this->_logToFile( $mode, $this->_formatString($mode,$msg) );
        }
    }
    
    /**
     * @access private
     */
    function _formatString($type, $msg) {
    	if (defined('_CID_')) {
    		return date('[Y.m.d - H:i:s]', time()) . ' ['.$this->getDescriptionForMode($type).'] (Community: '._CID_.') ' . $msg . "\n";
    	}
		return date('[Y.m.d - H:i:s]', time()) . ' ['.$this->getDescriptionForMode($type).'] (System) ' . $msg . "\n";
    }

    /**
     * @access private
     */
    function _logToFile($mode, $line) {
    	@fputs($this->_file, $line);
    }
    
    /**
     * Closes the Log File handle.
     * Make sure to call this method before the Script ends!
     */
    function finalize() {
        fclose($this->_file);
    }

}

?>