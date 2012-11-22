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
 * Holds methods for receiving all information about Itemtypes.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage item
 */
class MasterItemType
{
	/**
	 * Empty Constructor.
	 */
    function MasterItemType() { }
    
    /**
     * Return the Directory for the given Itemtype ID.
     * @return String the Directory Name
     */
    function getDirectoryForItemType($itemtype)
    {
       return $GLOBALS['_BIGACE']['ITEMS'][$itemtype]['dir'];
    }
    
    /**
     * Return the Command for the given Itemtype ID. 
     * @return String the Command
     */
    function getCommandForItemType($itemtype)
    {
        return $GLOBALS['_BIGACE']['ITEMS'][$itemtype]['cmd'];
    }

    /**
     * Returns the Select Columns to be used for Item- and Treeselects.
     * These Columns are comma separated and can be directly pasted into 
     * a SQL "SELECT ... FROM ..." statement.
     *  
     * @return String the Columns to be selected as Comma separated List 
     */
    public static function getSelectColumns($itemtype, $treetype = ITEM_LOAD_FULL)
    {
    	if (isset($GLOBALS['_BIGACE']['SELECT']['item_'.$itemtype][$treetype])) {
        	return $GLOBALS['_BIGACE']['SELECT']['item_'.$itemtype][$treetype];
    	} else if (isset($GLOBALS['_BIGACE']['SELECT']['item_'.$itemtype]['default'])) {
        	return $GLOBALS['_BIGACE']['SELECT']['item_'.$itemtype]['default'];
        } else if (isset($GLOBALS['_BIGACE']['SELECT']['default'][$treetype])) {
        	return $GLOBALS['_BIGACE']['SELECT']['default'][$treetype];
        } else if (isset($GLOBALS['_BIGACE']['SELECT']['default']['default'])) {
        	return $GLOBALS['_BIGACE']['SELECT']['default']['default'];
        }
    	return 'a.*';
    }

    /**
     * Get the Itemtype ID for the given Command (or null).
     * @return int the Itemtype ID or null
     */
    function getItemTypeForCommand($cmd)
    {
    	foreach ( $this->getItemTypeArray() AS $itemtype => $command ) 
        {
        	if ($command['cmd'] == $cmd)
        		return $itemtype;
        }
        return null;
    }

    /**
     * Returns the Classname for the given Itentype ID.
     * @return String the Classname for the Itemtype ID 
     */
    function getClassNameForItemType($itemtype)
    {
        return $GLOBALS['_BIGACE']['ITEMS'][$itemtype]['name'];
    }
    
    /**
     * Returns a new instance of the Class for the given Itentype ID.
     * @return Item a new instance (subclass of Item) for the given Itemtype ID   
     */
    function getClassForItemType($itemtype, $itemid, $treetype = ITEM_LOAD_FULL, $languageID = '')
    {
        return new $GLOBALS['_BIGACE']['ITEMS'][$itemtype]['name']($itemid, $treetype, $languageID);
    }
    
    /**
     * @access private
     */
    function getItemTypeIDforName($name)
    {
        $id = '';
        for($i=1,$n=$this->countItemtypes(); $i<$n; $i++)
        {
            if ($GLOBALS['_BIGACE']['ITEMS'][$i]['name'] == $name) {
                $id = $i;
            }
        }
        return $id;
    }
    
    /**
     * @access private
     */
    function getItemTypeArray() {
        return $GLOBALS['_BIGACE']['ITEMS'];
    }
    
    /**
     * Returns how many Itemtypes are known by the System.
     *
      * @returns int the number of items known by the System
     */
    function countItemtypes() {
        return count($this->getItemTypeArray());
    }

}

?>