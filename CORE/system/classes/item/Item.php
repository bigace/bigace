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

import('classes.item.Itemtype');

/**
 * This is the Super Class for all Items!
 * <br>
 * Changes in here will be available in all implementing Items.
 * <br><br>
 * Currently used Text/Num/Date fields:
 * <br>
 * <b>Item:</b><br>
 * <code>
 * getItemText('1') = getURL()
 * getItemText('2') = getOriginalName()
 * getItemNum('3')  = getFlag()
 * getItemNum('4')  = getPosition()
 * </code>
 *
 * <b>News:</b><br>
 * <code>
 * getItemNum('1') = getImageID()
 * getItemDate('2') = getDate()
 * </code>
 * 
 * <b>Menu:</b><br>
 * <code>
 * getItemText('3') = getModulID()
 * getItemText('4') = getLayoutName()
 * getItemText('5') = (cleaned up) SearchEngine content
 * </code>
 *
 * <b>FutureItem:</b><br>
 * <code>
 * getItemNum('5')  = getAssignedUser()
 * </code>
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage item
 */
class Item extends Itemtype
{
	private $classitem;
	private $childcount = null; 

	// if we do not know (see _setItemValues()) the treetype, it must be light to be able to perform a lazy loading
	private $requested = array('treetype' => ITEM_LOAD_LIGHT);  

	/**
	 * a cache for the function hasChildren and getChilren
	 * @access private
	 */
	private $childItemCache = null;
	private $childItemCacheTreetype = ITEM_LOAD_FULL;
	
	/**
	 * Full construtor for fetching an specified Item from the Database.
	 * You should probably use an concrete implementation of this class instead!?!
	 */
	function Item($itemtype,$id,$treetype = ITEM_LOAD_FULL,$languageID='') {
	    $this->init($itemtype,$id,$treetype,$languageID);
	}
	
	/**
     * Performs the Database Load.
     * @access private
	 */
    function init($itemtype,$id,$treetype = ITEM_LOAD_FULL,$languageID='') 
    {
        // remember if it might be required to perform a lazy loading
        $this->requested = array('treetype' => $treetype, 'language' => $languageID);

        $this->initItemtype($itemtype);
        $values = array('ITEMTYPE'    => $itemtype,
                        'ITEM_ID'     => $id,
                        'CID'         => _CID_,
                        'LANGUAGE_ID' => $languageID,
                        'COLUMNS'     => MasterItemType::getSelectColumns($itemtype,$treetype) );
        if ($languageID == '') {
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_select');
        } else {
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_select_language');
        }
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

        $this->_setItemValues( $temp->next() );
    }

    /**
     * @access private
     */
    function _setItemValues($array) {
        $this->classitem = $array;
        $this->requested['language'] = $this->getLanguageID();
    }

    /**
     * @access private
     */
    function _getItemValues() {
        return $this->classitem;
    }

    /**
     * This methods tries to fetch the Column from the Item result.
     * If this column could not be found it can perform a lazy loading
     * with ALL Columns.
     * A Log Message will then be generated to make sure developer will find this problem! 
     * 
     * @param String the requested Column to fetch value for
     * @param boolean if a lazy loading should be tried to fetch the column if not found
     * @return mixed the Column value or NULL
     * @access private
     */
    function getColumnValue($columnName, $tryLazyLoading = true) {
        if (isset($this->classitem[$columnName])) {
            return $this->classitem[$columnName];
        }

        if(is_array($this->classitem) && !array_key_exists($columnName, $this->classitem))
        {
            if($tryLazyLoading) {
                $this->loadLazy('Could not find Column "'.$columnName.'"!');
                return $this->getColumnValue($columnName, false);
            }
            $errMsg = 'Not able to read requested Column (' . $columnName .')';
            if(is_array($this->classitem) && isset($this->classitem['id']))
                $errMsg .= ' in Item: ' .  $this->classitem['id'];
            $GLOBALS['LOGGER']->logError($errMsg);
        }
        return NULL;
    }

    /**
     * Returns the Itemtype ID.
     * @return int the Itemtype ID
     */
    function getItemType() {
        return $this->getItemtypeID();
    }

    /**
     * Returns the Item ID.
     * @return int the Item ID
     */
    function getID() {
            return $this->getColumnValue("id");
    }

    /**
     * Returns the Items Mimetype.
     * @return String the Mimetype
     */
    function getMimetype() {
        return $this->getColumnValue("mimetype");
    }

    /**
     * Fetch the Items Name.
     * @return String the Items Name
     */
    function getName() {
        return $this->getColumnValue("name");
    }

    /**
     * Returns the Item Description.
     * @return String the Items description
     */
    function getDescription() {
        return $this->getColumnValue("description");
    }

