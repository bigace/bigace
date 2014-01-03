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
 * @subpackage template
 */
 
/**
 * This represents a Stylesheet to be used in a SmartyDesign.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage smarty
 */
class SmartyStylesheet
{
    private $value;

    function SmartyStylesheet($name = null)
    {
    	if($name != null)
    		$this->loadByName($name);
    }
    
    /**
     * @access protected
     */
    function setArray($values){
    	$this->value = $values;
    }
    
    /**
     * @access private
     */
    function loadByName($name) {
	    $values = array( 'NAME' => $name );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_stylesheet_load');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values,true);
        $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $this->setArray( $temp->next() );
        unset($temp);
    }

    function getName() {
        return $this->value["name"];
    }
    
    function getDescription() {
        return $this->value["description"];
    }

    function getFilename() {
        return $this->value["filename"];
    }

    /**
     * Returns the SmartyStylesheet that is used for the Editor.
     * @return SmartyStylesheet he Editor Stylesheet
     */
    function getEditorStylesheet() {
    	if($this->value["editorcss"] == null || $this->value["editorcss"] == '')	
    		return $this;
        return new SmartyStylesheet($this->value["editorcss"]);
    }

	/**
	 * The absolute Filename on the hard drive.
	 * DO NOT USE AS src ATTRIBUTE IN HTML!  
	 */
	function getFullFilename() {
        return $GLOBALS['_BIGACE']['DIR']['stylesheets'] . $this->value["filename"];
    }
	
	/**
	 * Returns the URL for the Stylesheet, to be used in HTML.
	 * @return String the URL to the Stylesheet
	 */
	function getURL() {
        return _BIGACE_DIR_PUBLIC_WEB . 'cid'._CID_.'/' . $this->value["filename"];
    }
}
?>