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

import('classes.item.ItemProjectService');
import('api.portlet.PortletService');

/**
 * The Root XML Node of a Portlet configuration.
 */
define('PORTLET_XML_ROOT', 'Portlets');

/**
 * Prefix for each Column.
 */
define('PORTLET_COLUMN_PREFIX', 'portlet.config.column.');

/**
 * This class provides methods for easy reading and parsing of Portlets.
 * The Portlet settings are saved (by default) in a Text-Project-Field with the Key: 
 * <code>PORTLET_COLUMN_PREFIX . PORTLET_DEFAULT_COLUMN</code>
 * The Column can be changed, in order to save multiple Columns for one Item.
 * 
 * If one of the configured Portlet Classes can not be found in the 
 * environment by calling <code>class_exists($classname)</code>, it trys to 
 * load it from the Portlets Package by calling:
 * <code>import('classes.portlets.'.$classname)</code>
 *  
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage parser
 */
class PortletParser extends PortletService
{
    private $ignoreDisplaySetting = false;
    
    /**
     * Create a new instance of the PortletParser.
     */
    function PortletParser() {
    }
    
    /**
     * If this function is called with <code>true</code> also 
     * the Portlets are rendered, that return <code>false</code> for 
     * <code>Portlet->displayPortlet()</code>. 
     */
    function setIgnoreDisplaySetting($ignorePortletDisplaySetting = false) {
        $this->ignoreDisplaySetting = $ignorePortletDisplaySetting;
    }
    
    /**
     * @access private
     */
    function getProjectTextID($column = null) {
        if($column != null)
            return PORTLET_COLUMN_PREFIX . $column;
        return PORTLET_COLUMN_PREFIX . PORTLET_DEFAULT_COLUMN;
    }
    
    /**
     * Get an array with ready parsed and configured Portlets for the given Itemtype and ID.
     * Even if Portlets are ment to be used with Menus, it is possible to fetch them for 
     * all Itemtypes.
     */
    function getPortlets($itemtype, $itemid, $languageid, $column = null)
    {
        $xml = $this->getPortletsXML($itemtype, $itemid, $languageid, $column);
        if ($xml == '')
            return array(); 
        return $this->getPortletsFromXML($xml);
    }
    
    /**
     * Returns the XML that defines the Portlet settings.
     * If no XML is found for the current Menu, it searches the way-home till TOP-LEVEL and
     * returns the first occurence.
     * If no XML could be found on the complete way-home, an empty String is returned.
     * @access private
     */
    function getPortletsXML($itemtype, $itemid, $languageid, $column = null)
    {
        $column = $this->getProjectTextID($column);

        $projectService = new ItemProjectService($itemtype);
        $itemService    = new ItemService($itemtype);
        $portletXML = '';

        
        do {
            $tempItem = $itemService->getItem($itemid, ITEM_LOAD_LIGHT, $languageid);
            if(!$tempItem->exists()) {
                $itemid = _BIGACE_TOP_PARENT;
            }
            else {
                if($projectService->existsProjectText($tempItem->getID(), $tempItem->getLanguageID(), $column)) {
                    $portletXML = $projectService->getProjectText($tempItem->getID(), $tempItem->getLanguageID(), $column);
                } 
                $itemid = $tempItem->getParentID();
            }
            
        } while ($itemid != _BIGACE_TOP_PARENT && $portletXML == '');

        return $portletXML;
    }
    
    /**
     * Return the ready configured Portlets from the given XML.
     * @access private
     */
    function getPortletsFromXML($xml) 
    {
        $reader = new PortletXmlReader($this->ignoreDisplaySetting);
        $xml_parser = xml_parser_create();
        xml_set_object($xml_parser, $reader);
        xml_set_element_handler($xml_parser, "startElement", "endElement");
        xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0); // make sure the correct parameter cases are passed
        
        if (!xml_parse($xml_parser, $xml, true)) {
            $GLOBALS['LOGGER']->logError('Could not parse Portlets from XML. ' . sprintf("XML error: %s at line %d.",
                            xml_error_string(xml_get_error_code($xml_parser)),
                            xml_get_current_line_number($xml_parser)) . "\n" . $xml);
        }
        xml_parser_free($xml_parser);
        return $reader->getPortlets();
    }
    
    /**
     * Saves the given Portlet XML.
     * @access private
     */
    function savePortletsXML($itemtype, $itemid, $languageid, $xml, $column = null)
    {
        $column = $this->getProjectTextID($column);
        import('classes.item.ItemAdminService');
        $admin = new ItemAdminService($itemtype);
        return $admin->setProjectText($itemid, $languageid, $column, addslashes($xml));
    }
    
    /**
     * Converts and then saves the given Portlets.
     * Pass null or an empty array to delete portlet settings.
     */
    function savePortlets($itemtype, $itemid, $languageid, $portlets, $column = null)
    {
        if($portlets == null || is_string($portlets) || (is_array($portlets) && count($portlets) == 0)) {
            $xml = '';
        } else {
            $xml = $this->getXMLFromPortlet($portlets);
        }
            
        return $this->savePortletsXML($itemtype, $itemid, $languageid, $xml, $column);
    }
    
    /**
     * Converts an Array of ready configured Portlets into XML.
     * @access private
     */
    function getXMLFromPortlet($portlets)
    {
        $xml  = "<?xml version='1.0'?>\n";
        $xml .= " <".PORTLET_XML_ROOT.">\n";
        foreach($portlets AS $portlet)
        {
            if(is_class_of($portlet, 'Portlet')) 
            {
                $xml .= '    <'.$portlet->getIdentifier();
                foreach($portlet->getAllParameter() AS $key => $value)
                    $xml .= ' ' . $key . '="'.$value.'"';
                $xml .= ' />' . "\n";
            }
        }
        $xml .= " </".PORTLET_XML_ROOT.">\n";
        return $xml;
    }
    
}

/**
 * @access private
 * @package bigace.classes
 * @subpackage parser
 */
class PortletXmlReader
{
    private $ignoreDisplaySetting = false;
    private $portlets = array();

    function PortletXmlReader($ignoreDisplaySetting) {
        $this->ignoreDisplaySetting = $ignoreDisplaySetting;
    }
    
    function getPortlets() {
        return $this->portlets;
    }
    
    /**
     * Simple implementation of an XML Parser function.
     * @access private
     */
    function startElement($parser, $name, $attrs) 
    {
        if (strnatcasecmp($name,PORTLET_XML_ROOT) != 0)
        {
            if (!class_exists($name)) {
            	import('classes.portlets.'.$name);
            } 
            
            if (class_exists($name)) 
            {
                $portlet = new $name();
                if (is_subclass_of($portlet, 'Portlet'))
                {
                    if(count($attrs) > 0) {
                        foreach($attrs AS $key => $value) {
                            $portlet->setParameter($key, $value);
                        }
                    }
                    if($this->ignoreDisplaySetting || $portlet->displayPortlet())
                        array_push($this->portlets, $portlet);
                } else {
                    $GLOBALS['LOGGER']->logError('Configured Class ('.$name.') does not to extend Portlet!');
                }
            }
            else 
            {
                $GLOBALS['LOGGER']->logError('Configured Portlet ('.$name.') does not exist!');
            }
        }
        
    }
    
    /**
     * Simple implementation of an XML Parser function.
     * @access private
     */
    function endElement($parser, $name) 
    {
    }
} 
 
?>