    /**
     * Returns the Item Catchwords. 
     * Catchwords are a small (up to 255 Character) text value that are used within the Search!
     * @return String the Items Cachtwords
     */
    function getCatchwords() {
        return $this->getColumnValue("catchwords");
    }

    /**
     * Returns the desired ItemDate field.
     * @access protected
     */
    function getItemDate($id) {
        return $this->getColumnValue("date_".$id);
    }

    /**
     * Returns the desired ItemNum field.
     * @access protected
     */
    function getItemNum($id) {
		$i = $this->getColumnValue("num_".$id);
		if(!is_null($i))
	        return (int)$i;
		return null;
    }

    /**
     * Returns the desired ItemText field.
     * @access protected
     */
    function getItemText($id) {
        return $this->getColumnValue("text_".$id);
    }

    /**
     * Returns the Language ID of this Item.
     * @return int the Items language ID
     */
    function getLanguageID() {
        return $this->getColumnValue("language");
    }

    /**
     * Returns the Parents Item ID.
     * @return int the ID of the Parent Item
     */
    function getParentID() {
        return $this->getColumnValue("parentid");
    }

    /**
     * Returns the User ID this Item was created by.
     * @return int the User ID of the Principal who created this Item
     */
    function getCreateByID() {
        return $this->getColumnValue("createby");
    }

    /**
     * Returns the Timestamp, when the Item was created.
     * @return int the creation timestamp
     */
    function getCreateDate() {
    	return $this->getColumnValue("createdate");
    }

    /**
     * Returns the timestamp of the last changes on this Item (like Description or Content).
     * @return int the timestamp of last changes
     */
    function getLastDate() {
    	return $this->getColumnValue("modifieddate");
    }
    
    /**
     * Returns the ID of the last User that updated this Item.
     * @return int the User ID of the last user
     */
    function getLastByID() {
    	return $this->getColumnValue("modifiedby");
    }

    /**
     * Returns the Position of this Item.
     * The Position should be unique in this Tree.
     * @return int the Position
     */
    function getPosition() {
    	//FIXME: use constant value instead
        return $this->getItemNum('4');
    }
    /**
     * Returns the Flag of this Item. Could be one of FLAG_HIDDEN, FLAG_TRASH or FLAG_NORMAL
     * @access private
     * @return int the Flag
     */
    function getFlag() {
    	//FIXME: use constant value instead
        return $this->getItemNum('3');
    }

    /**
     * returns whether the this items flag is set to hidden or not.
     * See FLAG_HIDDEN.
     * @return boolean indicating whether this Item is hidden or not
     */
    function isHidden() {
        return ($this->getFlag() == FLAG_HIDDEN);
    }

    /**
     * returns whether this items flag set to trashed or not.
     * See FLAG_TRASH.
     * @return boolean indicating whether this Item is trashed or not
     */
    function isInTrash() {
        return ($this->getFlag() == FLAG_TRASH);
    }

    /**
     * Returns the URL where this Items Content is stored.
    * @return String the Items file name
    */
	function getURL() {
		//FIXME: use constant value instead
		return $this->getItemText('1');
	}

    /**
     * Returns the name of the configured Workflow.
     * @return String the Workflow Name
     */
    function getWorkflowName() {
    	return $this->getColumnValue("workflow");
    }

    /**
     * Returns the absolute Path to the Items Content File.
     * @return String the Items full name including directory
     */
    function getFullURL() {
        return $this->getDirectory().$this->getURL();
    }

    /**
     * Returns the original File Name. This MUST only work with uploaded Files.
     * Otherwise it depends on the User entrys.
     * @return String the Original Item name
     */
    function getOriginalName() {
        return $this->getItemText('2');
    }

    /**
     * Returns the unique name, which is only a backup of the unique_name table for fast access.
     * @return String the unique name to be used in short URLs
     */
    function getUniqueName() {
        return $this->getColumnValue("unique_name");
    }

    /**
     * Returns the last modified date of the File (filemtime). 
     * Unlike <code>Item->getLastDate()</code> this only returns the last changes on the Items Content!
     * @return int the last modified timestamp
     */
    function lastModified()
    {
        if ( file_exists($this->getFullURL()) ) {
        	return filemtime($this->getFullURL());
        } else {
            return time();
        }
    }

    /**
     * Checks if the Item has children in the same Language as the item.
     * @return boolean returns whether this Item has children or not
     */
    function hasChildren() {
        return ($this->countChildren() > 0);
    }
    /**
     * Checks if the Item has children in the same Language as the item.
     * @deprecated use hasChildren instead
     * @return boolean returns whether this Item has children or not
     */
    function hasChilds() {
    	$GLOBALS['LOGGER']->logDebug("deprecated mehtod called: hasChilds(), use hasChildren() instead");
    	return $this->hasChildren();
    }

