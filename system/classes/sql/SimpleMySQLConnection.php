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

import('classes.sql.MySQLResult');
import('api.sql.DatabaseConnection');
import('api.sql.DBError');

/**
 * Implementation of the DatabaseConnection to connect to a MySQL Database.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage sql
 */
class SimpleMySQLConnection extends DatabaseConnection
{
    private $connected = FALSE;
    private $ressource = null;

    /**
     * Connects to the DB by using the configured Database values.
     */
    function SimpleMySQLConnection() {
        $this->connect($GLOBALS['_BIGACE']['db']['host'],$GLOBALS['_BIGACE']['db']['name'],$GLOBALS['_BIGACE']['db']['user'],$GLOBALS['_BIGACE']['db']['pass']);
    }
    
    /**
     * Connects to a MySQL DB using the given Connection Values.
     * @param String the DB Host
     * @param String the Database to select
     * @param String the DB User 
     * @param String the Users Password
     * @param boolean whether a new Connection should be established
     */
    function connect($host, $db, $user, $password, $newConnection = false)
    {
        $this->ressource = mysql_connect($host, $user, $password, $newConnection);
        if (mysql_errno() == 0) {
            if(mysql_select_db($db,$this->ressource)) {
                $this->connected = TRUE;
            }
        }

        if (mysql_errno($this->ressource) != 0) {
            $this->onError();
        }
        
        // change encoding - updated systems might not have this flag set!
        if($this->isConnected() && isset($GLOBALS['_BIGACE']['db']['character-set'])) {
			mysql_query("SET CHARACTER SET ".$GLOBALS['_BIGACE']['db']['character-set']);
			mysql_query("SET NAMES ".$GLOBALS['_BIGACE']['db']['character-set']);
        }
    }
    
    /**
     * Handle some errors that occured during runtime.
     * @access private
     */
    function onError($sql = '') 
    {
        switch(mysql_errno($this->ressource))
        {
            case 1049:
                $GLOBALS['LOGGER']->logError('Database does not exist ('.mysql_errno($this->ressource).': '.mysql_error($this->ressource).') for ['.$sql.']');
                break;
            case 1146:
                $GLOBALS['LOGGER']->logError('Table does not exist ('.mysql_errno($this->ressource).': '.mysql_error($this->ressource).') for ['.$sql.']');
                break;
            case 1044: // user has no rights to access
            case 1045: // user is not allowed to access (password)
            case 2000: // Unknown MySQL error
            case 2001: // Can't create UNIX socket
            case 2002: // Can't connect to local MySQL server through socket
            case 2003: // Can't connect to MySQL server
            case 2005: // Unknown MySQL server host
                import('classes.exception.ExceptionHandler');
                import('classes.exception.CoreException');
                ExceptionHandler::processSystemException( new CoreException('db', 'Could not connect to database with ['.$sql.']') );
                exit;
                break;
            case 0:
				// everything is fine?!
                //$GLOBALS['LOGGER']->logError("Unknown error (".mysql_errno($this->ressource).": ".mysql_error().') for ['.$sql.']');
                //break;
            default:
                $GLOBALS['LOGGER']->logError("MySQL error (".mysql_errno($this->ressource).": ".mysql_error($this->ressource).') for ['.$sql.']');
                break;
        }
    }

    /**
     * Returns whether we are connected to a DB or not.
     * @return boolean  true on successful Connection
     */
    function isConnected() {
        return $this->connected;
    }

    /**
    * Executes any SQL Statement (mysql_query).
    */
    function sql($query) 
    {
        $result = mysql_query($query, $this->ressource);
        if ($result === FALSE || mysql_errno($this->ressource) != 0)  {
            $this->onError($query);
        } 
        return new MySQLResult($result);
    }

    /**
    * Inserts a new DB entry and if exists returns the auto increment value.
    */
    function insert($query) 
    {
        $result = mysql_query($query, $this->ressource);
        if ($result === FALSE || mysql_errno($this->ressource) != 0)  {
            $this->onError($query);
            return FALSE;
        }
        return mysql_insert_id($this->ressource);
    }

	/**
	 * Escapes a value to be used in any SQL Statement.
	 * @param mixed value the value to be escaped
	 * @return mixed the escaped value
	 */
	function escape($value) {
		if(!is_int($value))
			return mysql_real_escape_string($value, $this->ressource);
		return $value;
	}

    /**
    * This closes the currently Connection.
    * Might be unnecessary (PHP closes links automatically).
    */
    function close() 
    {
        $ret = mysql_close($this->ressource);
        unset ($this->ressource);
        return $ret;
    }


    /**
     * Frees the given MySQL resources.
     */
    function freeResult($result) 
    {
        if (is_resource($result)) {
            return mysql_free_result($result);
        }
    }
    
    function getError() {
        if(mysql_errno($this->ressource) != 0)  {
            return new MySQLError(mysql_errno($this->ressource), mysql_error($this->ressource));
        }
        
        return null;
    }
}

class MySQLError implements DBError
{
    private $msg;
    private $number;    
    
    function MySQLError($number, $msg) {
        $this->number = $number;
        $this->msg = $msg;
    }
    
    /**
     * Returns the error number
     * @return int the database specific error number
     */
    function getNumber() {
        return $this->number;
    }
    
    /**
     * Gets the error message
     * @return mixed the error message
     */
    function getMessage() {
        return $this->msg;
    }
}
