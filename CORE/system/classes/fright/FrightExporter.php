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
 * @subpackage fright
 */

loadClass('fright', 'FrightImporter');
loadClass('util', 'IOHelper');

/**
 * @access private
 */
define('_BREAK',        'break');
/**
 * @access private
 */
define('_SPACE',        'space');
/**
 * @access private
 */
define('_START',        'start');
/**
 * @access private
 */
define('_END',          'end');

/**
 * The FrightExporter creates a full export of your Groups, 
 * Frights and GroupFright Mappings.
 * This export can be imported by the FrightImporter!
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage fright
 */
class FrightExporter
{
	/**
	 * @access private
	 */
    var $xmlChars = array(_BREAK => "\n", _END => '>', _START => '<', _SPACE => '   ');
	/**
	 * @access private
	 */
    var $htmlChars = array(_BREAK => '<br>', _END => '&gt;', _START => '&lt;', _SPACE => '&nbsp;&nbsp;');
	/**
	 * @access private
	 */
    var $export = '';

    function FrightExporter() {
    }
    
    function getExportArray() 
    {
        $xmlExport = array( _NODE_GROUPS => array(), _NODE_FRIGHTS => array(), _NODE_MAPPINGS => array());

        // export the frights itself with name, default-value and description
        $ENUM = new FrightStringsEnumeration();
        for($i=0; $i < $ENUM->count(); $i++)
        {
            $temp = $ENUM->next();
            array_push($xmlExport[_NODE_FRIGHTS], array(_NODE_FRIGHT => array(_NODE_FRIGHT_NAME => $temp->getName(), _NODE_FRIGHT_DEFAULT => $temp->getDefault(), _NODE_FRIGHT_DESCRIPTION => $temp->getDescription())));
        }
        unset($ENUM);
        
        // export the groups with id and name
        $ENUM = new GroupEnumeration();
        for($i=0; $i < $ENUM->count(); $i++)
        {
            $temp = $ENUM->next();
            array_push($xmlExport[_NODE_GROUPS], array(_NODE_GROUP => array(_NODE_GROUP_ID => $temp->getID(), _NODE_GROUP_NAME => $temp->getName())));
        }
        unset($ENUM);
        
        // export all fright-mappings for the groups
        $ENUM = new GroupEnumeration();
        for($i=0; $i < $ENUM->count(); $i++)
        {
            $temp = $ENUM->next();
            $groupRights = new GroupFrightEnumeration($temp->getID());
            for($a=0; $a < $groupRights->count(); $a++)
            {
                $tempRight = $groupRights->next();
                array_push($xmlExport[_NODE_MAPPINGS], array(_NODE_MAPPING => array(_NODE_GROUP_ID => $temp->getID(), _NODE_FRIGHT_ID => $tempRight->getID(), _NODE_FRIGHT_VALUE => $groupRights->getValue())));
            }
        }
        unset($ENUM);
        return $xmlExport;
    }
    
    function saveDump($filename = '', $dirname = '') 
    {
        if ($filename == '') {
        	$filename = 'fright_export_' . time() . '.' . _IMPORT_FILE_EXTENSION;
        }

        if ($dirname == '') {
    	    $dirname = $GLOBALS['_BIGACE']['DIR']['consumer'] . 'export/';
    	}
    	
        IOHelper::createDirectory($dirname);
        
    	$fullfile = $dirname . $filename;

        $fpointer = fopen($fullfile, "wb");
        fputs($fpointer, $this->getDump());
        fclose($fpointer);
        
        return $fullfile;
    }
    
    function getDump() 
    {
        $toXml = $this->getExportArray();
        $this->createDump($this->xmlChars, $toXml);
        return $this->export;
    }

    function getDumpAsHTML() 
    {
        $toXml = $this->getExportArray();
        $this->createDump($this->htmlChars, $toXml);
        return $this->export;
    }

    function createDump($chars, $toXml)
    {
        $this->showStartTag($chars, '?xml version="1.0"?', 0, TRUE);
    
        $this->showStartTag($chars, _NODE_IMPORT_ROOT, 0, TRUE);
        if (isset($toXml[_NODE_GROUPS])) {
            $this->showTree($chars, _NODE_GROUPS, $toXml[_NODE_GROUPS]);
        }
    
        if (isset($toXml[_NODE_FRIGHTS])) {
            $this->showTree($chars, _NODE_FRIGHTS, $toXml[_NODE_FRIGHTS]);
        }
    
        if (isset($toXml[_NODE_MAPPINGS])) {
            $this->showTree($chars, _NODE_MAPPINGS, $toXml[_NODE_MAPPINGS]);
        }
        $this->showEndTag($chars, _NODE_IMPORT_ROOT, 0, TRUE, TRUE);
    }
    
    /**
     * @access private
     */
    function showTree($chars, $arrayKey, $values) 
    {
        $this->showStartTag($chars, $arrayKey, 1);
        foreach ($values AS $key => $entry) {
            $this->showXml($chars, $entry, 2);
        }    
        $this->showEndTag($chars, $arrayKey, 1, TRUE, TRUE);
    }
    
    /**
     * @access private
     */
    function showStartTag($chars, $name, $depth, $addBr = TRUE) {
        $this->showDepth($chars, $depth);
        $this->showElement($chars, $name, $depth, $addBr);
    }
    
    /**
     * @access private
     */
    function showEndTag($chars, $name, $depth, $addBr = TRUE, $showDepth = FALSE) {
        if ($showDepth)
            $this->showDepth($chars, $depth);
        $this->showElement($chars, '/'.$name, $depth, $addBr);
    }
    
    /**
     * @access private
     */
    function showElement($chars, $name, $depth, $addBr) 
    {
        $this->addToString($chars[_START].$name.$chars[_END]);
        if ($addBr) {
            $this->addToString( $chars[_BREAK] );
        }
    }
    
    /**
     * @access private
     */
    function showDepth($chars, $depth) 
    {
        for($i=0; $i < $depth; $i++) {
            $this->addToString( $chars[_SPACE] );
        }
    }
    
    /**
     * @access private
     */
    function showXml($chars, $toXml, $depth) 
    {
        foreach ($toXml AS $name => $value) 
        {
            if (is_array($value)) {
                $this->showStartTag($chars, $name, $depth, TRUE);
                $this->showXml($chars, $value, $depth+1);
                $this->showEndTag($chars, $name, $depth, TRUE, TRUE);
            } else {
                $this->showStartTag($chars, $name, $depth, FALSE);
                $this->addToString( $value );
                $this->showEndTag($chars, $name, $depth, TRUE, FALSE);
            }
        }
    }
    
    /**
     * @access private
     */
    function addToString($s) {
        $this->export .= $s;
    }

}

?>