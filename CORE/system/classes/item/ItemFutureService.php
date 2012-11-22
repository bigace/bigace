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
 * @subpackage item
 */

loadClass('item', 'Itemtype');
loadClass('item', 'ItemHistoryEnumeration');
loadClass('item', 'FutureItem');
loadClass('item', 'ItemHelper');
loadClass('item', 'ItemAdminService');
loadClass('menu', 'MenuAdminService');
loadClass('language', 'ItemFutureLanguageEnumeration');

/**
 * Holds methods for working with Future Versions of Items.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage item
 */
class ItemFutureService extends Itemtype
{
    /**
     * @access private
     */
    var $itemService = NULL;

    /**
     * Initalizes a new ItemFutureService for a special Itemtype.
     */
    function ItemFutureService($itemtype)
    {
        $this->initItemtype($itemtype);
    }

    /**
     * @access private
     */
    function getItemService() 
    {
        if ($this->itemService == NULL) {
            $this->itemService = new ItemService($this->getItemtypeID());
        }
        return $this->itemService;
    }
    
    /**
     * This creates the new File for the Future Content and updates
     * the current Database Future Version to set the Filename.
     * 
     * @return String the new File name
     * @access private
     */
    function _createContentFutureVersion($id, $langid) 
    {
        $service = $this->getItemService();
        $temp = $service->getItem($id, ITEM_LOAD_FULL, $langid);
        $futureName = ItemHelper::getFutureURLFull($temp);

        if(file_exists($temp->getFullURL()) && is_file($temp->getFullURL())) 
        {
            if (copy($temp->getFullURL(), $futureName)) 
            {
                $GLOBALS['LOGGER']->logInfo('Created future version with Filename "' . $futureName . '"');
                $values = array('text_1' => ItemHelper::getFutureURL($temp));
                $res = $this->updateDatabaseFutureVersion($id, $langid, $values);
                if (!$res->isError())
                    $GLOBALS['LOGGER']->logInfo('Updated future version, added new Filename...');
                else
                    $GLOBALS['LOGGER']->log(E_USER_ERROR, 'Could not update future version to set Filename.');
            } 
            else 
            {
                $GLOBALS['LOGGER']->log(E_USER_ERROR, 'Could not create future version with Filename "' . $futureName . '"');
            }
        }
        
        return $futureName;
    }

