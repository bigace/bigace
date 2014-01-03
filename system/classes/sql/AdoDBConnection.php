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

import('classes.sql.AdoDBResult');
import('api.sql.DatabaseConnection');
include_once(_BIGACE_DIR_ADDON.'adodb/adodb.inc.php');

/**
 * Class used for connecting to a Database by using the AdoDB Framework.
 *
 * This is the ultimate way of using BIGACE with none MySQL Databases!
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage sql
 */
class AdoDBConnection extends DatabaseConnection
{
    /**
     * @access private
     */
    var $ressource = null;

    /**
     * Connects to the DB by using the configured Database values.
     */
    function AdoDBConnection() {
        $this->db = ADONewConnection( $GLOBALS['_BIGACE']['db']['type'] );
        @$this->db->Connect( $GLOBALS['_BIGACE']['db']['host'], $GLOBALS['_BIGACE']['db']['user'], $GLOBALS['_BIGACE']['db']['pass'], $GLOBALS['_BIGACE']['db']['name'] );
        if(!$this->db->IsConnected())
            $this->onError();
    }
    
    /**
     * Handle some errors that occured during runtime.
     * @access private
     */
    function onError($msg = '') 
    {
        switch($this->db->ErrorNo())
        {
            case 0:
                // everything is fine!
                break;
            case 1049:
                $GLOBALS['LOGGER']->logError('Database does not exist ('.$this->db->ErrorNo().': '.$this->db->ErrorMsg().'). ' . $msg);
                break;
            case 1146:
                $GLOBALS['LOGGER']->logError('Table does not exist ('.$this->db->ErrorNo().': '.$this->db->ErrorMsg().'). ' . $msg);
                break;
            case 2000: // Unknown MySQL error
            case 2001: // Can't create UNIX socket
            case 2002: // Can't connect to local MySQL server through socket
            case 2003: // Can't connect to MySQL server
            case 2005: // Unknown MySQL server host
                loadClass('exception', 'ExceptionHandler');
                loadClass('exception', 'CoreException');
                ExceptionHandler::processSystemException( new CoreException('db', 'Could not connect to Database! ' . $msg) );
                exit;
                break;
            default:
                $GLOBALS['LOGGER']->logError("SQL/DB problem (".$this->db->ErrorNo().": ".$this->db->ErrorMsg()."). " . $msg);
                break;
        }
    }

    /**
     * Returns whether we are connected to a DB or not.
     * @return boolean  true on successful Connection
     */
    function isConnected() {
        return $this->db->IsConnected();
    }

    /**
    * Executes any SQL Statement (mysql_query).
    */
    function sql($query) 
    {
        $recordSet = &$this->db->Execute($query);
        if($recordSet === FALSE) {
            $this->onError("Failed Statement: " . $query);
        } 
        return new AdoDBResult($recordSet);
    }

    /**
    * Inserts a new DB entry and if exists returns the auto increment value.
    */
    function insert($query) 
    {
        if ($this->db->Execute($query) === false) {
            $this->onError("Failed INSERT Statement: " . $query);
            return FALSE;
        } 
        else {
            return $this->db->Insert_ID();
        }
    }

    /**
     * Closes the Database Connection. The Results must be closed manually!
     */
    function close()  {
        $this->db->Close();
    }


    /**
     * Frees the given Result.
     */
    function freeResult($result) {
        if(is_object($result))
            $this->db->Close();
    }
    
    /**
     * Returns the original ADONewConnection Object to be used by other
     * AdoDB Components.
     * @return ADONewConnection the ADONewConnection object for further usage
     */
    function getADONewConnection() {
    	return $this->db;
    }

}

?>
