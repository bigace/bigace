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
 * but WITHOUT ANY WARRANTY; without even getToolLinksForItemthe implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation, 
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.administration
 */

/**
 * This script is not runnable as Standalone !
 * 
 * Scripts that use this include have to define the following variables:
 * <code>
 * $_ITEMTYPE  = The Item Type int value
 * $_ADMIN     = The Administration Service
 * </code>
 * 
 * The following methods are required as well:
 * <code>
 * createAdminMask($itemtype) => this method must return an instance of ItemAdminMask
 * createEditDataMaskForm($data, $item) => displays an formular to edit all item data
 * createFileListing($data) => create a listing for the specified items
 * </code>
 * 
 * See the file "item_main_default.php" for an example implementation.
 * -------------------------------------------------------------------
 * Following MODEs are available:
 * 
 * '17'             = Change existing Categorys
 * '19'             = Upload File to update existing Item
 * _MODE_EDIT_ITEM  = 
 * 'changecategory' = 
 * 'showUserRights' = 
 * 'raisePosition'  = 
 * 'lowerPosition'  = 
 * 
 */

// we are truelly not able to run without these objects!
if(defined('_VALID_ADMINISTRATION') && !isset($_ITEMTYPE) || !isset($_ADMIN)) {
    die('NOT RUNNABLE ALONE! SET $_ITEMTYPE AND $_ADMIN');
}

import('classes.util.html.FormularHelper');
import('classes.administration.ItemAdminMask');
import('classes.category.Category');
import('classes.category.ItemCategoryEnumeration');
import('classes.right.RightAdminService');
import('classes.right.GroupRight');
import('classes.group.Group');
import('classes.group.GroupEnumeration');
import('classes.language.Language');
import('classes.language.LanguageEnumeration');
import('classes.item.Itemtype');
import('classes.item.ItemService');
import('classes.item.ItemAdminService');
import('classes.item.ItemHistoryAdminService');
import('classes.item.ItemHistoryService');
import('classes.exception.WrongArgumentException');
import('classes.exception.NoWriteRightException');
import('classes.util.IOHelper');
import('classes.seo.UniqueNameService');
import('classes.seo.UniqueNameAdminService');

// ----------------------------------------------------------
// variables required for proper work
if (!isset($_SERVICE)) {
    $_SERVICE = new ItemService($_ITEMTYPE);
}
$_RIGHT_ADMIN = new RightAdminService($_ITEMTYPE);
// ----------------------------------------------------------

/**
 * Handles incoming requests. Must be called manually!
 */