    /**
     * Returns the Timestamp, from when this Item will be valid (and therefor visible).
     * @return long the Timestamp from when the Item will be valid
     */
    function getValidFrom() {
        return $this->getCreateDate();
    }

    /**
     * Returns the Timestamp, until this Item is valid (and therefor visible).
     * @return long the Timestamp till when the Item will be valid
     */
    function getValidTo() {
        return mktime(0, 0, 0, 12, 31, 2037);
    }
    
    /**
     * Returns the amount of views for this Item.
     * @return int the number of views
     */
    function getViews() {
        return $this->getColumnValue("viewed");
    }

    /**
    * Returns the Size of the linked File.
    * @return long the Filesize
    */
    function getSize()
    {
        if ( file_exists($this->getFullURL()) ) {
            return filesize($this->getFullURL());
        } else {
            return 0;
        }
    }

    /**
     * Fetches the Content from the linked File.
     * @return mixed the Content (might be BINARY, HTML or simply empty)
     */
    function getContent() 
    {
        $content = '';
        $size = $this->getSize();
        if ($size > 0) 
        {
            $fp = fopen($this->getFullURL(), "rb");
            $content = fread ($fp, $size);
            fclose($fp);
        }
        return $content;
    }

    /**
     * Returns all children of this item that are available in the same language as the item.
     * @deprecated use getChildren instead
     * @param String treetype the TreeType to use when fetching the Children
     * @return ItemTreeWalker the Children for this Item, in the Items Language
     */
    function getChilds($treetype = null) 
    {
    	$GLOBALS['LOGGER']->logDebug("deprecated mehtod called: getChilds(), use getChildren() instead");
        return $this->getChildren($treetype);
    }

    /**
     * Returns all children of this item that are available in the same language as the item.
     * @param String treetype the TreeType to use when fetching the Children
     * @return ItemTreeWalker the childdren for this Item, in the Items Language
     */
    function getChildren($treetype = null) 
    {
    	if(is_null($treetype)) {
    		$treetype = $this->requested['treetype'];
    	}
    	
     	if(is_null($this->childItemCache) || $treetype != $this->childItemCacheTreetype){
     		$this->childItemCacheTreetype = $treetype;
			$this->childItemCache = new ItemTreeWalker($this->getItemtype(), $this->getID(), ORDER_COLUMN_POSITION, $treetype, $this->getLanguageID());
		}
		
		return $this->childItemCache;
    }

    /**
     * Returns the number of children for this Item, language dependend!
     * @deprecated use countChildren instead
     * @access private
     */
    function countChilds() 
    {
    	$GLOBALS['LOGGER']->logDebug("deprecated method called: countChilds(), use countChildren() instead");
    	return $this->countChildren();
    }

    /**
     * Returns the number of children for this Item, language dependend!
     * @access private
     */
    function countChildren() 
    {
        if (is_null($this->childcount)) {
            $temp = $this->getChildren($this->requested['treetype']);
            $this->childcount = $temp->count();
        }
        return $this->childcount;
    }

    /**
     * Checks whether this Item exists or not.
     * @return boolean true if the Item exists, FALSE if it is corrupt or not existing
     */
    function exists() 
    {
    	//FIXME add a better check! 
        if ($this->classitem) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Perform a lazy loading if this Item was NOT already loaded fully.
     * Generates log messages!
     * @access private
     */
    function loadLazy($msg = 'unknown reason') 
    {
    	if (!isset($this->requested['treetype']) || $this->requested['treetype'] != ITEM_LOAD_FULL) {
    		$lang = (isset($this->requested['language']) ? $this->requested['language'] : '');
	        $GLOBALS['LOGGER']->log(E_USER_WARNING, 'Performing lazy loading. Message: ' . $msg . '. Item: ' . $this->toString());
    		$this->init($this->getItemTypeID(),$this->getID(), ITEM_LOAD_FULL, $lang);
    	} 
    	else 
    	{
	        $GLOBALS['LOGGER']->logInfo('Lazy loading was not performed, Item already loaded FULL. Item: ' . $this->toString());
    	}
    }
    
    /**
     * Simple toString() implementation to make debugging easier.
     * @return String a String representation of this Item, naming all important values 
     */
    function toString() {
    	return 'ID ' . $this->getID() . ', Type '.$this->getItemType().', Language ' . $this->getLanguageID() . (isset($this->requested['treetype']) ? ', TreeType ' . $this->requested['treetype'] : '' );
    }
}

?>