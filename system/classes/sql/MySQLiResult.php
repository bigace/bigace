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
 * @subpackage sql
 */

import('api.sql.Result');

/**
 * This represents a DB Result for any query made with a MySQLiConnection.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage sql
 */
class MySQLiResult extends Result
{
	/**
	 * @access private
	 */
    var $result = array();

    /**
     * Initializes the Object with the given Database result.
     *
     * @param Object a DB Result Array
     */
    function MySQLiResult($sqlResult) {
        $this->result = $sqlResult;
    }

    /**
     * Returns the Number of Results within the ResultSet.
     * @return int the Number of results
     */
    function count() {
        return mysqli_num_rows($this->result);
    }

    /**
     * Gets the Result from the ResultSet.
     * @return array the next array in the result
     */
    function next() 
    {
        return mysqli_fetch_array($this->result);
    }

    /**
     * Returns whether the Statement was successful or not.
     */
    function isError() {
        return ($this->result === false);
    }
    
    function free() {
    	return mysqli_free_result($this->result);
    }
}
