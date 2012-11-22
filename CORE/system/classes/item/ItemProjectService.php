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

// do we need that???
//define('PROJECT_DEFAULT_LANGUAGE', '0');

/**
 * Holds methods for receiving Project Values for Items.
 * The ItemProjectService MUST be intialized with an Itemtype!
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage item
 */
class ItemProjectService
{
    private $itemtype;

    /**
    * Initalizes a new ItemService for a special Itemtype.
    * @param int the Itemtype to get6 Project Values for
    */
    function ItemProjectService($itemtype)
    {
        $this->itemtype = $itemtype;
    }

    /**
     * Get the Itemtype the Serviec is initialized for. 
     * @access private
     */
    function getItemtype() 
    {
        return $this->itemtype;
    }
    
    /**
     * Get all Project Text Values for ItemID and LanguageID.
     * 
     * @param int the ItemID to get Project Text Values for
     * @param int the LanguageID to get Project Text Values for
     */
    function getAllProjectText($itemid, $languageid) 
    {
        return $this->_getAllProjectValue('item_select_all_project_text', $itemid, $languageid);
    }

    /**
     * Get all Project Text values for ItemID and LanguageID as Array.
     * 
     * @param int the ItemID to get Project Text Values for
     * @param int the LanguageID to get Project Text Values for
     */
    function getAllText($itemid, $languageid) 
    {
        $temp = array();
        $meta_vals = $this->getAllProjectText($itemid, $languageid);
        for($i=0; $i < $meta_vals->count(); $i++) {
            $tt = $meta_vals->next();
            $temp[$tt['project_key']] = $tt['project_value'];
        }        
        return $temp; 
    }

    /**
     * Get all Project Numeric values for ItemID and LanguageID as Array.
     * 
     * @param int the ItemID to get Project Numeric values for
     * @param int the LanguageID to get Project Numeric values for
     */
    function getAllNum($itemid, $languageid) 
    {
        $temp = array();
        $meta_vals = $this->getAllProjectNum($itemid, $languageid);
        for($i=0; $i < $meta_vals->count(); $i++) {
            $tt = $meta_vals->next();
            $temp[$tt['project_key']] = $tt['project_value'];
        }        
        return $temp; 
    }
    
    /**
     * Get all Project Numeric Values for ItemID and LanguageID.
     * 
     * @param int the ItemID to get Project Numeric Values for
     * @param int the LanguageID to get Project Numeric Values for
     */
    function getAllProjectNum($itemid, $languageid) 
    {
        return $this->_getAllProjectValue('item_select_all_project_num', $itemid, $languageid);
    }

    /**
     * Get a Project Text Value for ItemID and LanguageID for the specified Key. 
     * 
     * @param int the ItemID to get Project Text Values for
     * @param int the LanguageID to get Project Text Values for
     * @param String the key specifying your Project Text Value
     */
    function getProjectText($itemid, $languageid, $key, $default = "") 
    {
        return $this->_getProjectValue('item_select_project_text', $itemid, $languageid, $key, $default);
    }

    /**
     * Get a Project Numeric Value for ItemID and LanguageID for the specified Key. 
     * 
     * @param int the ItemID to get Project Numeric Values for
     * @param int the LanguageID to get Project Numeric Values for
     * @param String the key specifying your Project Text Value
     */
    function getProjectNum($itemid, $languageid, $key, $default = "") 
    {
        return $this->_getProjectValue('item_select_project_num', $itemid, $languageid, $key, $default);
    }

    /**
     * Checks whether the Project Numeric Value for ItemID and LanguageID and specified Key exists. 
     * 
     * @param int the ItemID to get Project Numeric Values for
     * @param int the LanguageID to get Project Numeric Values for
     * @param String the key specifying your Project Text Value
     */
    function existsProjectNum($itemid, $languageid, $key) 
    {
        return $this->_existsProjectValue('item_select_project_num', $itemid, $languageid, $key);
    }

    /**
     * Checks whether the Project Text Value for ItemID and LanguageID and specified Key exists. 
     * 
     * @param int the ItemID to get Project Text Values for
     * @param int the LanguageID to get Project Text Values for
     * @param String the key specifying your Project Text Value
     */
    function existsProjectText($itemid, $languageid, $key)
    { 
        return $this->_existsProjectValue('item_select_project_text', $itemid, $languageid, $key);
    }

    /**
     * Helper method to get all Project values for a specified SQL.
     * @access private
     */
    private function _getAllProjectValue($sqlFile, $itemid, $languageid) 
    {
        $values = array ( 'ITEMTYPE'    => $this->getItemType(), 
                          'ITEM_ID'     => $itemid,
                          'LANGUAGE_ID' => $languageid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement($sqlFile);
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
     * Helper method to get a Project value for a specified SQL.
     * @access private
     */
    private function _getProjectValue($sqlFile, $itemid, $languageid, $key, $default = "") 
    {
        $values = array ( 'ITEMTYPE'      => $this->getItemType(), 
                          'ITEM_ID'       => $itemid,
                          'LANGUAGE_ID'   => $languageid, 
                          'KEY'           => $key );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement($sqlFile);
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        
        if ($temp->isError() || $temp->count() == 0)
            return $default;
        
        $temp = $temp->next();
        return $temp['project_value']; 
    }

    /**
     * Private helper method to check if a Project value exists for a specified SQL.
     * @access private
     */
    private function _existsProjectValue($sqlFile, $itemid, $languageid, $key) 
    {
        $values = array ( 'ITEMTYPE'      => $this->getItemType(), 
                          'ITEM_ID'       => $itemid,
                          'LANGUAGE_ID'   => $languageid, 
                          'KEY'           => $key );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement($sqlFile);
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        if ($temp->isError() || $temp->count() == 0)
            return FALSE;
        return true; 
    }    
    
    /**
     * Helper function to get a boolean (numeric) value from the meta fields.
     * Supply its name and the default value 
     * @param $id the item id
     * @param $language the item language
     * @param $name the values name 
     * @param $default the default value to return if the key is not set
     * @return boolean the value
     */
    function getBool($id, $language, $name, $default = false) {
    	if($this->existsProjectNum($id, $language, $name))
    		return (bool)$this->getProjectNum($id, $language, $name);
    	return $default;
    }
}
