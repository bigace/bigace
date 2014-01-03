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
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.installation
 */

// check if we are truelly include within the Installation script
if(!defined('_BIGACE_INSTALL_PARENT_')) {
    die('Not runnable alone, go to '.dirname(__FILE__).'/index.php');
}

/**
 * This Logger saves all messages within an internal array. 
 * You may fetch these messages at the end of the call and use them for whatever you want (output in html, save to file...).
 * They are lost within the end of the call.
 */
class Logger 
{
    // change this if you want to debug
    var $debugEnabled   = false;
    var $LOGMSG         = array();
    var $countMsg       = array();
    var $ERROR_LEVEL    = array (
                                //E_STRICT            => "Runtime Notice",
                                E_ERROR             => "PHP Error",
                                E_WARNING           => "PHP Warning",
                                E_PARSE             => "Parsing Error",
                                E_NOTICE            => "Script Notice",
                                E_CORE_ERROR        => "Core Error",
                                E_CORE_WARNING      => "Core Warning",
                                E_COMPILE_ERROR     => "Compile Error",
                                E_COMPILE_WARNING   => "Compile Warning",
                                E_USER_ERROR        => "ERR",
                                E_USER_WARNING      => "WARN",
                                E_USER_NOTICE       => "INF",
                                E_DEBUG             => "DEB",
                                E_SQL               => "SQL"
                         );
 
    function Logger() 
    {
        $this->countMsg[E_DEBUG]        = 0;
        $this->countMsg[E_USER_NOTICE]  = 0;
        $this->countMsg[E_USER_ERROR]   = 0;

        $this->LOGMSG[E_DEBUG]          = array();
        $this->LOGMSG[E_USER_NOTICE]    = array();
        $this->LOGMSG[E_USER_ERROR]     = array();
    }
    
    function getDescriptionForMode($mode)
    {
        if ( isset($this->ERROR_LEVEL[$mode]) ) {
            return $this->ERROR_LEVEL[$mode];
        }
        return $mode;
    }
    
    function log($mode, $msg)
    {
        $this->countMsg[$mode]++;
        $this->LOGMSG[$mode][$this->countMsg[$mode]] = $msg;
    }

    /**
    * Messages of this Type will always be logged!
    */
    function logError($msg) {
        $this->log(E_USER_ERROR, $msg);
    }

    /**
    * Messages of this Type are mostly used for development or error search!
    */
    function logDebug($msg) {
        $this->log(E_DEBUG, $msg);
    }
    
    /**
    * Messages of this Type are used for information messages.
    * Not used for deep level information but more for real important calls!
    */
    function logInfo($msg) {
        $this->log(E_USER_NOTICE, $msg);
    }
    
    function dumpMessages ($mode, $pre = '<!-- ', $past = ' -->', $showDesc = true)
    {
         $temp = $this->LOGMSG[$mode];
         $desc = '';
         if ($showDesc) {
            $desc = '['.$this->getDescriptionForMode($mode).'] ';
         }
       	 for ($i=0; $i < count($temp); $i++) {
       	    echo $pre . $desc . $temp[$i+1] . $past . "\n";
       	 }
    }

    function isDebugEnabled() {
        return $this->debugEnabled;
    }    
    
    function countLog($mode)
    {
        return $this->countMsg[$mode];
    }
    
    function finalize() 
    {
        // Show Debug if enabled
        if ($this->isDebugEnabled()) {
            echo "\n\n";
    	    $this->dumpMessages($this->ERROR_LEVEL[E_DEBUG]);
            echo "\n";
        }
        // Show all Error Messages
     	$this->dumpMessages($this->ERROR_LEVEL[E_ERROR]);
    }

}

// ------------------------------------------------------------
// ----------------- [START] DB CLASSES -----------------------

/**
 * MySQL DB Connection
 */
class dbConnection 
{
	/* The DB connection */
	var $connect = null;
	var $prefix = '';

    /**
    * Connects to the Database, needs three values:
    *
    * Host:     $data['host']
    * User:     $data['user']
    * Password: $data['pw']
    */
	function dbConnection ($data) 
	{
		$this->prefix = $data['prefix'];
		$this->connect = @mysql_connect($data['host'],$data['user'],$data['pass']);
	}
	
	function isConnected()
	{
	    return is_resource($this->connect);
	}
	
    /**
    * Connects to the Database
    */
	function doConnect($db) 
	{
		return @mysql_select_db($db,$this->connect);
	}
	
	function createDB($name) 
	{
	    return $this->sql("CREATE DATABASE ".$name);
	}
	
	function getError() 
	{
        return mysql_error();
	}

	function sql ($query) 
	{
		$query = $this->_parseStatement($query);
		return new dbResult(@mysql_query ($query, $this->connect));
	}
    
    function _parseStatement($query) {
    	return parse_sql($this->prefix, $query);
    }

	function close() 
	{
		$ret = @mysql_close($this->connect);
		unset ($this->connect);
		unset ($this->result);
		return $ret;
	}



}


class dbResult {

    var $result = array();

    /**
    * Initializes the Object with the given DB Resource
    *
    * @param    Object  a DB Result Array
    */
    function dbResult ($sql_result) 
    {
        $this->result = $sql_result;
    }

    /**
    * Looks up if the result for this Select contains an DB Pointer. ONLY FOR SELECTS!
    *
    * @param    boolean  if object is usable
    */
    function numrows()
    {
        return mysql_num_rows($this->result);
    }

    /**
    * Returns the Number of Results
    *
    * @return   int the Number of affected Rows
    */
    function countResult() 
    {
        return $this->count();
    }

    /**
    * Returns the Number of Results
    *
    * @return   int the Number of affected Rows
    */
    function count() 
    {
        return mysql_num_rows ($this->result);
    }

    /**
    * Gets the Next Array in the ResultSet
    *
    * @return   Array   the next Result Array
    */
    function getNextResult() 
    {
        return $this->next();
    }
    
    function next() 
    {
        return mysql_fetch_array($this->result);
    }
    
    function isError() {
        return ($this->result == FALSE);
    }

}


?>