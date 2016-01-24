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

import('classes.logger.LogEntry');

/**
 * Base class for all Exceptions.
 * DO NOT DIRECTLY USE THIS CLASS, USE SUBCLASSES OF EXCEPTION! 
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage exception
 */
class CMSException extends LogEntry
{

	private $isSmarty = false;
	    
    /**
     * Creates a new CMSException.
     * 
     * @param int the Error Code
     * @param String the Error Message
     * @access public
     */
    function CMSException($code,$message,$namespace="") 
    {
    	$this->LogEntry($code, $message, $namespace);
    }
    
    /**
     * Returns the Error Code.
     * @return int the Error Code 
     */
    function getCode() {
		return $this->getLevel();
	}
	
    /**
     * Returns whether this Error should write an Log Message or not.
     * Might be overwritten by implementing classes!
     * @return boolean whether the Exception is logged or not 
     */
    function logException() {
        return true;
    }

	function toString() {
		return get_class($this) . " | " . $this->getMessage(); 
	}


	/**
	 * Returns whether this Error is represented by a Smarty template.
	 */
	function isSmarty() {
	    return $this->isSmarty;
	}

	/**
	 * Sets whether this Error is represented by a Smarty template.
	 */
	function setIsSmarty($s) {
	    $this->isSmarty = $s;
	}	
}

?>