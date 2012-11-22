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
 * @package bigace.api
 * @subpackage sql
 */

/**
 * Interface for Database Connections.
 * The Implementation has to overwrite all mentioned methods in a proper way.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.api
 * @subpackage sql
 */
class DatabaseConnection
{
    
    /**
     * Returns whether we are connected to the Database or not.
     * @return boolean the connection state
     */
    function isConnected() {
        return false;
    }

    /**
     * Returns the Result of this SQL Query.
     * @return Result the result of this Query
     */
	function sql($statement) {
        return false;
	}

	/**
	* Inserts one or more rows and returns the result.
    * @return mixed the result of this insert
	*/
	function insert($statement) {
        return false;
	}
	
	/**
	 * Escapes a value to be used in any SQL Statement.
	 * @param mixed value the value to be escaped
	 * @return mixed the escaped value
	 */
	function escape($value) {
		return addslashes($value);
	}

	/**
	 * Closes the currently used Connection.
	 * @return mixed the result of this close attempt
	 */
	function close() {
		return false;
	}

	/**
	 * Free the given SQL resource.
     * @return mixed the result
	 */
	function freeResult($result) {
        return false;
	}

    /**
     * Return a DBError or null.
     * @return DBError or null
     */
    function getError() {
        return null;
    }
}
