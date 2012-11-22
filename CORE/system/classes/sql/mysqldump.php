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
 * @package bigace.classes
 * @subpackage sql
 */

/**
 * Class used for creating MySQL Database dumps.
 * Was introduced for easier transport of single Communities.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage sql
 */
class mysqldump
{
	/**
	 * @access private
	 */
	var $link;
	/**
	 * @access private
	 */
	var $host;
	/**
	 * @access private
	 */
	var $filename;
    
	/**
	 * @access private
	 */
    var $selectTables;    
	/**
	 * @access private
	 */
    var $msg;

	/**
	 * @access private
	 */
    var $showDropTable;
	/**
	 * @access private
	 */
    var $showCreateTable;
	/**
	 * @access private
	 */
    var $showComment;
    
	/**
	 * @access private
	 */
    var $replacer;
	/**
	 * @access private
	 */
    var $useReplacer;
	/**
	 * @access private
	 */
    var $tablePreset;
	/**
	 * @access private
	 */
    var $commentStart = "#";
    
	function mysqldump($host, $user, $password, $create = true, $drop = true, $comment = true)
	{	
	    $this->setShowCreateTable($create);
	    $this->setShowDropTable($drop);
	    $this->setShowComments($comment);
	    
  		$this->link = @mysql_connect($host, $user, $password);
  		if(!$this->link ){return false;}
  		$this->host=$host;

  		$this->replacer = array();
  		$this->useReplacer = false;
  		$this->tablePreset = "";
	}
	

	function setUseReplacer($useReplacer) {
	   if (is_bool($useReplacer)) {
	     $this->useReplacer = $useReplacer;
     }
  }

	function setReplacer($replacer) {
	   if (is_array($replacer)) {
	     $this->replacer = $replacer;
     }
  }
	
	function addReplacer($replacer, $value) {
	   $this->replacer[$replacer] = $value;
    }

	function setTablePreString($preString) {
	    $this->tablePreset = $preString;
	}

	function setShowComments($s) {
	    $this->showComment = $s;
	}

	function setShowDropTable($s) {
	    $this->showDropTable = $s;
	}

	function setShowCreateTable($s) {
	    $this->showCreateTable = $s;
	}

	/**
	 * Adds a message to the internal store
	 * @access private
	 */
    function message($t)
    {
        $this->msg .= $t;
    }
    
	/**
	 * Returns only usefull data if <code>backup()</code> was called before!
	 * @return String the Full Dump.
	 */
    function getDump()
    {
        return $this->msg;
    }
	
	/**
	 * @access private
	 */
	function getDumpComment($database)
	{
	    $m = "\n";
		$server_info=mysql_get_server_info($this->link);
		$m .= $this->commentStart . "-----------------------------------------------\n";
		$m .= $this->commentStart . " BIGACE DB Dump for Consumer: ".$_SERVER['HTTP_HOST']." (CID "._CID_.")\n";
		$m .= $this->commentStart . "\n";
		$m .= $this->commentStart . " Host :           $this->host\n";
		$m .= $this->commentStart . " Database :       $database\n";
		$m .= $this->commentStart . " Server version : $server_info \n";
		$m .= $this->commentStart . "-----------------------------------------------\n";
		$m .= "\n";
		return $m;
	}

	/**
	 * @access private
	 */
	function getTableComment($tablename)
	{
	    $m = "\n\n";
        $m .= $this->commentStart . "\n";
        $m .= $this->commentStart . " Creating information for table '$tablename'\n";
        $m .= $this->commentStart . "\n";
        $m .= "\n";
	    return $m;
	}

	/**
	 * @access private
	 */
	function getTableCreateComment($tablename)
	{
	    return '';
	}
	
	/**
	 * @access private
	 */
	function getTableDropComment($tablename)
	{
	    return '';
	}

	/**
	 * @access private
	 */
	function getTableInsertComment($tablename)
	{
	    $m = "\n";
        $m .= $this->commentStart . "\n";
        $m .= $this->commentStart . " Dumping data for table '$tablename' \n";
        $m .= $this->commentStart . "\n";
	    return $m;
	}

	/**
	 * @access private
	 */
	function getTableEmptyComment($tablename)
	{
	    $m = "\n";
        $m .= $this->commentStart . "\n";
        $m .= $this->commentStart . " Table '$tablename' is empty...\n";
        $m .= $this->commentStart . "\n";
	    $m .= "\n";
	    return $m;
	}

