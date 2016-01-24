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

/**
 * Class used for handling any SQL Statement, which is meant as DB abstraction layer.
 * It caches loaded - unprepared - Statments for performance issues.
 * 
 * NOTE: 
 * There are two super-global Replacer that are automatically appended.
 * 
 * - 'CID' will be taken from the environment if you do NOT pass it
 * - 'DB_PREFIX' will always be added
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage sql
 */
class SQLHelper 
{
	/**
	 * @access private
	 */
    var $DB_CONNECTION;
    private $CACHED_STATEMENTS = array();
    private $counter = 0;
    
    /**
     * Initializes the SQLHelper with the required Database Connection.
     * @param DatabaseConnection dbConnection the Database Connection to be used
     */
    function SQLHelper($dbConnection) {
        $this->DB_CONNECTION = $dbConnection;
    }
    
    /**
     * Prepares the given Statement by replacing all found {REPLACER}
     * with the values of the submitted values array.
     * 
     * For example you have the SQL:
     * Select * from {TABLE} where id='{ID}'
     * 
     * You would pass an array like this:
     * array('TABLE' => 'item_1', 'ID' => '-1')
     */
    function prepareStatement($statement, $values = array(), $escape = false) 
    {
    	// always add the DB_PREFIX
    	$values = array_merge($values, array('DB_PREFIX' => $GLOBALS['_BIGACE']['db']['prefix']));

    	// and the CID only if not set
        if ( !isset($values['CID']) ) {
            if ( defined('_CID_') ) {
                $values = array_merge($values, array('CID' => _CID_));
            }
        }
        
        foreach ($values AS $key => $val) {
        	if($escape && $key != 'DB_PREFIX')	
        		$val = $this->quoteAndEscape($val);
            $statement = str_replace("{".$key."}",$val,$statement);
        }
        return $statement;
    }
	
	/**
	 * This function cares about quoting and escaping of all values, that will
	 * be used for SQL Queries.
	 * @param mixed value the value to be escaped and quoted
	 * @return String your prepared value to be used for any SQL Query
	 */
	function quoteAndEscape($value) 
	{
		if(is_null($value))
			return "null";
		if(is_int($value))
			return $value;
		return "'".$this->escape($value). "'";
	}
	
	/**
	 * Escapes a character to be used in SQL statements.
	 * @param $value the values to be escaped.
	 * @return string the escaped value
	 */
	function escape($value)
	{
		if(get_magic_quotes_gpc())
			$value = stripslashes($value);
		return addslashes($value);
	}
	
    /**
     * Loads and returns the Statement. If this statement was loaded before, 
     * it will be served directly from the Cache.
     */
    function loadStatement($name)
    {
        $statement = '';
        if (isset($this->CACHED_STATEMENTS[$name])){
            $statement = $this->CACHED_STATEMENTS[$name];
        } else {
            $statement = file_get_contents(_BIGACE_DIR_ROOT . '/system/sql/' . $name . '.sql');
            $this->CACHED_STATEMENTS[$name] = $statement;
        }
        
        return $statement;
    }
    
    /**
     * Simple wrapper for <code>prepareStatement(loadStatement($name), $values, $escape)</code>.
     */
    function loadAndPrepareStatement($name, $values, $escape = false) {
        return $this->prepareStatement($this->loadStatement($name), $values, $escape);
    }

    /**
     * Executes a SQL Query from a file prepared with the given values.
     *
     * @param string the filename to load the sql from
     * @param array the replacer values
     * @return mixed the sql result
     */
	function sql($name, $values, $escape = false) 
	{
		return $this->execute( $this->loadAndPrepareStatement($name, $values, $escape) );
	}
	
	function get_var($sql, $name = null) {
		$res = $this->execute($sql);
		if(is_null($res) || $res->isError())
			return false;
		$res = $res->next();
		if (!is_null($name) && isset($res[$name]))
			return $res[$name];
		return $res[0];
	}
    /**
     * Executes the SQL Statement and returns the Result. 
     * Normaly used for Select, Update, Delete ...
     */
    function execute($sql) {
	    $this->counter++;
    	return $this->DB_CONNECTION->sql($sql);
    }
    
    /**
     * Executes the Insert Statement and returns the generated ID.
     * @return int the Database ID
     */
    function insert($sql) {
	    $this->counter++;
    	return $this->DB_CONNECTION->insert($sql);
    }
    
    /**
     * Get the DB Connection.
     * @return DatabaseConnection the DB Connection that is used
     */
    function getConnection() {
    	return $this->DB_CONNECTION;
    }

    /**
     * Fetch all results for the given statement and replacer values.
     * @param statement string - the statement to execute
     * @param values array - with a key-value mapped array of the values to be replaced
     * @param escape boolean - whether the values should be replaced or not
     * @param name string - add a name if you only want to fetch the results of a special column
     * @return array the array with all results
     */
    function fetchAll($statement, $values = array(), $escape = false, $name = null) 
    {
	    $this->counter++;
    	$all = array();
        $sqlString = $this->loadStatement($statement);
	    $sqlString = $this->prepareStatement($sqlString,$values,$escape);
	    $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        for($i=0;$i < $temp->count(); $i++) {
        	$temp2 = $temp->next();
        	if(!is_null($name))
        		$all[] = $temp2[$name];
        	else
        		$all[] = $temp2;
        }
    	return $all;
    }
    
    /**
     * Returns the amount of executed SQL statements for the object instance.
     * @return int the amount of executed statements
     */
    function getCounter() {
    	return $this->counter;
    }
    
    /**
     * Returns the error which occured when executing the last statement. 
     * Depends on the underlying database connection.
     * @return DBError the error object or null
     */
    function getError() {
        return $this->DB_CONNECTION->getError();
    }    
}
