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
 * @subpackage template
 */
 
/**
 * This represents a SmartyTemplate. 
 * A SmartyTemplate combined with a SmartyStylesheet is a SmartyDesign, which
 * is chooseable in the Menu Administration.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage smarty
 */
class SmartyTemplate
{
	/**
	 * @access private
	 */
    var $value;

    function SmartyTemplate($name = null) {
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
    function loadByName($name)
    {
	    $values = array( 'NAME' => $name );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_template_load');
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

    function isInWork() {
        return ($this->value["inwork"] == 1 ? true : false);
    }

	/**
	 * If is a System Template that might NOT be deleted!
	 */
    function isSystem() {
        return ($this->value["system"] == 1 ? true : false);
    }

    function isInclude() {
        return ($this->value["include"] == 1 ? true : false);
    }

    function getTimestamp() {
        return $this->value["timestamp"];
    }

    function getChangedBy() {
        return $this->value["userid"];
    }

    function getContent() {
        return $this->value["content"];
    }
    
}
?>