function propagateAdminMode($mode = null, $data = null, $showList = false)
{
	if(is_null($mode)) $mode = extractVar(_PARAM_ADMIN_MODE, _MODE_BROWSE_MENU);
	if(is_null($data)) $data = extractVar('data',array('id'=>_BIGACE_TOP_LEVEL));

	if ($mode == _MODE_EDIT_ITEM) {
		editItem( $data );
	} 
	else if ($mode == _MODE_SAVE_ITEM) {
		$temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $data['id'], $GLOBALS['_BIGACE']['SESSION']->getUserID());
		if ($temp_right->canWrite()) {
		    saveFileData( $data );
		    // Update children too if recursive is set
		    $temp_menu = $GLOBALS['_SERVICE']->getClass($data['id']);
		    if ($temp_menu->hasChildren() && (isset($data['recursive_catchwords']) || isset($data['recursive_description']) || isset($data['recursive_layout']))) {
		    	saveFileDataRecursive($data, $temp_menu);
		    }
		    unset($temp_menu);
		    editItem( $data );
		} 
		else {
		    ExceptionHandler::processAdminException( new NoWriteRightException('Missing permissions for updating Item') );
		}
	} 
	else if ($mode == _MODE_CREATE_RIGHT) {
		createNewRight( $data );
		showChangeRightMask( $data );
	} 
	else if ($mode == '2') {
		// the question if the item should really be deleted will be handled inside the function
		// we only check if the item was truelly deleted, because we have to change the id of the 
		// page to be displayed if it is not available any longer!
		if (isset($data['id'])) {
		    $actualItem = $GLOBALS['_SERVICE']->getItem($data['id']);
		    $parentid = $actualItem->getParentID();
		    $result = deleteFile( $data );
		    if ($result) {
		        $data['id'] = $parentid;
		        $showList = true;
		    }
		} else {
		    $showList = true;
		}
	} 
	else if ($mode == _MODE_DELETE_RIGHT) {
		deleteRight( $data );
		showChangeRightMask( $data );
	} 
	else if ($mode == _MODE_CHANGE_RIGHT) {
		changeRight( $data );
		showChangeRightMask( $data );
	} 
	else if ($mode == 'showUserRights') {
		showChangeRightMask( $data );
	} 
	else if ($mode == 'changecategory') {
		showChangeCategoryMask( $data );
	} 
	else if ($mode == '17') { 
		// Change existing Categorys
		changeCategorys( $data );
	} 
	else if ($mode == _MODE_UPDATE_WITH_UPLOAD) { 
		// Upload File to update Item
		processUpdateWithUploadesFile( $data, $_FILES['userfile'] );
		editItem( $data );
	} 
	else if ($mode == 'raisePosition') {
		// Move item upwards
		$temp = $GLOBALS['_ADMIN']->raisePosition($data['moveid'], $data['langid']);
		$showList = true;
	} 
	else if ($mode == 'lowerPosition') {
		// Move item downwards
		$temp = $GLOBALS['_ADMIN']->lowerPosition($data['moveid'], $data['langid']);
		$showList = true;
	}
	else if ($mode == _MODE_REFRESH_HISTORY_CONTENT) 
	{
		// Check submitted values for needed ones
		if ( isset($data['id']) && isset($data['version']) && isset($data['langid']) ) 
		{
		    $item = $GLOBALS['_SERVICE']->getClass($data['id']);
		    $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $data['id'], $GLOBALS['_BIGACE']['SESSION']->getUserID());
		    // If user is allowed to write menu, recall version
		    if ($temp_right->canWrite()) 
		    {
		        if ($GLOBALS['_ADMIN']->refreshHistoryVersionContent($data['id'], $data['langid'], $data['version'])) {
		            displayMessage('Replaced actual version of '.$item->getName().' (ID: '.$data['id'].', Language: '.$data['langid'].') with history version: ' . $data['version']);
		        } else {
		            displayError('Could NOT replace actual version of '.$item->getName().' (ID: '.$data['id'].', Language: '.$data['langid'].') with history version: ' . $data['version']);
		        }
		    }
		    editItem( $data );
		}
		else
		{
		    ExceptionHandler::processAdminException( new WrongArgumentException(400, 'Could not recover History Version. ItemID, LanguageID or Version missing!') );
		}
	} 
	else if($mode == _MODE_CREATE_LANGUAGE)
	{
		if (isset($data['id']) && isset($data['name']) && isset($data['langid']) && isset($data['copyLangID'])) 
		{
		    $temp_menu = $GLOBALS['_SERVICE']->getClass($data['id']);
		    $my_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $data['id'], $GLOBALS['_BIGACE']['SESSION']->getUserID());
		    if($my_right->canWrite()) {
		        $GLOBALS['_ADMIN']->createItemLanguageVersion($data['id'], $data['copyLangID'], $data);
		        editItem( $data );
		    } 
		    else {
		        ExceptionHandler::processAdminException( new NoWriteRightException('Missing permission to create a new language version!') );
		    }
		}
		else
		{
		    ExceptionHandler::processAdminException( new WrongArgumentException(400, 'Could not recover HistoryVersion. ItemID, LanguageID or Version missing!') );
		}
	}
	else if ($mode == _MODE_DELETE_LANGUAGE)
	{
		if (isset($data['id']) && isset($data['langidtodelete'])) 
		{
		    $my_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $data['id'], $GLOBALS['_BIGACE']['SESSION']->getUserID());
		    if($my_right->canWrite()) {
		        $val = $GLOBALS['_ADMIN']->deleteItemLanguage($data['id'],$data['langidtodelete']);
		        if ($val == -1) {
		            displayError('Removed Item!');
		            $showList = true;
		        } else {
		            if ($val != 1) {
		                displayError('Could not delete language version!');
		            }
		            editItem( $data );
		        }
		    }
		    else {
		        ExceptionHandler::processAdminException( new NoWriteRightException('No permission to delete language version') );
		    }
		}
		else
		{
		    ExceptionHandler::processAdminException( new WrongArgumentException(400, 'Could not delete language version. Missing Parameter: ItemID or LanguageID') );
		}
	}
	else if($mode == _MODE_MOVE_ITEM)
	{
		// Move Item to new Parent
		$temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $data['id'], $GLOBALS['_BIGACE']['SESSION']->getUserID());
		if ($temp_right->canWrite()) 
		{
		    if ( isset($data['parentid']) && $data['id'] != _BIGACE_TOP_LEVEL && $data['parentid'] != _BIGACE_TOP_PARENT)
		    {
		        $GLOBALS['_ADMIN']->moveItem($data['id'], $data['parentid']);
		        editItem( $data );
		    }
		} 
		else 
		{
		    ExceptionHandler::processAdminException( new NoWriteRightException('No permission to move item') );
		}
	}    
	else if ($mode == _MODE_DISPLAY_HISTORY)
	{
		createHistoryVersionMask($data);
	}
	else if ($mode == _MODE_DELETE_HISTORY_MENU) {
		
		if (isset($data['ids']) && is_array($data['ids']) && (count($data['ids']) > 0) ) {
		    deleteHistoricVersions($data, $data['ids']);
		} else {
		    // TODO translate
		    displayError('No history versions submitted to delete');
		}
		$showList = true;
	} 
	else if ($mode == _MODE_DOWNLOAD_AS_ZIP) 
	{
		$ids = extractVar('data', array());
		if (isset($data['ids']) && is_array($data['ids']) && (count($data['ids']) > 0) )
		{
		    $values = array('itemtype' => $GLOBALS['_ITEMTYPE']);
		    foreach($ids['ids'] AS $key) {
		        $values['data[ids]['.$key.']'] = $key;
		    }
		    displayMessage('<a href="'.createCommandLink('download', '0', $values, 'files.zip').'">'.getTranslation('download_link').'</a>');
		} else {
		    displayError('No Items to download supplied!');
		}
		$showList = true;
	} 
	else if ($mode == _MODE_DELETE_MULTIPLE) 
	{
		$showList = true;
		$kept = array();
		$ids = extractVar('data', array());
		if (isset($data['ids']) && is_array($data['ids']) && (count($data['ids']) > 0) )
		{
		    $values = array('itemtype' => $GLOBALS['_ITEMTYPE']);
            $counter = 0;
		    foreach($ids['ids'] AS $key) 
            {
                $result = false;
               	if ($key != _BIGACE_TOP_LEVEL) {
	            	// Delete Item
	                $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $key, $GLOBALS['_BIGACE']['SESSION']->getUserID());
	                if ($temp_right->canDelete()) {
	                    if($GLOBALS['_ITEMTYPE'] == _BIGACE_ITEM_MENU)
	                        $result = $GLOBALS['_ADMIN']->deleteItem($key, true);
	                    else 
	                        $result = $GLOBALS['_ADMIN']->deleteItem($key, false);
	                    $counter++; 
	                    unset($data['ids'][$key]);
	                } 
	                else {
	                    import('classes.exception.NoDeleteRightException');
	                    ExceptionHandler::processAdminException( new NoDeleteRightException('No sufficient permission to delete Item with ID:'.$key) );
	                }
                } 
                
                if(!$result) $kept['ids'][] = $key;
		    }
            // TODO: translate
            displayMessage( "Deleted ".$counter." items!" );

            if(count($kept) > 0) {
                showMultipleItemsMask($kept);
	            $showList = false;
		    }
		}
	} 
	else if ($mode == _MODE_PARENT_MULTIPLE) 
	{
		$ids = extractVar('data', array());
		if (isset($data['ids']) && is_array($data['ids']) && (count($data['ids']) > 0) && isset($_POST['parentID']))
		{
            $newID = $_POST['parentID']; // TODO sanitize and check
		    $values = array('itemtype' => $GLOBALS['_ITEMTYPE']);

		    foreach($ids['ids'] AS $key) 
            {
                // Delete Item
                $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $key, $GLOBALS['_BIGACE']['SESSION']->getUserID());
                if ($temp_right->canWrite()) 
                {
                    if (!$GLOBALS['_ADMIN']->setParent($key, $newID)) {
                        // TODO translate
                        displayError('Could not move Item: ' . $key . " to: " . $newID);
                    } 
                } 
                else 
                {
                    import('classes.exception.NoDeleteRightException');
                    ExceptionHandler::processAdminException( new NoDeleteRightException('No sufficient permission to delete Item with ID:'.$key) );
                }

		    }
            // TODO: translate
            displayMessage( "Moved ".count($ids['ids'])." items to new parent!" );
		}
		$showList = true;
	} 
	else if ($mode == _MODE_DELETE_HISTORY) 
	{
		if (isset($data['id']) && isset($data['langid']) && isset($data['modified']) && is_array($data['modified']) && (count($data['modified']) > 0) ) {
		    deleteHistoryVersions($data['id'], $data['langid'], $data['modified']);
		    createHistoryVersionMask($data);
		    $showList = false;
		} else {
		    $showList = true;
		}
	} 
	else if ($mode == _MODE_UPDATE_MULTIPLE) 
    {
        if(isset($data['ids']) && is_array($data['ids']) && count($data['ids']) > 0) {
            showMultipleItemsMask($data);
	        $showList = false;
		} else {
		    $showList = true;
		}
    }
    else if ($mode == 'defaultPermissions')
    {
        if($GLOBALS['_ITEMTYPE'] !== _BIGACE_ITEM_MENU && has_permission('admin_default_permission')) {
    		showChangeRightMask( array('id' => _BIGACE_TOP_LEVEL, 'langid' => _ULC_) );
	        $showList = false;
		} else {
		    $showList = true;
		}
    }
	else if ($mode == _MODE_SET_GROUP_PERM) 
	{
		    $showList = false;
	    
            if(isset($_POST['groupID']) && isset($data['permission'])) 
            {
                if($data['permission'] == _BIGACE_RIGHTS_READ || $data['permission'] == _BIGACE_RIGHTS_WRITE
                    || $data['permission'] == _BIGACE_RIGHTS_RW || $data['permission'] == _BIGACE_RIGHTS_DELETE
                    || $data['permission'] == _BIGACE_RIGHTS_RWD || $data['permission'] == _BIGACE_RIGHTS_NO 
                ) {
                    if(is_array($data['ids'])) 
                    {
                        foreach($data['ids'] as $permID) 
                        {
				            // TODO check if own permissions would be increased, what is not allowed. 
				            // use createNewRight() method, which checks that
                        	if(has_item_permission($GLOBALS['_ITEMTYPE'],$permID, 'w')) {
	                            if (!$GLOBALS['_RIGHT_ADMIN']->checkForExistence($_POST['groupID'],$permID))  {
	                                $GLOBALS['_RIGHT_ADMIN']->createGroupRight($_POST['groupID'],$permID,$data['permission']);
	                            } else {
	                                $GLOBALS['_RIGHT_ADMIN']->changeRight($_POST['groupID'],$permID,$data['permission']);
	                            }
	                        }
                        }
	                    $showList = true;
	                    displayMessage( getTranslation('saved_ok') );
                    }
                    else {
                        displayError('Missing Item IDs');
                    }
                }
            }

            if(!$showList) {
                showMultipleItemsMask($data);
            }
    }
	else 
	{
		// Any mode was submitted that does not exist!
		$showList = true;
	}


	if ($showList) 
	{
		if(!function_exists('createFileListing'))
		    include_once(dirname(__FILE__).'/item_listing.php');
		createFileListing($data);
	}

	unset ($showList);

}

