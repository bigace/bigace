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
 * @subpackage updates
 */

/**
 * This is the super class for all updates!
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage updates
 */
class AbstractUpdate
{
	private $errors = array();
	private $_db_conn;
	private $modul;
	private $manager;
	
	function AbstractUpdate() {
	}
	
	/**
	 * Adds an error to the internal data structure.
	 * 
	 * @param String the message
	 */
    function addError($message) {
        array_push($this->errors, new UpdateResult(FALSE, $message));
    }
    
    function getErrors() {
    	return $this->errors;
    }
	
	function setUpdateManager($updateManager) {
		$this->manager = $updateManager;
	}

	function getUpdateManager() {
		return $this->manager;
	}

	function setUpdateModul($updateModul) {
		$this->modul = $updateModul;
	}

	function getUpdateModul() {
		return $this->modul;
	}
	
	function setDBConnection($db) {
		$this->_db_conn = $db;
	}

	function getDBConnection() {
		return $this->_db_conn;
	}
	
}

?>