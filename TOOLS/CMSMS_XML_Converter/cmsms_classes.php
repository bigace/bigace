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
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.tools
 */

/**
 * This file contains PHP Classes (PHP 4), which can be used for easy extraction 
 * of CMS Made Simple Templates.
 */ 

if(!defined('CMSMS_FOR_BIGACE')) die('Not runnable');

class CMSMS_TemplateImporter
{

	var $sawRoot = false;
	var $name = null;
	var $dtd = null;
	var $stylesheet = array();
	var $template = array();
	var $mmtemplate = array();
	var $assoc = array();
	var $reference = array();

	function CMSMS_TemplateImporter() {
	}

	/**
	 * @access private
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

	function importFile($filename) {
		$this->parseFromString( $this->get_file_contents($filename) );
	}
	
	function parseFromString($xml) {
        $xml_parser = $this->_create_parser();
		
		// Process the XML
    	if( !xml_parse( $xml_parser, $xml, TRUE ) ) {
            $this->addError('XML Problems while importing the CMS MS Template File. ' . sprintf("XML error: %s at line %d.",
                      xml_error_string(xml_get_error_code($xml_parser)),
                      xml_get_current_line_number($xml_parser)) . "\n" . $xml);
	    }
        xml_parser_free($xml_parser);
    }


	/**
	 * @access private
	 */
	function addError($msg) {
		echo "<h1>".$msg."</h1>";
	}

    // ---------------------------------------------------------------------------------

	/**
	* XML Callback to process start elements
	*
	* @access private
	*/
	function _tag_open( &$parser, $tag, $attributes ) {

		switch( strtolower( $tag ) ) {
			case 'name':
				$this->name = new ImportSingleElement( $this, $attributes, $tag );
				xml_set_object($parser, $this->name);
				break;
			case 'dtdversion':
				$this->dtd = new ImportSingleElement( $this, $attributes, $tag );
				xml_set_object($parser, $this->dtd);
                break;
			case 'template':
				$this->template[] = new ImportNestedElements( $this, $attributes, $tag, array('tname','tencoding','tdata') );
				xml_set_object($parser, $this->template[count($this->template)-1]);
                break;
			case 'mmtemplate':
				$this->mmtemplate[] = new ImportNestedElements( $this, $attributes, $tag, array('mmtemplate_name','mmtemplate_data') );
				xml_set_object($parser, $this->mmtemplate[count($this->mmtemplate)-1]);
                break;
			case 'stylesheet':
				$this->stylesheet[] = new ImportNestedElements( $this, $attributes, $tag, array('cssname','cssmediatype','cssdata') );
				xml_set_object($parser, $this->stylesheet[count($this->stylesheet)-1]);
                break;
			case 'assoc':
				$this->assoc[] = new ImportNestedElements( $this, $attributes, $tag, array('assoc_tname','assoc_cssname') );
				xml_set_object($parser, $this->assoc[count($this->assoc)-1]);
                break;
			case 'reference':
				$this->reference[] = new ImportNestedElements( $this, $attributes, $tag, array('refname','refencoded','reflocation','refdata') );
				xml_set_object($parser, $this->reference[count($this->reference)-1]);
                break;
			case 'theme':
				$this->sawRoot = true;
				break;
			default:
				$this->addError('Wrong Field in _tag_open in CMSMS_TemplateImporter: ' . $tag);
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

class ImportNestedElements extends XmlImportObject {
	var $values;
	var $keys;

	function ImportNestedElements( &$parent, $attributes = NULL, $name, $nested ) {
		$this->parent =& $parent;
		$this->setCurrentElement($name);
		$this->keys = $nested;
	}

	function getValue($name) {
		return $this->values[$name]->getValue();
	}

	function _tag_open( &$parser, $tag, $attributes ) {
		if( $tag == null || $tag == '' )
			return;

		$found = false;
		foreach($this->keys AS $searchTag)
		{
			if(strtolower($tag) == strtolower($searchTag)) 
			{
				$this->values[$searchTag] = new ImportSingleElement( $this, $attributes, $tag );
				xml_set_object($parser, $this->values[$searchTag]);
				$found = true;
				break;
			}
		}

		if(!$found) {
			echo '<h1>Wrong Field in _tag_open in ImportNestedElements: ' . $tag . '</h1>';
		}
	}
}


class ImportSingleElement extends XmlImportObject {

	var $value = null;

	function ImportSingleElement( &$parent, $attributes = NULL, $name ) {
		$this->parent =& $parent;
		$this->setCurrentElement($name);
	}

	function getValue() {
		return $this->value;
	}

	function _tag_cdata( &$parser, $cdata ) {
		if( $this->getCurrentElement() == null || $this->getCurrentElement() == '' )
			return;

		$this->value = $cdata;
	}


}

/**
* @access private
*/
class XmlImportObject {
	
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
	function XmlImportObject( &$parent, $attributes = NULL ) {
		$this->parent =& $parent;
	}
	
	function setCurrentElement($name) {
		$this->currentElement = strtoupper($name);
	}

	function getCurrentElement() {
		return $this->currentElement;
	}

	/**
	* XML Callback to process start elements
	*
	* @access private
	*/
	function _tag_open( &$parser, $tag, $attributes ) {
		$this->setCurrentElement($tag);
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
		switch( strtoupper($tag) ) {
			case $this->currentElement:
				xml_set_object( $parser, $this->parent );
				break;
			default:
				echo '<h1>XmlImportObject->_tag_close: ' . $tag . '/'.$this->currentElement.'</h1>';
                break;
		}
		$this->currentElement = null;
	}
}

?>