// ----------------------------------------------------------
// From here all the scripts functions are declared
// ----------------------------------------------------------


    function getToolLinksForItem($item)
    {
        $html = array(  'name'      => $item->getName(), 
                        'mimetype'  => '',
                        'history'   => '',
                        'rights'    => '', 
                        'admin'     => '', 
                        'delete'    => '', 
                        'up'        => '', 
                        'down'      => '' );
        
        // only set for none menu items             
        if ($item->getItemtype() != _BIGACE_ITEM_MENU) {
            $mime = $GLOBALS['_BIGACE']['style']['class']->getMimetypeImageURL( getFileExtension($item->getOriginalName() ) );                  
            $html['mimetype'] = ($mime == NULL ? '' : $mime);
        }
        
        $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($item->getItemtypeID(), $item->getID(), $GLOBALS['_BIGACE']['SESSION']->getUserID());
    
        if ($temp_right->canRead()) 
        {
            $html['history'] = createNamedCheckBox('data[ids][]', $item->getID(), FALSE, FALSE);
            
            if ($temp_right->canWrite()) 
            {
                $html['rights'] = '<a href="'.createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => $item->getID(), 'data[langid]' => $item->getLanguageID(), 'mode' => 'showUserRights')).'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'rights.png" alt="'.getTranslation('edit').'" title="'.getTranslation('edit').'"></a>';
            }
            
            $previewLink = LinkHelper::getCMSLinkFromItem($item);
            $html['preview'] = '<a href="'.LinkHelper::getUrlFromCMSLink($previewLink).'" target="_blank"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'preview.png" alt="'.getTranslation('show').'" title="'.getTranslation('show').'"></a>';
                            
            $html['download'] = '<a href="'.createCommandLink('download', $item->getID(), array('itemtype' => $item->getItemType()),$item->getCommand().'.zip').'" target="_blank"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'download.png" alt="'.getTranslation('download').'" title="'.getTranslation('download').'"></a>';
    
            if ($temp_right->canWrite()) 
            {
                $ile = $GLOBALS['_SERVICE']->getItemLanguageEnumeration($item->getID()); 
                $lang = array();
                for ($i=0; $i < $ile->count(); $i++) 
                {
                    $tempLanguage = $ile->next();
                    array_push($lang, $tempLanguage->getID());
                    $html['admin'] .= '&nbsp;<a href="'.createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => $item->getID(), 'adminCharset' => $tempLanguage->getCharset(), 'data[langid]' => $tempLanguage->getID(), 'mode' => _MODE_EDIT_ITEM)).'"><img alt="'.$tempLanguage->getName().'" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'languages/'.$tempLanguage->getLocale() . '.gif" class="langFlag"></a>';
                    $html['admin_link'] = createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => $item->getID(), 'adminCharset' => $tempLanguage->getCharset(), 'data[langid]' => $tempLanguage->getID(), 'mode' => _MODE_EDIT_ITEM));
                }
        
                if ($item->getID() != _BIGACE_TOP_LEVEL)
                {
                    $allowTreeDelete = true;
                    
                    if($temp_right->canDelete()) {
                        if(!$GLOBALS['_SERVICE']->isLeaf($item->getID()) || $allowTreeDelete)
                            $html['delete'] = '<a href="'.createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => $item->getID(), 'mode' => '2')).'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'delete.png" border="0" alt="'.getTranslation('delete').'"></a>';
                    }
                
                    // Show change position arrows
                    $html['up']   = '<a href="'.createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => $item->getParentID(), 'data[moveid]' => $item->getID(),'data[langid]' => $item->getLanguageID(), 'mode' => 'raisePosition')).'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'up.png" border="0" class="rightSpacer"></a>';
                    $html['down'] = '<a href="'.createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => $item->getParentID(), 'data[moveid]' => $item->getID(), 'data[langid]' => $item->getLanguageID(), 'mode' => 'lowerPosition')).'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'down.png" border="0"></a>';
                }
            }
        }    
        
        return $html;
    }

    
    function processUpdateWithUploadesFile( $data, $file )
    {
    	if(isset($file['error']) && $file['error'] > 0)
    	{
    		if($file['error'] == UPLOAD_ERR_INI_SIZE)
            	displayError('Could not update: Filesize is too high');
    		else if($file['error'] == UPLOAD_ERR_FORM_SIZE)
            	displayError('Could not update: Formsize is too high');
    		else if($file['error'] == UPLOAD_ERR_PARTIAL)
            	displayError('Could not update: File upload is broken');
    		else if($file['error'] == UPLOAD_ERR_NO_FILE)
            	displayError('Could not update: No file submitted');
    		else if($file['error'] == UPLOAD_ERR_NO_TMP_DIR)
            	displayError('Could not update: No temp directory configured');
    		else if($file['error'] == UPLOAD_ERR_CANT_WRITE)
            	displayError('Could not update: Cannot write file');
    		else if($file['error'] == UPLOAD_ERR_EXTENSION)
            	displayError('Could not update: Extension is missing');
    		return false;	
    	}
    	
        if (!is_uploaded_file($file['tmp_name']))
        {
        	//TODO translate
            displayError('Could not update, there is no uploaded file at ' . $file['tmp_name']);
            return false;
        }
		    	
        if (!isset($data['id']) || !isset($data['langid']))
        {
            // TODO translate
            displayError('Could not update item - ITEM ID OR LANGUAGE SETTING WAS MISSING!');
            return false;
        }

        $result = $GLOBALS['_ADMIN']->updateItemWithUpload($data['id'], $file, $data['langid']);
            
        import('classes.core.FileCache');
        $fileCache = new FileCache();
        $fileCache->expireAllCacheFiles($GLOBALS['_ADMIN']->getItemTypeID(), $data['id']);
        unset($fileCache);
        
        if($result['result']) {
            displayMessage($result['message']);
        } else {
            displayError($result['message']);
        }
        return $result['result'];
    }
    
    /**
    * Change Item Categorys
    */
    function changeCategorys ( $data ) 
    {
        if ( isset($data['id']) && (isset($data['newcat']) || isset($data['delcat'])) ) {

            if ( isset($data['newcat']) && $data['newcat'] != _BIGACE_TOP_LEVEL) {
                // register new category to item
                $cat_service = new CategoryService();
                if (!$cat_service->isItemLinkedToCategory($GLOBALS['_ITEMTYPE'],$data['id'],  $data['newcat'])) {
                    $i = $GLOBALS['_ADMIN']->addCategoryLink( $data['id'], $data['newcat'] );
                    displayMessage( getTranslation('linked_category') );
                }
            }
            
            if ( isset($data['delcat']) )  {
                $GLOBALS['_ADMIN']->removeCategoryLink( $data['id'], $data['delcat'] );
                displayMessage(getTranslation('removed_category'));
            }
            
            showChangeCategoryMask ( $data );
            
        }
        else
        {
            ExceptionHandler::processAdminException( new WrongArgumentException(400, 'Could not change item categories. ItemID or CategoryID to change is missing.') );
        }
    }
    
    /**
     * Changes an existing right
     */
    function changeRight($data)
    {
        if (isset($data['id']) && isset($data['group'])  && isset($data['rights']) )
        {
            $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $data['id'], $GLOBALS['_BIGACE']['SESSION']->getUserID());
            $allowed = $temp_right->canWrite();

            if($allowed) {
                if($data['rights'] >= _BIGACE_RIGHTS_DELETE) {
                    $allowed = $temp_right->canDelete();                
                }
            }

            if ($allowed)  {
                $temp_result = $GLOBALS['_RIGHT_ADMIN']->changeRight($data['group'], $data['id'], $data['rights']);
            } 
            else  {
                ExceptionHandler::processAdminException( new NoWriteRightException('No permission to change Item permissions') );
            }
        }
        else
        {
            ExceptionHandler::processAdminException( new WrongArgumentException(400, 'Missing parameter to change item permission') );
        }
    }

    /**
     * Delete a single right entry.
     */
    function deleteRight($data)
    {
        if (isset($data['id']) && isset($data['group']))
        {
            $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $data['id'], $GLOBALS['_BIGACE']['SESSION']->getUserID());
            if ($temp_right->canWrite()) {
                $GLOBALS['_RIGHT_ADMIN']->deleteGroupRight($data['group'], $data['id']);
            } else {
                import('classes.exception.NoDeleteRightException');
                ExceptionHandler::processAdminException( new NoDeleteRightException('No sufficient permission to delete item permission.') );
            }
        } else {
            ExceptionHandler::processAdminException( new WrongArgumentException(400, 'Could not delete Item Group Right, ItemID or GroupID missing!') );
        }
    }

    /**
     * Deletes a full Item
     */
    function deleteFile($data) 
    {
        $result = false;
        if (isset($data['id']))
        {
            $actualItem = $GLOBALS['_SERVICE']->getItem($data['id']);

            if ( !isset($data['confirm']) ) 
            {
                $cont = array();
                $cont[getTranslation('name')]  = $actualItem->getName();
                
                // Ask if Item should be deleted
                $config = array(
                            'size'          => array('left' => '200px'),
                            'align'         => array('right' => 'left'),
                            'title'         => getTranslation('confirm_delete_item'),
                            'image'         => $GLOBALS['_BIGACE']['style']['DIR'].'delete.png',
                            'form_reset'    => 'location.href='."'".createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => $actualItem->getParentID()))."'",
                            'form_action'   => createAdminLink($GLOBALS['MENU']->getID()),
                            'form_method'   => 'post',
                            'form_hidden'   => array(
                                                    _PARAM_ADMIN_MODE              =>  '2',
                                                    'data[id]'          =>  $actualItem->getID(),
                                                    'data[confirm]'     =>  $actualItem->getID()
                                            ),
                            'entries'       => $cont,
                            'form_submit'   => true,
                            'submit_label'  => getTranslation('delete'),
                            'reset_label'   => getTranslation('back')
                );
                // TODO set image and file parent to top level if their parent is deleted!
                if ($GLOBALS['_ITEMTYPE'] == _BIGACE_ITEM_MENU && !$GLOBALS['_SERVICE']->isLeaf($actualItem->getID())) {
                    displayMessage( getTranslation('confirm_delete_tree') );
                } 
                echo createTable($config);
                unset ($config);
            }
            else if ($data['confirm'] == $data['id'])
            {
                // Delete Item
                $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $data['id'], $GLOBALS['_BIGACE']['SESSION']->getUserID());
                if ($temp_right->canDelete()) 
                {
                    if ($actualItem->getID() == _BIGACE_TOP_LEVEL) {
                        // TODO translate
                        displayError('Could not delete Item, the Item is TOP_LEVEL and can NOT be deleted!');
                    } else {
                    	if($GLOBALS['_ITEMTYPE'] == _BIGACE_ITEM_MENU)
                        	$result = $GLOBALS['_ADMIN']->deleteItem($data['id'], true);
                        else 
                        	$result = $GLOBALS['_ADMIN']->deleteItem($data['id'], false);
                    } 
                } 
                else 
                {
                    import('classes.exception.NoDeleteRightException');
                    ExceptionHandler::processAdminException( new NoDeleteRightException('No sufficient permission to delete Item.') );
                }
            }
            else
            {
                ExceptionHandler::processAdminException( new WrongArgumentException(400, 'Could not delete Item. Submitted confirmation was invalid!') );
            }
            
        } else {
            ExceptionHandler::processAdminException( new WrongArgumentException(400, 'Could not delete Item. ItemID is missing!') );
        }
        
        return $result;
    }


    /**
     * Lege neues Recht an, wenn dieses noch nicht existiert, sonst update existierendes.
     */
    function createNewRight($data)
    {
        if (isset($data['id']) && isset($data['group']) && isset($data['rights']))
        {
            $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $data['id'], $GLOBALS['_BIGACE']['SESSION']->getUserID());
            $allowed = $temp_right->canWrite();

            if($allowed) {
                if($data['rights'] >= _BIGACE_RIGHTS_DELETE) {
                    $allowed = $temp_right->canDelete();                
                }
            }

            if ($allowed) 
            {
                if (!$GLOBALS['_RIGHT_ADMIN']->checkForExistence($data['group'],$data['id']))  {
                    $GLOBALS['_RIGHT_ADMIN']->createGroupRight($data['group'], $data['id'], $data['rights']);
                } else {
                    $GLOBALS['_RIGHT_ADMIN']->changeRight($data['group'], $data['id'], $data['rights']);
                }
            }
            else
            {
                ExceptionHandler::processAdminException( new NoWriteRightException('Missing permission to create new permission.') );
            }
        }
        else
        {
            showChangeRightMask( $data );
        }
    }

    /**
     * Saves changed File details for children items.
     * @param array data to change
     * @param item the parent Menu
     */
    function saveFileDataRecursive($data, $parent)
    {
    	$children = $parent->getChildren();
    	for ($i = 0; $i < $children->count(); $i++) {
    		$child = $children->next();
    		$temp_data['id'] = $child->getID();
    		$temp_data['langid'] = $child->getLanguageID();
    		$temp_data['name'] = $child->getName();
    		if (isset($data['recursive_catchwords'])) {
    			$temp_data['catchwords'] = $data['catchwords'];
    			$temp_data['recursive_catchwords'] = 1;
    		}
    		if (isset($data['recursive_description'])) {
    			$temp_data['description'] = $data['description'];
    			$temp_data['recursive_description'] = 1;
    		}
    		if (isset($data['recursive_layout'])) {
    			$temp_data['text_4'] = $data['text_4'];
    			$temp_data['recursive_layout'] = 1;
    		}
    		saveFileData($temp_data);
    		if ($child->hasChildren()) saveFileDataRecursive($temp_data, $child);
    	}
    }

    /**
     * Saves changed File details.
     */
    function saveFileData($data)
    {
        if (isset($data['id']) && isset($data['langid']))
        {
            $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $data['id'], $GLOBALS['_BIGACE']['SESSION']->getUserID());
            if ($temp_right->canWrite()) 
            {
            	if(!isset($data['name']) ||strlen(trim($data['name'])) == 0)
				{
					// TODO translate error message
					displayError("Empty name is not allowed!");
				}
				else
				{
					$curItem = $GLOBALS['_SERVICE']->getItem($data['id'],ITEM_LOAD_FULL,$data['langid']);
	       			$curFilenameForUID = $curItem->getURL();
	
	       			if(!isset($data['unique_name']) || strlen($data['unique_name']) == 0)
	       				$data['unique_name'] = $curItem->getUniqueName();
	       				 
					//$data['unique_name'] = bigace_build_unique_name($data['unique_name'], $curFilenameForUID);
	
					// create a different unique name in the one case:
					// actual differs from submitted AND submitted is not empty!
					if($curItem->getUniqueName() != $data['unique_name']) {
						
			        	// check if unique name exists, if so: create a different one
			        	$curUniqueName = bigace_unique_name_raw($data['unique_name']);
			           	if($curUniqueName != null) {
							// check if unique name NOT matches current item
			            	if($curUniqueName['itemid'] != $data['id'] || $curUniqueName['language'] != $data['langid'] || $curUniqueName['command'] != $GLOBALS['_ADMIN']->getCommand()) {
			            		$data['unique_name'] = $GLOBALS['_ADMIN']->buildUniqueNameSafe($data['unique_name'], getFileExtension($data['unique_name']));
			            	}
					    }
					}	
					return $GLOBALS['_ADMIN']->changeItem($data['id'], $data['langid'], $data);
				}
			} 
            else {
                ExceptionHandler::processAdminException( new NoWriteRightException('No sufficient permission to save submitted Item data.') );
            }
        }
        return false;
    }

    /**
     * Deletes the specified history versions for a language for the specified item.
     * 
     * @param int the Item ID
     * @param int the Item Language ID
     * @param array an Array with modified Timestamps that should be deleted
     */
    function deleteHistoryVersions($id, $languageid, $modifieddate = array())
    {
        $item = $GLOBALS['_SERVICE']->getClass($id, ITEM_LOAD_FULL, $languageid);
        $ihas = new ItemHistoryAdminService($GLOBALS['_ADMIN']->getItemtypeID());
    
        // make sure we work with an array!
        if (!is_array($modifieddate)) {
            $modifieddate = array($modifieddate);
        }
        
        $i = 0;
        // Loop submitted Item IDs
        foreach ($modifieddate as $key) {
            
            $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $id, $GLOBALS['_BIGACE']['SESSION']->getUserID());
            if ($temp_right->canDelete()) {
                //delete history version
                $ihas->deleteHistoryVersion($id, $languageid, $key);
                $i++;
            }
        }
    
        // TODO translate
        displayMessage('Deleted '.$i.' history versions for ' . $item->getName());
    }
    
    /**
     * Deletes all history versions for all languages for all submitted items.
     */
    function deleteHistoricVersions($data, $items)
    {
        $ihas = new ItemHistoryAdminService($GLOBALS['_ADMIN']->getItemtypeID());
    
        // make sure we work with an array!
        if (!is_array($items)) {
            $items = array($items);
        }
        
        // Loop submitted Item IDs
        foreach ($items as $key) 
        {
            $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $key, $GLOBALS['_BIGACE']['SESSION']->getUserID());
            if ($temp_right->canDelete()) 
            {
                $item = $GLOBALS['_SERVICE']->getItem($key);
                $ihas->deleteAllHistoryVersionsAllLanguages($item->getID());
            }
        }
    }
    

    /**
     * Datei Attribute bearbeiten. Kann nur benutzt werden falls globaler USER Schreibrechte auf die Datei hat.
     */
    function editItem ( $data )
    {
        $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $data['id'], $GLOBALS['_BIGACE']['SESSION']->getUserID());

        if ($temp_right->canWrite()) 
        {
            $item = $GLOBALS['_SERVICE']->getItem($data['id'], ITEM_LOAD_FULL, $data['langid']);
            createEditDataMaskForm($data, $item);
        } 
        else 
        {
            ExceptionHandler::processAdminException( new NoWriteRightException('No permission to edit item') );
        }
    }
    
    /**
     * We have to implement a method called <code>createAdminMask($itemtype)</code>. 
     */
    function getAdminMaskInstance() {
        return createAdminMask($GLOBALS['_ITEMTYPE']);
    }
    
    function createHistoryVersionMask($data) 
    {
        $itemMask = getAdminMaskInstance();
        $itemMask->editItem($data['id'], $data['langid'], $itemMask->MODE_EDIT_VERSIONS) ;
    }

    /**
     * Show the "Change File right" Formular
     */
    function showChangeRightMask($data) 
    {
        $itemMask = getAdminMaskInstance();
        if($data['id'] == _BIGACE_TOP_LEVEL && $GLOBALS['_ITEMTYPE'] !== _BIGACE_ITEM_MENU) {
            if(has_permission('admin_default_permission')) {
                $itemMask->editItem(
                    _BIGACE_TOP_LEVEL, 
                    (isset($data['langid']) ? $data['langid'] : _ULC_), 
                    $itemMask->MODE_EDIT_RIGHTS, 
                    array('toolbar' => false, 'title' => getTranslation('defaultPermissions_'.$GLOBALS['_ITEMTYPE']))
                ) ;
            }
        } else {
            $itemMask->editItem($data['id'], $data['langid'], $itemMask->MODE_EDIT_RIGHTS) ;
        }
    }

    /**
     * Zeige Maske um Kategorien einer Datei zu verwalten
     */
    function showChangeCategoryMask($data)
    {
        $itemMask = getAdminMaskInstance();
        $itemMask->editItem($data['id'], $data['langid'], $itemMask->MODE_EDIT_CATEGORY) ;
    }

    function showMultipleItemsMask($data)
    {
        $items = $data['ids'];
    
        // make sure we work with an array!
        if (!is_array($items)) {
            $items = array($items);
        }

        $allItems = array();
                
        // Loop submitted Item IDs
        foreach ($items as $key) 
        {
            $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $key, $GLOBALS['_BIGACE']['SESSION']->getUserID());
            if ($temp_right->canWrite()) 
            {
                    $allItems[] = $GLOBALS['_SERVICE']->getItem($key);
            }
        }

            $perms = array (
                    getTranslation('no_right')  => _BIGACE_RIGHTS_NO,
                    getTranslation('read')      => _BIGACE_RIGHTS_READ,
                    getTranslation('read').', '.getTranslation('write') => _BIGACE_RIGHTS_RW,
                    getTranslation('read').', '.getTranslation('write').', '.getTranslation('delete') => _BIGACE_RIGHTS_RWD
            );
        
        import('classes.util.links.MenuChooserLink');
        $link = new MenuChooserLink();
        $link->setItemID(_BIGACE_TOP_LEVEL);
        $link->setJavascriptCallback('"+javascriptFunction');

        import('classes.util.formular.GroupSelect');
        $gs = new GroupSelect();
        $gs->setName('groupID');

        $smarty = getAdminSmarty();
        $smarty->assign('GROUP_SELECT', $gs->getHtml());

        $smarty->assign('MENU_CHOOSER_JS', 'javascriptFunction');
        $smarty->assign('MENU_CHOOSER_LINK', '"' . LinkHelper::getUrlFromCMSLink($link));
        $smarty->assign('CHOOSE_ID_JS', 'chooseMenuID');

        $smarty->assign('PERMISSION_SELECT', createSelectBox('permission', $perms, _BIGACE_RIGHTS_READ, '', false));
        $smarty->assign('ITEMS', $allItems);
        $smarty->assign('MODE_SET_GROUP_PERM', _MODE_SET_GROUP_PERM);
        $smarty->assign('MODE_UPDATE_MULTIPLE', _MODE_UPDATE_MULTIPLE);
        $smarty->assign('MODE_DELETE_MULTIPLE', _MODE_DELETE_MULTIPLE);
        $smarty->assign('MODE_PARENT_MULTIPLE', _MODE_PARENT_MULTIPLE);
        $smarty->display('MultipleItemsUpdate.tpl');
        unset($smarty);

    }
