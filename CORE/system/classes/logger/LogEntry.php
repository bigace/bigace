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
 * Class used for full backend logging.
 * Create an instance and pass it to Logger->
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage exception
 */
class LogEntry
{
	private $level;
	private $message;
	private $namespace = "";

	private $filename= "";
	private $linenum = "";

    /**
     * Creates a new LogEntry.
     *
     * @param int the Error Code
     * @param String the Error Message
     * @access public
     */
    function LogEntry($level,$message,$namespace="")
    {
    	$this->level = $level;
    	$this->message = $message;
    	$this->namespace = $namespace;
    }

    /**
     * Returns the entries level.
     * @return int the log level
     */
    function getLevel() {
		return $this->level;
	}

	/**
	 * Returns the Error Message.
	 * @return String the Eroor Message
	 */
	function getMessage() {
		return $this->message;
	}

    /**
     * Return the namespace or an empty string, if no namesapce is configured.
     * @return string namespace or empty string
     */
    function getNamespace() {
    	return $this->namespace;
    }


	function toString() {
		return $this->getMessage();
	}

	function setNamespace($namespace) {
		$this->namespace = $namespace;
	}

	function setFilename($filename) {
		$this->filename = $filename;
	}

	function getFilename() {
		return $this->filename;
	}

	function getLinenum() {
		return $this->linenum;
	}

	function setLinenum($linenum) {
		$this->linenum = $linenum;
	}

	function setLevel($level) {
	    $this->level = $level;
	}
}