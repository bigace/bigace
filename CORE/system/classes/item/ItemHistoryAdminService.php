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

import('classes.item.ItemService');
import('classes.item.Itemtype');
import('classes.item.ItemHistoryService');
import('classes.language.ItemLanguageEnumeration');


/**
 * The ItemHistoryAdminService provides services for HistoryItems.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage item
 */
class ItemHistoryAdminService extends ItemHistoryService
{

    function ItemHistoryAdminService($itemtype)
    {
        $this->initItemHistoryService($itemtype);
    }

    // ---------------------------------------------------------------------------------------------
    // ------------------------------------- [HISTORY METHODS] -------------------------------------
    // ---------------------------------------------------------------------------------------------
    
    /**
    * Deletes all old Versions of this Item
    */
    function deleteAllHistoryVersionsAllLanguages($id) 
    {
        $ilenum = new ItemLanguageEnumeration($this->getItemtypeID(), $id);

        // loop above existing languages
        for($i=0; $i < $ilenum->count(); $i++) {
            $lang = $ilenum->next();
            $this->deleteAllHistoryVersions($id, $lang->getID());
        }
        
    }
    
    /**
    * Deletes all old Versions of this Item
    */
    function deleteAllHistoryVersions($id, $langid) 
    {
        $ihs = new ItemHistoryService($this->getItemtypeID());
        $all = $ihs->getHistoryVersions($id, $langid);
        
        $GLOBALS['LOGGER']->logDebug('Deleting all History Versions for Item ('.$id.', '.$langid.') ...');
        
        for ($i=0; $i < $all->count(); $i++) {
            $temp = $all->next();
            $this->deleteHistoryVersion($temp->getID(), $temp->getLanguageID(), $temp->getLastDate());
        }
    }

    /**
    * Deletes a special History Version of this Item
    */
    function deleteHistoryVersion($id,$langid,$modifieddate) 
    {
    	$service = new ItemService($this->getItemtypeID());
        $temp = $this->getHistoryVersion($id,$langid,$modifieddate);
        $item = $service->getItem($id, ITEM_LOAD_FULL, $langid);
        if ($temp->getFullURL() != $item->getFullURL()) {
            if ( file_exists($temp->getFullURL()) && is_file($temp->getFullURL()) ) {
                unlink($temp->getFullURL());
                $GLOBALS['LOGGER']->logDebug('Deleted History version file: ' . $temp->getFullURL()); 
            }
        }
        $values = $this->_prepareSqlValues($id, $langid);
        $values['MODIFIED_DATE'] = $modifieddate;
        
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_history_delete');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
	    
	    return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    private function _prepareSqlValues($id, $langid)
    {
	    return array('ITEM_ID'     => $id,
                     'ITEMTYPE'    => $this->getItemtypeID(),
                     'LANGUAGE_ID' => $langid);
    }
}