	/**
	 * @return boolean whether the Database select worked or not
	 */
	function backup($table, $excludeTables = array())
	{
		if(!mysql_select_db($table))
		{
		    return false;
        }
		else
		{ 
		    if($this->showComment) {
		        $this->message($this->getDumpComment($table));
		    }

			$ltable = mysql_list_tables($table,$this->link); 
			$nb_row = mysql_num_rows($ltable);
					
			$i = 0;
			while ($i < $nb_row) 
			{ 	
                $tablename = mysql_tablename($ltable, $i);
                
                $hasPreString = TRUE;
                if(strpos($tablename, $this->tablePreset) === FALSE) {
                	$hasPreString = FALSE;
                }
                
                // make sure to fetch omly tables with the given PreSet-String liek 'cms_'
                if ($this->tablePreset == "" || $hasPreString)
                {
	                $compTablename = $tablename;
	                
	                if ($this->tablePreset != "") {
	                	$compTablename = substr($tablename, strlen($this->tablePreset), strlen($tablename));
	                }
                
	                if( (count($this->selectTables)==0 || !is_array($this->selectTables) || in_array($compTablename,$this->selectTables)) && 
	                    ( count($excludeTables)==0 || !is_array($excludeTables) || !in_array($compTablename,$excludeTables) ) ) 
	                {
	                    // Table comment
	                    if($this->showComment) {
	        		        $this->message($this->getTableComment($tablename));
	                    }
	
	                    if($this->showDropTable) {
	                        if($this->showComment) {
	                            $this->message($this->getTableDropComment($tablename));
	                        }
	                        $this->message("DROP TABLE IF EXISTS `$tablename`;\n");
	                    }
	                    
	                    // Show create Table Statement
	                    if($this->showCreateTable) 
	                    {
	                        $query = "SHOW CREATE TABLE $tablename";
	                        $tbcreate = mysql_query($query);
	                        $row = mysql_fetch_array($tbcreate);
	                        
	                        $row[1] = urlencode($row[1]);
	                        //$row[1] = str_replace("%0A","\n",$row[1]);
	                        $row[1] = str_replace("%0D"," ",$row[1]);
	                        $row[1] = urldecode($row[1]);
	                        
	                        $create = $row[1].";";
	
	                        if($this->showComment) {
	                            $this->message($this->getTableCreateComment($tablename));
	                        }
	                        $this->message($create . "\n");
	                    }
	
	                    if($this->showComment) {
	                        $this->message($this->getTableInsertComment($tablename));
	                    }
	
	                    $query = "SELECT * FROM $tablename where cid='"._CID_."'";
                        $this->currentStatement = $query;
	                    $datacreate = mysql_query($query);
	                    if (mysql_num_rows($datacreate) > 0)
	                    {
	                        //$this->message("LOCK TABLES $tablename WRITE; \n");
	                       $column_names = "";	
	                        
	                        while($assoc = mysql_fetch_assoc($datacreate))
	                        {   
	                            if ($column_names == "") {
	                              $column_names = implode(",",array_keys($assoc));	
	                            }
                                //uncomment following line if your server is in safe mode	
	                            //set_time_limit(30);            

                               // Fix for Bug that NULL was displayed even if field was of type 'not_null'
                               // prepare each value, read below...
                               $assoc2 = array(); // holds the refactored values!
                               foreach($assoc AS $key => $value) 
                               {
                               		// $columnType = mysql_field_type($datacreate,$key);
                               		// probably we will find out if the type is int... and remove the ''
                               		// currently there are problems with numerical enum('1', '0')
                               		// this will be matched by is_numeric and not placed into ''. 
                               		// But when importing, it will always be interpretated as 0!
                               
                            		if(is_numeric($value)){ 
                                        $assoc2[$key] = "'" . $value . "'";
                                    }
                            		else if($value == '' || !$value) {
                                        // empty value must be checked, whether they are NULL or simply an empty String
                                        $flags = mysql_field_flags($datacreate,$key);
                                        if(stristr($flags, 'not_null') !== false) {
                                            //echo $key . '=' . $value . '('. mysql_field_flags($datacreate,$key) . ')<br />';
	                                        $assoc2[$key] = "''";
                                        } else {
                                            $assoc2[$key] = "NULL";
                                        }
                                    }
                                    else {
                            		    $assoc2[$key] = "'" . mysql_escape_string(htmlspecialchars($value)) . "'";
                                    }
                               }

                               // if some columns should be replaced, we map the replacer into the value columns
                               // for example each cid column colud be replaced by {CID}	                               
                               if($this->useReplacer) {
                               		foreach ($this->replacer AS $key => $value) {
                                    	$assoc2[$key] = $value;
                                 	}
                               }
                               
                               $data = implode(",",$assoc2);
                               $data = "INSERT INTO `$tablename` ($column_names) values ($data);\n";
                               
                               $this->message("$data");
	                        }
	                        //$this->message("UNLOCK TABLES; \n");
	                    }
	                    else
	                    {
	                        // Table is empty
	                        if($this->showComment) {
	                            $this->message($this->getTableEmptyComment($tablename));
	                        }
	                    }
            	}
          	  }
	  		$i++;
		  }   
		}
		return TRUE;
	}

}
?>