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
 * @subpackage item
 */

/**
 * This file holds method for manipulating unique names.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage seo
 */

import('classes.item.Item');
import('classes.item.MasterItemType');
import('classes.item.ItemAdminService');


    /**
     * This adds or changes an unique name for an Item language version.
     * You do not have to care about the method, only pass the three required values.
     */
    function bigace_set_unique_name($itemtype, $id, $langid, $name)
    {
        $values = array( 'UNIQUE_NAME' => $name,
                         'ITEM_ID'     => $id,
                         'LANGUAGE'    => $langid,
        				 'ITEMTYPE'    => $itemtype );
        
        $admin = new ItemAdminService($itemtype);
        $admin->changeItemColumnLanguage($id, $langid, 'unique_name', $name);

        // see if there is already an unique name existing 
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('unique_name_fetch');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

        // only insert no entry is existing AND if name is not empty
        if($res->count() == 0 && strlen($name) > 0) {
           	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('unique_name_insert');
        }
        else {
        	// only update, if name is not empty
        	if(strlen($name) > 0)
        		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('unique_name_update');
        	else
        		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('unique_name_delete');
        }

        // prepare statement
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        // execute the prepared sql
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
     * Returns a valid unique name String with special character replaced by the passed Delimiter.
     */
    function bigace_build_unique_name($name, $extension) {
   		$delim = ConfigurationReader::getConfigurationValue('seo', 'word.delimiter', '-'); 
   		$lower = ConfigurationReader::getConfigurationValue('seo', 'url.lowercase', false); 
   		
	   	$name = trim($name);
    		
        // replace all strange german character by delimiter
        $search = array("Ä","Ö","Ü","ä","ö","ü","ß","\t","\r","\n"," ");
        $replace = array("AE","OE","UE","ae","oe","ue","ss",$delim,$delim,$delim,$delim);
        $name = str_replace($search, $replace, $name);

        // replace all other special character by the delimiter
        // and then all occurences of multiple delimiter by a single one
        $name = preg_replace("/($delim)+/", $delim, preg_replace("/[^a-zA-Z0-9.,\/_-\\s]/", $delim, $name));
	   	
        // strip all ending delimitier
        while($name[strlen($name)-1] == $delim) {
            $name = substr($name, 0, strlen($name)-1);
        }
        
        // strip all starting delimitier
        while($name[0] == $delim) {
            $name = substr($name, 1, strlen($name));
        }

        if (strlen($extension) > 0 && substr_count($name,$extension) == 0) $name .= $extension;
        if($lower) $name = strtolower($name); 	
         	
        return $name;
    }

    
	/**
     * Removes all unique names for the given Item language version.
     */
    function bigace_delete_unique_name($itemtype, $id, $langid) 
    {
        $values = array( 'ITEM_ID'  => $id,
                         'LANGUAGE' => $langid,
                         'ITEMTYPE'	=> $itemtype);

        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('unique_name_delete');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }
    
    