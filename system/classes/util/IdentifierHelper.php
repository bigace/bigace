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
 * @subpackage util
 */

/**
 * This class provides some helper methods for creating and fetching Identifier.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util
 */
class IdentifierHelper
{
    /**
     * Gets the Maximum URL from a Database Table.
     * @param int the current maximum ID
     */
    static function getMaximumID($table) {
        $values = array( 'TABLE' => $table );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('select_max_id');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $temp = $temp->next();
        return $temp['max'];
    }
    
    /**
     * Creates a fail safe new ID for the given name. 
     * Do not rely on auto_increment values, but on this function.
     *
     * @param string $name the name of the id you want to fetch
     * @return int
     */
    static function createNextID($name) {
        $values = array( 'NAME' => $name );
        /*
        $sqlString = "LOCK TABLE {DB_PREFIX}id_gen WRITE";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

        if(!$temp->isError()) {
        */
	        $sqlString = 'UPDATE {DB_PREFIX}id_gen SET value = value +1 WHERE cid = {CID} AND name = "{NAME}"';
	        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
	        $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	        
	        $sqlString = 'SELECT value FROM {DB_PREFIX}id_gen WHERE cid = {CID} AND name = "{NAME}"';
	        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
	        $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	        $temp = $temp->next();
	      /*  
	        $sqlString = "UNLOCK TABLES";
	        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
	        $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	      */  
	        return $temp['value'];
	        /*
        }
        
    	return false;
    	*/
    }

}
