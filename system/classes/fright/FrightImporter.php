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
 
loadClass('util', 'IOHelper');
loadClass('fright', 'FrightAdminService');
loadClass('fright', 'FrightStringsEnumeration');
loadClass('group',  'GroupAdminService');
loadClass('group',  'GroupEnumeration');

/**
 * @access private
 */
define('_NODE_GROUPS',  'GROUPS');
/**
 * @access private
 */
define('_NODE_FRIGHTS', 'FRIGHTS');
/**
 * @access private
 */
define('_NODE_MAPPINGS','MAPPINGS');
/**
 * @access private
 */
define('_NODE_MAPPING', 'MAPPING');
/**
 * @access private
 */
define('_NODE_GROUP',   'GROUP');
/**
 * @access private
 */
define('_NODE_FRIGHT',  'FRIGHT');
/**
 * @access private
 */
define('_NODE_IMPORT_ROOT',  'FrightExport');
/**
 * @access private
 */
define('_NODE_GROUP_ID',            'GROUP_ID');
/**
 * @access private
 */
define('_NODE_GROUP_NAME',          'NAME');
/**
 * @access private
 */
define('_NODE_FRIGHT_ID',           'FRIGHT_NAME');
/**
 * @access private
 */
define('_NODE_FRIGHT_VALUE',           'VALUE');
/**
 * @access private
 */
define('_NODE_FRIGHT_NAME',         'NAME');
/**
 * @access private
 */
define('_NODE_FRIGHT_DESCRIPTION',  'DESCRIPTION');
/**
 * @access private
 */
define('_NODE_FRIGHT_DEFAULT',  'DEFAULT');
/**
 * @access private
 */
define('_IMPORT_FILE_EXTENSION', 'xml');

/**
 * Directory where import files should be stored, for later backup or review...
 */
define('_BIGACE_IMPORT_DIRECTORY', $GLOBALS['_BIGACE']['DIR']['consumer'] . 'import/');

/**
 * The FrightImporter is able to import a XML as File or String and setup 
 * the system with the configured settings.
 * CAREFULL: It deletes all existing settings within the System, make sure your Import
 * is working. A Backup is created before the delete is performed, so your are always able to 
 * recover your old system.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage fright
 */
class FrightImporter
{
	/**
	 * the XML Parser we are using
	 * @access private
	 */
    var $xml_parser;
	/**
	 * Remember in which Node we are currently
	 * @access private
	 */
    var $inNode = array();
	/**
	 * the XML Parser we are using
	 * @access private
	 */
    var $currentValues = array();
	/**
	 * the current Node name
	 * @access private
	 */
    var $currentTag    = '';
    
	/**
	 * @access private
	 */
    var $frightAdmin;
	/**
	 * @access private
	 */
    var $groupAdmin;
     
    function FrightImporter() 
    {
        $this->groupAdmin = new GroupAdminService();
        $this->frightAdmin = new FrightAdminService();
    }

    /**
    * Import an XML File
    */
    function importFile($filename)
    {
        $couldRead = FALSE;
        if (file_exists($filename)) 
        {
            $extension = getFileExtension($filename);
            if (strnatcasecmp($extension, _IMPORT_FILE_EXTENSION) == 0)
            {
                $content = file_get_contents($filename);
                $couldRead = TRUE;
                $GLOBALS['LOGGER']->logInfo('Importing Fright File: ' . $filename);
                return $this->importXML($content);
            } 
            else
            {
                $GLOBALS['LOGGER']->log(E_USER_ERROR, 'File to import seems to have the wrong Format, found File Extension ('.$extension.') expecting ('._IMPORT_FILE_EXTENSION.')');
            }
        }
        else 
        {
            $GLOBALS['LOGGER']->log(E_USER_ERROR, 'Could not find File to import: ' . $filename);
        }

        return $couldRead;
    }

    function importXML($xml)
    {
        if ($this->checkForIntegrity($xml)) 
        {
            $this->xml_parser = xml_parser_create();
    
            xml_set_object($this->xml_parser, $this);
            xml_set_element_handler($this->xml_parser, "tag_open", "tag_close");
            xml_set_character_data_handler($this->xml_parser, "cdata");
            
            // do Parsing
            if (!xml_parse($this->xml_parser, $xml, true)) {
                $GLOBALS['LOGGER']->logError('XML Problems while parsing the Import File. ' . sprintf("XML error: %s at line %d.",
                                xml_error_string(xml_get_error_code($this->xml_parser)),
                                xml_get_current_line_number($this->xml_parser)) . "\n" . $xml);
                return false;                                
            }

            xml_parser_free($this->xml_parser);
            return true;
        }
        
        return false;
    }
    
    /**
     * Prepares the Import by creating a Backup and deleting all Groups
     * and Fright Mappings.
     *  
     * @access private
     */
    function prepareImport($xml) 
    {
        // first create backup
        $exporter = new FrightExporter();
        $filename = $exporter->saveDump('fright_backup_' . time() . '.' . _IMPORT_FILE_EXTENSION);
        $GLOBALS['LOGGER']->logInfo('Saved Fright Backup before importing: ' . $filename);
        
        $groupEnum = new GroupEnumeration();
        for ($i=0; $i < $groupEnum->count(); $i++) 
        {
            $group = $groupEnum->next();
            $gid = $group->getID();
            
            $this->frightAdmin->deleteAllGroupFrights($gid);
            $this->groupAdmin->deleteGroup($gid);
            $GLOBALS['LOGGER']->logInfo('Deleted all settings for Group ('.$gid.') before import!');
        }
        unset($i);
        
        $frightEnum = new FrightStringsEnumeration();
        for($i=0; $i < $frightEnum->count(); $i++)
        {
            $fright = $frightEnum->next();
            $fid = $fright->getID();
            $this->frightAdmin->deleteFright($fid);
            $GLOBALS['LOGGER']->logInfo('Deleted Fright ('.$fid.') before import!');
        }
    }
    
