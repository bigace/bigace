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
 * @subpackage parser
 */

define('XML_SQL_DEFAULT_SCHEMA_VERSION', '1.0');
define('XML_SQL_DEFAULT_MODE', 'update');

define('XML_SQL_MODE_UPDATE', 'update');
define('XML_SQL_MODE_INSTALL', 'install');

/**
 * This class provides methods for easy parsing XML Files to SQL Statements.
 *  
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage parser
 */
class XmlToSqlParser
{
    /** 
     * @access private
     */
    var $rootTag = 'content';
    /** 
     * @access private
     */
    var $schemaVersion = '1.0';
    /** 
     * @access private
     */
    var $ignoreVersion = false;
    /** 
     * @access private
     */
    var $tablePrefix = '';
    /** 
     * @access private
     */
    var $obj = null;
    /** 
     * @access private
     */
    var $sqlArray = array();
    /** 
     * @access private
     */
    var $replacer = array();
    /** 
     * @access private
     */
    var $errors = array();
    /** 
     * @access private
     */
    var $mode = XML_SQL_DEFAULT_MODE;
    /** 
     * @access private
     */
    var $adoDbConn = null;
    

    /**
     * Parses a XML File name identified by its canoncial Filename.
     */
    function parseFile($filename) {
        $this->parseStructure( $this->get_file_contents($filename) );
    }

	/**
	 * Returns the content of the desired file.
	 */
	function get_file_contents($file) 
	{
 		if (function_exists('file_get_contents')) 
 			return file_get_contents($file);
	
		$f = fopen($file,'r');
		if (!$f) return '';
		$t = '';
	
		while ($s = fread($f,100000)) $t .= $s;
			fclose($f);
			
		return $t;
	}

	/**
	 * @access private
	 */
	function &_create_parser() {
		// Create the parser
		$xmlParser = xml_parser_create();
		xml_set_object( $xmlParser, $this );
		
		// Initialize the XML callback functions
		xml_set_element_handler( $xmlParser, '_tag_open', '_tag_close' );
		xml_set_character_data_handler( $xmlParser, '_tag_cdata' );
		
		return $xmlParser;
	}

    function getVersionRegExp() {
        return '/<'.$this->rootTag.'.*?( version="([^"]*)")?.*?>/';
    }

    /**
     * Sets if we try to parse the Schema File, even if Version conflict is found.
     * Returns the current setting.
     */
    function setIgnoreVersionConflict($ignore = true) {
        if(is_bool($ignore))
            $this->ignoreVersion = $ignore;
        return $this->ignoreVersion;
    }
      
    /**
     * Sets the Prefix for each Table.
     */
    function setTablePrefix($prefix = '') {
        $this->tablePrefix = $prefix;
    }

    /**
     * Converts the given Tablename into a prefixed one.
     */
    function prefix($tableName) {
        return $this->tablePrefix . $tableName;
    }

    /**
     * Returns the prefixed Table Name.
     */
    function setReplacer($replacer) {
        return $this->replacer = $replacer;
    }

    /**
     * Returns the prefixed Table Name.
     */
    function setAdoDBConnection(&$connection) {
        return $this->adoDbConn = $connection;
    }
    
    /**
     * Parses an XML Structure
     */
    function parseStructure($xml)
    {   
        $version = $this->getSchemaVersion($xml);
		if( $version === FALSE ) {
            if($this->ignoreVersion) {
    			$this->addError('Ignoring missing schema version ... fix your XML!');
            } else {
    			$this->addError('Missing schema version, skip XML parsing ... fix your XML!');
    			return FALSE;
            }
		}
		else if($version != $this->schemaVersion) {
            if($this->ignoreVersion) {
    			$this->addError('Ignoring invalid schema version: '.$version.'. Current version: ' . $this->schemaVersion);
            } else {
			    $this->addError('Skip XML parsing, invalid schema version: '.$version.'. Current version: ' . $this->schemaVersion);
			    return FALSE;
            }
		}
		
  		if($this->adoDbConn == null) {
   			$this->addError('No AdoDB Object supplied, using Insert Statements');
   		}

        $xml_parser = $this->_create_parser();
		
		// Process the XML
    	if( !xml_parse( $xml_parser, $xml, TRUE ) ) {
            $this->addError('XML Problems while parsing the Import File. ' . sprintf("XML error: %s at line %d.",
                      xml_error_string(xml_get_error_code($xml_parser)),
                      xml_get_current_line_number($xml_parser)) . "\n" . $xml);
	    }
        xml_parser_free($xml_parser);
    }

