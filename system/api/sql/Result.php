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
 * This represents a Result for any Database query made with DatabaseConnection.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.api
 * @subpackage sql
 */
class Result 
{

    /**
     * Returns the Number of Results
     * @return int the Number of affected Rows
     */
    function count() {
        return 0;
    }

    /**
     * Gets the next result or FALSE if none is available.
     * @return mixed the next result in this query or FALSE
     */
    function next() {
    	return FALSE;
    }

    /**
     * Returns whether the Statement was successful or not.
     */
    function isError() {
        return false;
    }

    /**
     * Free the memory for this result.
     * @return unknown_type
     */
    function free() {
    	return true;
    }
}

?>