    /**
     * Checks if the gievn XML is proper formatted.
     * @access private
     */
    function checkForIntegrity($xml) 
    {
        $isValid = TRUE;
        
        // FIXME add some integrity check, otherwise we might delete all settings for a simple text file...
        
        return $isValid;
    }

    /**
     * Get the Node we are currently parsing!
     * @access private
     */
    function getCurrentNode() {
        return $this->currentTag;
    }

    /**
     * Set the Node we are currently parsing!
     * @access private
     */
    function setCurrentNode($tag) {
        $this->currentTag = $tag;
    }

    /**
     * @access private
     */
    function setCurrentValue($value) 
    {
        $value = trim($value);
        if ($value != '' && $value != "\n") {
            $this->currentValues[$this->getCurrentNode()] = $value;
        }
    }

    /**
     * @access private
     */
    function getCurrentValue() 
    {
        return $this->currentValues[$this->getCurrentNode()];
    }
    
    /**
    * Clears all values currently cached!
    * @access private
    */
    function clearCurrentValue() {
        return $this->currentValues = array();
    }

    /**
     * @access private
     */
    function tag_open($parser, $tag, $attributes)
    { 
        $this->checkNode(_NODE_GROUP, $tag, TRUE);
        $this->checkNode(_NODE_FRIGHT, $tag, TRUE);
        $this->checkNode(_NODE_MAPPING, $tag, TRUE);
        
        if(strnatcasecmp(_NODE_GROUP,$tag) == 0){
            $this->clearCurrentValue();
        } else if(strnatcasecmp(_NODE_FRIGHT,$tag) == 0){
            $this->clearCurrentValue();
        } else if(strnatcasecmp(_NODE_MAPPING,$tag) == 0){
            $this->clearCurrentValue();
        }
        
        $this->setCurrentNode($tag);
    }

    /**
     * @access private
     */
    function cdata($parser, $cdata)
    {
        if ($this->isInNode(_NODE_GROUP) || $this->isInNode(_NODE_FRIGHT) || $this->isInNode(_NODE_MAPPING)) {
            $this->setCurrentValue($cdata);
        }
    }

    /**
     * @access private
     */
    function tag_close($parser, $tag)
    {
        if(strnatcasecmp(_NODE_GROUP,$tag) == 0){
            if (isset ($this->currentValues[_NODE_GROUP_ID]) && isset ($this->currentValues[_NODE_GROUP_NAME])) {
                $id = $this->currentValues[_NODE_GROUP_ID];
                $name = $this->currentValues[_NODE_GROUP_NAME];
                echo "Create Group with ID ($id) and Name ($name)<br>";
                $this->groupAdmin->createGroupWithID($id, $name);
            }
        } else if(strnatcasecmp(_NODE_FRIGHT,$tag) == 0){
            // do not check for the default flag to be backward-compatible
            if (isset ($this->currentValues[_NODE_FRIGHT_NAME]) && isset ($this->currentValues[_NODE_FRIGHT_DESCRIPTION])) {
                $name = $this->currentValues[_NODE_FRIGHT_NAME];
                $desc = $this->currentValues[_NODE_FRIGHT_DESCRIPTION];
                $default = $this->currentValues[_NODE_FRIGHT_DEFAULT];
                echo "Create Fright with Name ($name), Default ($default) and Description ($desc)<br>";
                $this->frightAdmin->createFright($name, $desc, $default);
            }
        } else if(strnatcasecmp(_NODE_MAPPING,$tag) == 0){
            if (isset ($this->currentValues[_NODE_GROUP_ID]) && isset ($this->currentValues[_NODE_FRIGHT_ID])) {
                $gid = $this->currentValues[_NODE_GROUP_ID];
                $fid = $this->currentValues[_NODE_FRIGHT_ID];
                $value = $this->currentValues[_NODE_FRIGHT_VALUE];
                echo "Create Mapping between Group ($gid) and Fright ($fid) with Value ($value)<br>";
                $this->frightAdmin->createGroupFright($gid, $fid, $value);
            }
        }

        $this->checkNode(_NODE_GROUP, $tag, FALSE);
        $this->checkNode(_NODE_FRIGHT, $tag, FALSE);
        $this->checkNode(_NODE_MAPPING, $tag, FALSE);
    }
    
    /**
     * @access private
     */
    function checkNode($name, $tag, $value) {
        if (strnatcasecmp($name,$tag) == 0) {
            $this->setIsInNode($name, $value);
        }
    }
    
    /**
     * @access private
     */
    function setIsInNode($name, $value) {
        $this->inNode[$name] = $value;
    }
    
    /**
     * @access private
     */
    function isInNode($name) {
        if (!isset($this->inNode[$name])) {
            return FALSE;
        } else {
            return $this->inNode[$name];
        }
    }
}

?>