    /**
     * Returns the Array with the parsed SQL Statements.
     */
    function getSqlArray() {
        return $this->sqlArray;
    }

    /**
     * Sets the runtime mode for this XML Parsing.
     */
    function setMode($mode) {
        if(is_string($mode))
            $this->mode = $mode;
    }

    /**
     * Returns the Schema Version or FALSE.
     */
	function getSchemaVersion($xmlstring) {
		if( !is_string( $xmlstring ) OR empty( $xmlstring ) ) {
			return FALSE;
		}
		
		if( preg_match( $this->getVersionRegExp(), $xmlstring, $matches ) ) {
			return !empty( $matches[2] ) ? $matches[2] : XML_SQL_DEFAULT_SCHEMA_VERSION;
		}
		
		return FALSE;
	}

	/**
	 * @access private
	 */
	function addSQL($select, $insert, $update = NULL, $mode = XML_SQL_DEFAULT_MODE) {
		
		if(is_string($select) &&  is_string($insert)) {
            if($mode == $this->mode) {
	    		foreach($this->replacer AS $search => $replace) {
	    			$select = str_replace($search,$replace,$select); 
	    		}
	    		if($this->adoDbConn != null) 
	    		{
		    		$temp = $this->adoDbConn->Execute($select);
		    		if($temp && $temp->RecordCount() == 0) {
        				foreach($this->replacer AS $search => $replace) {
        					$insert = str_replace($search,$replace,$insert); 
        				}
	    				$this->sqlArray[] = $insert;
		    		} else {
		    			if(is_string($update)) {
        	    			foreach($this->replacer AS $search => $replace) {
            					$update = str_replace($search,$replace,$update); 
            				}
	    					$this->sqlArray[] = $update;
	                    }
    	            }
	    		} 
	    		else {
       	    		foreach($this->replacer AS $search => $replace) {
    	    			$insert = str_replace($search,$replace,$insert); 
    	    		}
    				$this->sqlArray[] = $insert;
	    		}
			}
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * @access private
	 */
	function addError($msg) {
		$this->errors[] = $msg;
	}
	
	function getError() {
		return $this->errors;
	}

    // ---------------------------------------------------------------------------------

	/**
	* XML Callback to process start elements
	*
	* @access private
	*/
	function _tag_open( &$parser, $tag, $attributes ) {
		switch( strtoupper( $tag ) ) {
			case 'TABLE':
				$this->obj = new XmlDbTable( $this, $attributes );
				xml_set_object($parser, $this->obj);
				break;
			case 'CONTENT':
                break;
			default:
				$this->addError('Wrong Field in _tag_open in XmlToSQql: ' . $tag);
		}
		
	}
	
	/**
	* XML Callback to process CDATA elements
	*
	* @access private
	*/
	function _tag_cdata( &$parser, $cdata ) {
	}
	
	/**
	* XML Callback to process end elements
	*
	* @access private
	* @internal
	*/
	function _tag_close( &$parser, $tag ) {
	}

}


/**
* Abstract DB Object. This class provides basic methods for database objects, such
* as tables and indexes.
* @access private
*/
class XmlDbObject {
	
	/**
	* var object Parent
	*/
	var $parent;
	
	/**
	* var string current element
	*/
	var $currentElement;
	
	/**
	* NOP
	*/
	function XmlDbObject( &$parent, $attributes = NULL ) {
		$this->parent =& $parent;
	}
	
	/**
	* XML Callback to process start elements
	*
	* @access private
	*/
	function _tag_open( &$parser, $tag, $attributes ) {
		
	}
	
	/**
	* XML Callback to process CDATA elements
	*
	* @access private
	*/
	function _tag_cdata( &$parser, $cdata ) {
		
	}
	
	/**
	* XML Callback to process end elements
	*
	* @access private
	*/
	function _tag_close( &$parser, $tag ) {
	}
	
	function create() {
		return array();
	}
	
	/**
	* Destroys the object
	*/
	function destroy() {
		unset( $this );
	}
	
	/**
	* Returns the prefix set by the ranking ancestor of the database object.
	*
	* @param string $name Prefix string.
	* @return string Prefix.
	*/
	function prefix($name = '') {
		return is_object( $this->parent ) ? $this->parent->prefix( $name ) : $name;
	}

    /**
     * Adds another SQL Statement.
     */
    function addSQL($select = NULL, $insert = NULL, $update = NULL, $mode = XML_SQL_DEFAULT_MODE) {
        $this->parent->addSQL($select,$insert,$update,$mode);
    }

    /**
     * Escapes and Quotes the SQL Value.
     */ 
    function escapeAndQuoteValue($value) {
        return "'".addslashes($value)."'";
    }
}

/**
 * Creates a table object.
 *
 * This class stores information about a database table. As charactaristics
 * of the table are loaded from the external source, methods and properties
 * of this class are used to build up the table description.
 * @access private
 */
class XmlDbTable extends XmlDbObject {
	
	/**
     * @var string Table name
     * @access private
	 */
	var $name;
	/**
     * @var object current DbRow
     * @access private
	 */
    var $row = null;
	/**
     * @var string the table mode
     * @access private
	 */
    var $mode = null;
	/**
     * @var boolean table wants to be updated?
     * @access private
	 */
    var $update = true;
	
	/**
	* Iniitializes a new table object.
	*
	* @param string $prefix DB Object prefix
	* @param array $attributes Array of table attributes.
	*/
	function XmlDbTable( &$parent, $attributes = NULL ) {
		$this->parent =& $parent;
		$this->name = $this->prefix($attributes['NAME']);
        if(isset($attributes['MODE']))
    		$this->mode = $attributes['MODE'];
    	else
    		$this->mode = $parent->mode;
        if(isset($attributes['UPDATE'])) {
            if(strtolower($attributes['UPDATE']) == 'false')
        		$this->update = false;
            else
        		$this->update = (bool)$attributes['UPDATE'];
        }
	}

    /**
     * Returns the prefixed Table Name.
     */
    function getTableName() {
        return $this->name;
    }
	
	/**
	* XML Callback to process start elements. Elements currently 
	* processed are: ROW
	*
	* @access private
	*/
	function _tag_open( &$parser, $tag, $attributes ) {
		$this->currentElement = strtoupper($tag);
		
		switch( $this->currentElement ) {
			case 'ROW':
                if($this->mode != null && !isset($attributes['MODE']))
                    $attributes['MODE'] = $this->mode;
                if(!isset($attributes['UPDATE']))
                    $attributes['UPDATE'] = $this->update;
                $this->row = new XmlDbRow($this, $attributes);
				xml_set_object($parser, $this->row);
				break;
			default:
				$this->addError('Wrong Field in _tag_open in XmlDbTable: ' . $tag);
		}
	}
	
	/**
	* XML Callback to process CDATA elements
	*
	* @access private
	*/
	function _tag_cdata( &$parser, $cdata ) {
	}
	
	/**
	* XML Callback to process end elements
	*
	* @access private
	*/
	function _tag_close( &$parser, $tag ) {
		$this->currentElement = '';
		
		switch( strtoupper( $tag ) ) {
			case 'TABLE':
				xml_set_object( $parser, $this->parent );
				$this->destroy();
				break;
			default:
				$this->addError('Wrong Field in _tag_close in XmlDbTable: ' . $tag);
                break;
		}
	}
}

/**
 * Creates a table row object.
 * * This class stores information about a database row. 
 * @access private
 */
class XmlDbRow extends XmlDbObject {
	
	/**
 	 * @access private
 	 */
	var $update = false;
	/**
 	 * @access private
 	 */
    var $columns = array();
	/**
 	 * @access private
 	 */
    var $currentColumn = null;
	/**
 	 * @access private
 	 */
    var $rowMode = XML_SQL_DEFAULT_MODE;
	/**
 	 * @access private
 	 */
    var $passedColumns = array();
	
	/**
	* Iniitializes a new row object.
	*
	* @param string $parent the Table DB Object
	* @param array $attributes Array of table attributes.
	*/
	function XmlDbRow( &$parent, $attributes = NULL ) {
		$this->parent =& $parent;
        if(isset($attributes['UPDATE'])) {
            if(strtolower($attributes['UPDATE']) == 'false')
        		$this->update = false;
            else
    		$this->update = (bool)$attributes['UPDATE'];
        }
        if(isset($attributes['MODE']))
    		$this->rowMode = $attributes['MODE'];
    	else
    		$this->rowMode = $parent->mode;
	}

	/**
	* XML Callback to process start elements. Elements currently 
	* processed are: ROW
	*
	* @access private
	*/
	function _tag_open( &$parser, $tag, $attributes ) {
		$this->currentElement = strtoupper( $tag );
        $this->currentColumn = array( 'name'  => $tag, 
                                      'key'   => (isset($attributes['KEY']) && $attributes['KEY'] == 'true' ? true : false),
                                      'func'  => (isset($attributes['FUNCTION']) && $attributes['FUNCTION'] == 'true' ? true : false),
                                      'null'  => (isset($attributes['NULL']) && $attributes['NULL'] == 'true' ? true : false),
			        				  'value' => ''
        );
	}
	
	/**
	* XML Callback to process CDATA elements
	*
	* @access private
	*/
	function _tag_cdata( &$parser, $cdata ) {
		if( $this->currentElement != null && $this->currentElement != '' ) {
 	       $this->currentColumn['value'] .= $cdata;
		}
	}
	
	/**
	* XML Callback to process end elements
	*
	* @access private
	*/
	function _tag_close( &$parser, $tag ) {
		$this->currentElement = '';
		
		switch( strtoupper( $tag ) ) {
			case 'ROW':
				$this->create( $this->parent );
				xml_set_object( $parser, $this->parent );
				$this->destroy();
				break;
			default:
                $this->columns[] = $this->currentColumn;
                $this->currentColumn = null;
                break;
		}
	}

    function create(&$parent) {
        $updateWhere = '';
        $updateWhat = '';
        $select = '';
        $insert = 'INSERT INTO ' . $parent->getTableName() . ' ';

        $values = '';
        $columnNames = '';
        for($i=0; $i < count($this->columns); $i++) {
            $column = $this->columns[$i];
            $name = strtolower($column['name']);
            if($column['func'] === false) {
	            if($column['null'] === false)
	                $value = $this->escapeAndQuoteValue($column['value']);
				else
	                $value = "null";
			}
            else {
                $value = $column['value'];
			}

			// prepare select and update statement
            if($column['key']) {   
                if($select != '')
                    $select .= ' AND ';
                $select .= $name . '=' . $value;
                if($updateWhere != '')
                    $updateWhere .= ' AND ';
                $updateWhere .= $name . '=' . $value;
            } else {
                if($updateWhat != '')
                    $updateWhat .= ', ';
                $updateWhat .= $name . '=' . $value;
            }
            // prepare insert statement
            if(!isset($this->passedColumns[$name])) {
                if($columnNames != '') {
                    $columnNames .= ', ';
                    $values .= ', ';
                }
                $columnNames .= $name;
                $values .= $value;
            }
            // remember the column name
            $this->passedColumns[$name] = $name;
        }
        $insert .= '(' . $columnNames . ') VALUES ('.$values.');';
        $select = 'SELECT * FROM ' . $parent->getTableName() . ' WHERE ' . $select . ';';
        $update = 'UPDATE ' . $parent->getTableName() . ' SET ' . $updateWhat . ' WHERE '.$updateWhere.';';
        // column does not want to be updated!
        if(!$this->update)
            $update = null;

		$this->addSQL($select,$insert,$update,$this->rowMode);
        
        return TRUE;
    }
}
?>