    /**
     * Updates the values of a Future Version.
     * @access private
     */
    function updateDatabaseFutureVersion($id, $langid, $values) 
    {
        $values = ItemHelper::prepareSqlValues($id, $langid, $values);
        $values['ITEMTYPE'] = $this->getItemtypeID();
        
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_future_update');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logSQL('Updated Future Version with Statement: ' . $sqlString);
        
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    // ---------------------------------------------------------------------------

    /**
     * Receives the Future Version of an Item.
     * @return FutureItem the Future Item of the requested Item 
     */
    function getFutureVersion($itemid, $languageid)
    {
        $values = array ( 'CID'             => _CID_, 
                          'ITEM_ID'         => $itemid,
                          'ITEMTYPE'        => $this->getItemtypeID(),  
                          'LANGUAGE_ID'     => $languageid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_future_select');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logSQL('Select Future Version with Statement: ' . $sqlString);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $hi = new FutureItem($res->next());
        $hi->initItemtype($this->getItemtypeID());
        return $hi;
    }

    
    /**
     * Returns if a Item has a Future Version.
     */
    function hasFutureVersion($id, $languageid)
    {
        $values = array( 'ITEMTYPE'    => $this->getItemtypeID(),
                         'ITEM_ID'     => $id,
                         'LANGUAGE_ID' => $languageid,
                         'CID'         => _CID_ );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_has_future_version');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logSQL('Check if Item has Future Version with Statement: ' . $sqlString);
        $version = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $version = $version->next();
        return ($version['amount'] > 0);
    }
    
    /**
     * Creates a Future Version of a Item, including a File and Database Version.
     */
    function createFutureVersion($itemid, $languageid, $userid = '')
    {
        if ($userid == '')
            $userid = $GLOBALS['_BIGACE']['SESSION']->getUserID();
        $values = array ( 'CID'             => _CID_, 
                          'ITEM_ID'         => $itemid,
                          'ITEMTYPE'        => $this->getItemtypeID(),  
                          'LANGUAGE_ID'     => $languageid,
                          'ACTIVITY'        => '',
                          'INITIATOR'       => $userid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_future_create');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logSQL('Create Future Version with Statement: ' . $sqlString);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        
        if (!$res->isError())
        {
            return $this->_createContentFutureVersion($itemid, $languageid);
        }
        else
        {
            $GLOBALS['LOGGER']->logError('Could not create Future Version!');
        }
    }
    
    /**
     * Updates the Future Content of an Item.
     * If the Item currently has no Future Content, it will be created!
     * 
     * @return boolean whether the update could be performed or not
     */
    function updateFutureContent($itemid, $languageid, $content)
    {
        
        // fallback check to make sure we have a valid future version
        if (!$this->hasFutureVersion($itemid, $languageid))
        {
            $this->createFutureVersion($itemid, $languageid);
        }
        
        $temp = $this->getFutureVersion($itemid, $languageid);
        if (ItemHelper::checkFile($temp->getFullURL()))
        {
            if (ItemHelper::saveContent($temp->getFullURL(), $content)) {
                $GLOBALS['LOGGER']->logInfo("Saved Future Content in File: '".$temp->getFullURL()."'");
                $this->updateDatabaseFutureVersion($itemid, $languageid, array());
                return TRUE;
            }
            else
            {
                $GLOBALS['LOGGER']->logError('Could not write Future Content for File ('.$temp->getFullURL().')!');
            }
        } else {
        	$GLOBALS['LOGGER']->logError('Check your access rights, File ('.$temp->getFullURL().') is not writeable!');
        }
        return FALSE;
    }
    
    /**
     * Publish the current Future Version and deletes it afterwards.
     * @return boolean the Result of this call
     */
    function publishFutureContent($itemid, $languageid)
    {
        $temp = $this->getFutureVersion($itemid, $languageid);
        if($this->getItemtypeID() == _BIGACE_ITEM_MENU)
            $ias = new MenuAdminService();
        else
            $ias = new ItemAdminService($this->getItemtypeID());
        if (!$ias->updateItemContent($itemid, $languageid, $temp->getContent())) {
            $GLOBALS['LOGGER']->logError("Could not publish Future Version for ItemID: " . $itemid . " and Language: " . $languageid);
        } else {
            $GLOBALS['LOGGER']->logDebug("Published Future Version for ItemID: " . $itemid . " and Language: " . $languageid);
            return $this->deleteFutureVersion($itemid, $languageid);
        }
        
        // did not work
        return FALSE;
    }
    
    /**
     * Delete the specified Future Language Version.
     * This returns true on success or when there is simply no language version existing.
     * @return boolean whether the call was successfull or not
     */
    function deleteFutureVersion($itemid, $languageid)
    {
        if ($this->hasFutureVersion($itemid, $languageid))
        {
            $temp = $this->getFutureVersion($itemid, $languageid);
            // ------------------------ delete the content file ------------------------
            if ( file_exists($temp->getFullURL()) && is_file($temp->getFullURL()) )
            {
                if (!unlink($temp->getFullURL()))
                {
                    $GLOBALS['LOGGER']->logError('Could not delete Future Language Versions Content File: ' . $temp->getFullURL());
                    // do not delete database entry, cause file might not have been removed
                    return FALSE;
                }
            }
            
            // ------------------------ delete the future rights ------------------------
            // if there are more than one language future version keep the rights
            $langs = $this->getFutureLanguagesForItem($itemid);
            if ($langs->count() <= 1) {
                // otherwise delete them
                $this->deleteFutureRights($itemid);
            }
    
            // ------------------------ delete the database entry ------------------------
            $values = array ( 'CID'             => _CID_, 
                              'ITEM_ID'         => $itemid,
                              'ITEMTYPE'        => $this->getItemtypeID(),  
                              'LANGUAGE_ID'     => $languageid );
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_future_delete_language');
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
            $GLOBALS['LOGGER']->logSQL('Delete Future Language Version with Statement: ' . $sqlString);
            $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
            if ($res->isError()) {
                $GLOBALS['LOGGER']->logError('Could not delete Future Language Version.');
                return false;
            }
        }
        return true;
    }

    /**
     * Delete all Future Language Version for the given Item.
     * @return boolean whether the call was successfull or not
     */
    function deleteFutureVersions($itemid)
    {
        $result = TRUE;
        $enum = $this->getFutureLanguagesForItem($itemid);
        for($i=0; $i < $enum->count(); $i++)
        {
            $lang = $enum->next();
            $locale = $lang->getLocale();
            $res = $this->deleteFutureVersion($itemid, $locale);
            if (!$res)
                $result = FALSE;
        }
        return $result;
    }
    
    /**
     * Returns an Enumeration of all Languages, the specified Item has Future Versions of.
     * @return ItemFutureLanguageEnumeration all Future Languages for this Item
     */
    function getFutureLanguagesForItem($itemid)
    {
        return new ItemFutureLanguageEnumeration($this->getItemtypeID(), $itemid);
    }
    
    
    /**
     * Set access rights for a single User.
     */    
    function setFutureRights($itemid, $userid, $rightValue)
    {
        $values = array ( 'CID'         => _CID_, 
                          'ITEM_ID'     => $itemid,
                          'ITEMTYPE'    => $this->getItemtypeID(),
                          'RIGHT_VALUE' => $rightValue,  
                          'USER_ID'     => $userid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('right_future_create');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logSQL('Create Right for Future Version with Statement: ' . $sqlString);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sqlString);
    }

    /**
     * Delete all exisiting User access rights for an Item.
     * @param long itemid the ItemID to delete rights for
     */ 
    function deleteFutureRights($itemid)
    {
        $values = array ( 'CID'         => _CID_, 
                          'ITEM_ID'     => $itemid,
                          'ITEMTYPE'    => $this->getItemtypeID() );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('right_future_delete');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logSQL('Delete Right for Future Version with Statement: ' . $sqlString);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }
    
}

?>