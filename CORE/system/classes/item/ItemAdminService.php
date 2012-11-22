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

import('classes.util.IdentifierHelper');
import('classes.util.IOHelper');
import('classes.item.Itemtype');
import('classes.item.ItemtypeHelper');
import('classes.item.Item');
import('classes.item.DBItem');
import('classes.item.ItemHelper');
import('classes.item.ItemService');
import('classes.item.ItemHistoryAdminService');
import('classes.item.ItemProjectService');
import('classes.right.RightAdminService');
import('classes.right.RightService');
import('classes.administration.AdminRequestResult');
import('classes.group.GroupService');
import('classes.seo.UniqueNameService');
import('classes.seo.UniqueNameAdminService');
import('classes.event.EventDispatcher');
import("classes.core.FileCache");

/**
 * The ItemAdminService provides all kind of methods for write
 * access to any Item and Item Language Version of all Itemtypes.
 *
 * Initialize the ItemAdminService with the required Itemtype!
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage item
 */
class ItemAdminService extends ItemService
{
    private $itemProjectService = NULL;
    private $itemService = NULL;
    private $historyAdminService = NULL;

    function ItemAdminService($itemtype) {
        $this->initItemAdminService($itemtype);
    }

    /**
     * Has to be called by implementing classes.
     * @access protected
     */
    function initItemAdminService($itemtype) {
        $this->initItemService($itemtype);
    }

    /**
     * Returns the ItemService for the current Itemtype.
     *
     * TODO replace, this class is an ItemService itself, no need for this construct!!!!
     *
     * @access private
     */
    private function getItemService()
    {
        if ($this->itemService == NULL) {
            $this->itemService = new ItemService($this->getItemtypeID());
        }
        return $this->itemService;
    }

    /**
     * Returns the ItemHistoryAdminService for the current Itemtype.
     * @access private
     */
    private function getItemHistoryAdminService()
    {
        if ($this->historyAdminService == NULL) {
            $this->historyAdminService = new ItemHistoryAdminService($this->getItemtypeID());
        }
        return $this->historyAdminService;
    }

    /**
     * Returns the ItemProjectService for the current Itemtype.
     * @access private
     */
    private function getItemProjectService()
    {
        if ($this->itemProjectService == NULL) {
            $this->itemProjectService = new ItemProjectService($this->getItemtypeID());
        }
        return $this->itemProjectService;
    }

    /**
     * Returns whether the uploaded file type is allowed by the System.
     * This depends on the File type.
     *
     * @return boolean whether the given File is supported by this Itemtype or not
     */
    function isAllowed($file)
    {
        return $this->isSupportedFile($file['name'], $file['type']);
    }

    function isSupportedFile($name, $mimetype = null)
    {
        $itemtypeTemp = ItemtypeHelper::getItemtypeForFile($name, $mimetype);
        if (!is_null($itemtypeTemp) && $itemtypeTemp == $this->getItemtypeID())
            return TRUE;

        return false;
    }

    // ------------------------------------------------------------------------------------------
    // --------------------------------------- [DELETE ITEM] ------------------------------------

    /**
     * This returns:
     * - -2 if last language version has childs
     * - -1 if item was completely removed,
     * -  1 if language version was deleted,
     * -  0 if nothing was performed,
     * @return int the deleted Flag, see method description for details
     */
    function deleteItemLanguage($id, $langid)
    {
        $deleteAll = false;
        $enum = new ItemLanguageEnumeration($this->getItemtypeID(), $id);
        if ($enum->count()==1)
        {
            $temp = $enum->next();
            if ($temp->getID() == $langid)
            {
                if ($this->deleteItem($id, false)) { // TODO use ($this->getItemtypeID() == _BIGACE_ITEM_MENU) ???
                    return -1;
                }
            }
        }
        else if ($enum->count() > 1)
        {
            $res = $this->_deleteItemLanguage($id, $langid);
            if (!$res->isError())
                return 1;
        }
        return 0;
    }

    /**
     * @access private
     */
    function getTableName() {
       return 'item_' . $this->getItemtypeID();
    }

    /**
     * Deletes the Items Languae Versions, all Backups and all depenend Project Values.
     * @access private
     */
    private function _deleteItemLanguage($id, $langid)
    {
		import('classes.item.ItemFutureService');
    	$temp = $this->getClass($id, ITEM_LOAD_FULL, $langid);

        // remove all history versions
        $ihas = $this->getItemHistoryAdminService();
        $ihas->deleteAllHistoryVersions($id, $temp->getLanguageID());

        // delete all Future Versions
        $futureService = new ItemFutureService($this->getItemtypeID());
        if ($futureService->hasFutureVersion($id, $langid))
            $futureService->deleteFutureVersion($id, $langid);

        // delete all Project values for the Item
        $this->deleteAllProjectText($id, $langid);
        $this->deleteAllProjectNum($id, $langid);

        // remove unique names
        bigace_delete_unique_name($this->getItemtypeID(), $id, $langid);

        // and now remove the files content
        if ( file_exists($temp->getFullURL()) && is_file($temp->getFullURL()) ) {
            unlink($temp->getFullURL());
        }

        // remove the item entry itself
        $values = array( 'TABLE'       => $this->getTableName(),
                         'ITEM_ID'     => $id,
                         'LANGUAGE_ID' => $langid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_delete_language');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

        // clear the cache for this item
        $fc = new FileCache();
        $fc->expireAllCacheFiles($this->getItemtypeID(), $id);

        // at the end, inform listener for item deletion
        EventDispatcher::propagateEvent('delete-item',
                            array('itemtype' => $this->getItemtypeID(), 'id' => $id, 'language' => $langid));

        return $res;
    }


    /**
     * Deletes an Item from BIGACE, including all Language Versions,
     * its rights and categorys.
     * If you try to delete the TOP_LEVEL it will return FALSE.
     */
    function deleteItem($id, $deleteRecursive = false)
    {
        if ($id == _BIGACE_TOP_LEVEL)
            return FALSE;

        // delete childs recursive
        $is = $this->getItemService();
        $item = $is->getItem($id);

        if($deleteRecursive) {
            // FIXME set image and file parent to top level if their parent is deleted!
        	if ($item->hasChildren()) {
	            $childs = $is->getLightTree($id);
	            for($i=0; $i < $childs->count(); $i++)
	            {
	                $tempItem = $childs->next();
	                $this->deleteItem($tempItem->getID(), $deleteRecursive);
	            }
	        }
        }

        $enum = new ItemLanguageEnumeration($this->getItemtypeID(), $id);
        for ($i=0; $i<$enum->count();$i++)
        {
            $tempLang = $enum->next();
            $temp = $this->_deleteItemLanguage($id, $tempLang->getID());
        }

        $this->removeAllCategoryLinks($id);
        $right = new RightAdminService($this->getItemtypeID());
        $right->deleteItemRights($id);

        $values = array( 'TABLE'       => $this->getTableName(),
                         'ITEM_ID'     => $id,
                         'CID'         => _CID_ );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_delete');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

        // clear the cache for this item
        $fc = new FileCache();
        $fc->expireAllCacheFiles($this->getItemtypeID(), $id);

        // at the end, inform listener for item deletion
        EventDispatcher::propagateEvent('delete-item',
                            array('itemtype' => $this->getItemtypeID(), 'id' => $id));

        return $res;
    }

    // ------------------------------------------------------------------------------------------
    // --------------------------------------- [POSITION] ---------------------------------------

    /**
     * @access private
     */
    private function _changePosition($id, $langid, $order = 'DESC', $flag = '<=')
    {
        $item = $this->getClass($id, ITEM_LOAD_FULL, $langid);
        $pos = $item->getPosition();

        $values = array( 'TABLE'       => $this->getTableName(),
                         'ITEM_ID'     => $id,
                         'PARENT_ID'   => $item->getParentID(),
                         'LANGUAGE_ID' => $langid,
                         'POSITION'    => $pos,
                         'ORDER'       => $order,
                         'LIMIT'       => '1',
                         'DIRECTION'   => $flag );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_select_by_position');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $nextItem = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

        if ($nextItem->count() > 0)
        {
            $next = new DBItem( $nextItem->next() );
            $next->initItemtype($this->getItemtypeID());
            $nextPos = $next->getPosition();

            if ($nextPos == $pos) {
                $nextPos++;
            }

            $this->_changeItemColumn($id, 'num_4', $nextPos);
            $this->_changeItemColumn($next->getID(), 'num_4', $pos);
            return true;
        }
        return false;
    }

    /**
     * Sets the new Item position.
     * @param long id the Item ID to set the new position for
     * @param int position the new Position
     * @return boolean whether the change worked or not
     */
    function setItemPosition($id, $position)
    {
        return $this->_changeItemColumn($id, 'num_4', $position);
    }

    /**
     * Moves the Item a position up.
     * @return boolean whether the Position was changed or not
     */
    function raisePosition($id, $langid)
    {
        return $this->_changePosition($id, $langid, 'DESC', '<=');
    }

    /**
     * Moves the Item a position down.
     * @return boolean whether the Position was changed or not
     */
    function lowerPosition($id, $langid)
    {
        return $this->_changePosition($id, $langid, 'ASC', '>=');
    }

    /**
     * Moves an Item to a new Parent ID.
     * No further check is performed, so make sure, the new Parent
     * is not the Page itself or a child of the current Page!
     * @return boolean whether the Page was moved or not
     */
    function moveItem($itemid, $newParentID)
    {
        if($itemid != _BIGACE_TOP_LEVEL && $newParentID != _BIGACE_TOP_PARENT)
        {
            if(!$this->isChildOf($itemid, $newParentID))
            {
                if ($itemid != $newParentID)
                    return $this->_changeItemColumn($itemid, 'parentid', $newParentID);
            }
        }
        return FALSE;
    }

    /**
     * Get the highest Position within the given Tree.
     * Will read the childs of the given ParentID and search the highest
     * value.
     * If the returned Result is successful, you get the Max Position by
     * calling <code>getID()</code>.
     * @return AdminRequestResult the Result
     */
    function getMaxPositionForParentID($parentid, $languageid)
    {
        $values = array( 'TABLE'        => $this->getTableName(),
                         'PARENT_ID'    => $parentid,
                         'LANGUAGE_ID'  => $languageid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_select_max_position');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $result = new AdminRequestResult(!$res->isError());
        if (!$res->isError()) {
            $res = $res->next();
            $result->setID( $res['max_position'] );
        }
        return $result;
    }

    // -------------------------------------------------------------------------------------------
    // --------------------------------------- [CATEGORYS] ---------------------------------------

    function addCategoryLink($itemid, $categoryid)
    {
        $values = array( 'TABLE'       => 'item_category',
                         'ITEMTYPE'    => $this->getItemTypeID(),
                         'ITEM_ID'     => $itemid,
                         'CATEGORY_ID' => $categoryid,
                         'CID'         => _CID_ );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('admin_item_add_category_link');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    function removeCategoryLink($itemid, $categoryid)
    {
        $values = array( 'TABLE'       => 'item_category',
                         'ITEMTYPE'    => $this->getItemTypeID(),
                         'ITEM_ID'     => $itemid,
                         'CATEGORY_ID' => $categoryid,
                         'CID'         => _CID_ );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('admin_item_delete_category_link');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    function removeAllCategoryLinks($itemid)
    {
        $values = array( 'TABLE'       => 'item_category',
                         'ITEMTYPE'    => $this->getItemTypeID(),
                         'ITEM_ID'     => $itemid,
                         'CID'         => _CID_ );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('admin_item_delete_all_category_links');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    // -------------------------------------------------------------------------------------------
    // --------------------------------------- [PROJECT FIELDS] ----------------------------------

    function deleteAllProjectNum($id, $langid)
    {
        $values = array( 'ITEMTYPE'     => $this->getItemtypeID(),
                         'ITEM_ID'      => $id,
                         'LANGUAGE_ID'  => $langid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_delete_all_project_num');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    function deleteAllProjectText($id, $langid)
    {
        $values = array( 'ITEMTYPE'     => $this->getItemtypeID(),
                         'ITEM_ID'      => $id,
                         'LANGUAGE_ID'  => $langid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_delete_all_project_text');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

	/**
     * Inserts or updates the given numeric project value.
     */
    function setProjectNum($id, $langid, $key, $value)
    {
        $projectService = $this->getItemProjectService();

        $sql = 'item_create_project_num';
        if ($projectService->existsProjectNum($id, $langid, $key)) {
            $sql = 'item_update_project_num';
        }

        return $this->_createProjectValue($sql, $id, $langid, $key, $value);
    }

    /**
     * Inserts or updates the given textual project value.
     */
    function setProjectText($id, $langid, $key, $value)
    {
        $projectService = $this->getItemProjectService();

        $sql = 'item_create_project_text';
        if ($projectService->existsProjectText($id, $langid, $key)) {
            $sql = 'item_update_project_text';
        }

        return $this->_createProjectValue($sql, $id, $langid, $key, $value);
    }

    /**
     * @access private
     */
    private function _createProjectValue($file, $id, $langid, $key, $value)
    {
        $values = array( 'ITEMTYPE'     => $this->getItemtypeID(),
                         'ITEM_ID'      => $id,
                         'LANGUAGE_ID'  => $langid,
                         'VALUE'        => $value,
                         'KEY'          => $key );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement($file);
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    // -------------------------------------------------------------------------------------------
    // --------------------------------------- [CHANGE ITEM] -------------------------------------

    function createDataArrayForParent($item) {
		$data = array();

		$data['name'] = $item->getName();
		$data['description'] = $item->getDescription();
		$data['userid'] = $item->getCreateByID();
		$data['catchwords'] = $item->getCatchwords();
		$data['mimetype'] = $item->getMimetype();
		$data['langid'] = $item->getLanguageID();
		$data['parentid'] = $item->getID();
		$data['workflow'] = $item->getWorkflowName();
		$data['text_1'] = $item->getItemText("1");
		$data['text_2'] = $item->getItemText("2");
		$data['text_3'] = $item->getItemText("3");
		$data['text_4'] = $item->getItemText("4");
		$data['text_5'] = $item->getItemText("5");
		$data['num_1'] = $item->getItemNum("1");
		$data['num_2'] = $item->getItemNum("2");
		$data['num_3'] = $item->getItemNum("3");
		$data['num_4'] = $item->getItemNum("4");
		$data['num_5'] = $item->getItemNum("5");
		$data['date_1'] = $item->getItemDate("1");
		$data['date_2'] = $item->getItemDate("2");
		$data['date_3'] = $item->getItemDate("3");
		$data['date_4'] = $item->getItemDate("4");
		$data['date_5'] = $item->getItemDate("5");
		$data['unique_name'] = '';

		return $data;
    }

    /**
     * Changes an existing File in the System.
     *
     * THIS METHOD CREATES A DATABASE BACKUP!
     */
    function changeItem($id, $languageid, $data)
    {
        $this->_createDatabaseHistoryVersion($id, $languageid);
        return $this->_changeItem($id, $languageid, $data);
    }

    /**
     * Updates the Content in the File that is fetched from Item->getFullURL().
     * If the file does not exists it will simply create it.
     *
     * THIS METHOD CREATES A FILESYSTEM AND A DATABASE BACKUP!
     */
    function updateContent($id, $langid, $content, $data = array())
    {
        $is = $this->getItemService();
        $temp = $is->getItem($id, ITEM_LOAD_FULL, $langid);
        if (ItemHelper::checkFile($temp->getFullURL()))
        {
            // create backup
            $historyFileName = $this->_createFullHistoryVersion($temp->getID(), $temp->getLanguageID());
            //save content
            if (ItemHelper::saveContent($temp->getFullURL(), $content)) {
                // make new version to remember the author/change timestamp
                return $this->_changeItem($id, $langid, $data);
            } else {
	        	$GLOBALS['LOGGER']->logError('Check file permission, '.$temp->getFullURL().' could not be written.');
            }
        } else {
        	$GLOBALS['LOGGER']->logError('Check file permissions, '.$temp->getFullURL().' is not writeable.');
        }

        return false;
    }

    /**
     * Overwrite this method to pipe update Calls to your ItemService implementation.
     */
    function updateItemContent($id, $langid, $content, $data = array())
    {
        return $this->updateContent($id, $langid, $content, $data);
    }

    /**
    * Updates the given Items Content with the last uploaded File.
    *
    * THIS METHOD CREATES A FILESYSTEM AND A DATABASE BACKUP!
    */
    function updateItemWithUpload($id, $file, $languageID)
    {
        if ( $this->isAllowed($file) )
        {
            $is = $this->getItemService();
            $temp = $is->getItem($id, ITEM_LOAD_FULL, $languageID);
            $temp_name = $this->getDirectory() . time() . '_temp_' . $temp->getURL();
            $result_upload = move_uploaded_file($file['tmp_name'], $temp_name);
            if ($result_upload)
            {
                // create backup
                $historyFileName = $this->_createFullHistoryVersion($temp->getID(), $temp->getLanguageID());

                // delete original file
                if (file_exists($temp->getFullURL()) && is_file($temp->getFullURL())) {
                    @unlink($temp->getFullURL());
                } else {
                    $GLOBALS['LOGGER']->logError('Could not delete old File version for Item (ItemType: '.$temp->getItemTypeID().', ID: '.$temp->getID().',Language: '.$temp->getLanguageID().'), because it could not be found ('.$temp->getFullURL().')!');
                }

                //then rename the upload to the items filename
                if (rename ($temp_name, $temp->getFullURL())) {
                    // switch original filename and mimetype - maybe different file format
                    $temp_id = $this->_changeItem($temp->getID(), $temp->getLanguageID(), array('mimetype' => $file['type'], 'text_2' => $file['name']) );
                    return array('result' => TRUE, 'message' => 'Updated Item successful!');
                }
                else {
                    return array('result' => TRUE, 'message' => 'Could not rename uploaded File!');
                }

            }
            else
            {
                return array('result' => false, 'message' => 'Error moving uploaded Item: '.$result_upload);
            }
        }
        else
        {
            return array('result' => false, 'message' => 'No supported Item type: '.$file['type']);
        }
    }

    /**
    * Registers a File that was posted to the system.
    * If your HTML FORM input field for a File looked like that:
    * <code><input name="NewFile" type="file"></code>
    * you can use
    * <code>$_FILES['NewFile']</code>
    * to receive this File.
    *
    * To specify the File setting, pass an Array like this:
    * <code>array("name" => "foo", "description" => "bar", "langid" => "LanguageID")</code>
    *
    * @param Array the File information, which are automatically extracted by the PHP Core
    * @param Array Information to further specifiy this Item
    * @return AdminRequestResult the Result for this Upload
    */
    function registerUploadedFile($file, $data)
    {
        if ( $this->isAllowed($file) )
        {
            $new_file_name = ItemHelper::buildInitialFilenameDirectory($this->getDirectory(), getFileExtension($file['name']), '', '', time());
            $result_upload = move_uploaded_file($file['tmp_name'], $this->getDirectory() . $new_file_name);
            if ($result_upload && is_file($this->getDirectory() . $new_file_name))
            {
                // Switch file rights and umask
               	// TODO - use file permission instead of dir permission and do NOT umask???
                $oldumask = umask(_BIGACE_DEFAULT_UMASK_FILE) ;
                chmod( $this->getDirectory() . $new_file_name, _BIGACE_DEFAULT_RIGHT_DIRECTORY ) ;
                umask($oldumask);

                $itemValues = array('langid' => (isset($data['langid']) ? $data['langid'] : _ULC_),
                                    'name'          => $data['name'],
                                    'text_1'        => $new_file_name,
                                    'mimetype'      => $file['type'],
                                    'description'   => $data['description'],
                                    'parentid'      => (isset($data['parentid']) ? $data['parentid'] : _BIGACE_TOP_LEVEL),
                                    'text_2'        => $file['name']);

                if(isset($data['unique_name']))
                	$itemValues['unique_name'] = $data['unique_name'];

                $temp_id = $this->createItem( $itemValues );

                $result = new AdminRequestResult(TRUE, 'Successful created new Item with uploaded File!');
                $result->setID($temp_id);
                $result->setName($data['name']);
                $result->setValue('langid', $itemValues['langid']);

                return $result;
            }
            else
            {
                $r = new AdminRequestResult(FALSE, 'Error moving uploaded Item!');
                $r->setValue('code', '1');
                return $r;
            }
        }
        else
        {
            $r = new AdminRequestResult(FALSE, 'File not supported!');
            $r->setValue('code', '2');
            return $r;
        }
    }

    function registerAsFile($filename, $content, $data)
    {
        if ( $this->isSupportedFile($filename) )
        {
            $new_file_name = ItemHelper::buildInitialFilenameDirectory($this->getDirectory(), getFileExtension($filename), '', '', time());
            $out = fopen($this->getDirectory() . $new_file_name, 'w+');
            fwrite($out, $content);
            fclose($out);
            if (is_file($this->getDirectory() . $new_file_name))
            {
                // Switch file rights and umask
               	// TODO - use file permission instead of dir permission and do NOT umask???
                chmod($this->getDirectory() . $new_file_name, _BIGACE_DEFAULT_RIGHT_DIRECTORY);

                $itemValues = array('langid' => (isset($data['langid']) ? $data['langid'] : _ULC_),
                                    'name'          => $data['name'],
                                    'text_1'        => $new_file_name,
                                    'mimetype'      => $data['mimetype'],
                                    'description'   => $data['description'],
                                    'parentid'      => (isset($data['parentid']) ? $data['parentid'] : _BIGACE_TOP_LEVEL),
                                    'text_2'        => $filename);

                if(isset($data['unique_name']))
                	$itemValues['unique_name'] = $data['unique_name'];

                $temp_id = $this->createItem( $itemValues );

                $result = new AdminRequestResult(TRUE, 'Created item with imported file.');
                $result->setID($temp_id);
                $result->setName($data['name']);
                $result->setValue('langid', $itemValues['langid']);

                return $result;
            }
            else
            {
                $r = new AdminRequestResult(FALSE, 'Error writing imported file!');
                $r->setValue('code', '1');
                return $r;
            }
        }
        else
        {
            $r = new AdminRequestResult(FALSE, 'File not supported!');
            $r->setValue('code', '2');
            return $r;
        }
    }


    /**
    * Inserts a new Item, NOT a language version!
    *
    * THIS ONLY INSERTS THE DATABASE ENTRY. IF YOU NEED A FILESYSTEM ENTRY,
    * YOU HAVE TO CREATE IT FIRST AND SUBMIT ITS NAME WITHIN THE PASSED ARRAY.
    *
    * Check for ($result === false) to know if this worked.
    *
    * @return mixed int the new Item ID or false
    */
    function createItem($data)
    {
        $newid = IdentifierHelper::createNextID( $this->getTableName() );
        $createresult = $this->_insertItem($data, $newid);
        // probably a problem with the ID
        if($createresult === false)
        {
            $newid = IdentifierHelper::getMaximumID( $this->getTableName() ) + 1;
            $createresult = $this->_insertItem($data, $newid);
            $count = 0;
            while($createresult === false && $count < 20)
            {
                // FIXME: can create a language version of an existing item IF new item is different language and something
                // wrong happened with the ID
                if($createresult === false)
                    $newid = IdentifierHelper::createNextID( $this->getTableName() );
                $createresult = $this->_insertItem($data, $newid);
                $count ++;
            }
        }

        if($createresult === false) {
            return false;
        }

        // Create rights for the new item
        $itemtype = $this->getItemtypeID();
        $parentid = _BIGACE_TOP_LEVEL;

        // if a parent was submitted, we take its id  to copy permissions from
        if(isset($data['parentid'])) {
            $parentid = $data['parentid'];
        }
        // if a parent was submitted, we take menu as as itemtype to copy permissions from
        if($parentid != _BIGACE_TOP_LEVEL) {
            $itemtype = _BIGACE_ITEM_MENU;
        }

        // Create rights for the new item
        $X_RIGHT_ADMIN = new RightAdminService( $this->getItemtypeID() );
        // Copy all parent rights
        $X_RIGHT_ADMIN->createRightCopy($parentid, $newid, $itemtype);

// if user is allowed to create page it is enough to copy permissions.
// otherwise we get problems with permissions for anonymous user allowed to WRITE...
/*
        // FIXME shall we create only configured default rights???
        $groupService = new GroupService();
        $memberships = $groupService->getMemberships($GLOBALS['_BIGACE']['SESSION']->getUser());
        foreach($memberships AS $mShip) {
            // Create rights for the all Groups of the User creating this Item
            $X_RIGHT_ADMIN->createGroupRight($mShip->getID(), $newid, _BIGACE_RIGHTS_RWD);
        }
*/
        return $newid;
    }

    /**
    * Creates a NEW language for the given Item.
    * Copies the settings from the given Language Version of this Item if they are not passed.
    */
    function createLanguageVersion($id, $copyLangID, $data)
    {
        if (isset($data['langid']))
        {
            $tempLang = new Language($data['langid']);
            $item = new Item($this->getItemtypeID(), $id, ITEM_LOAD_FULL, $copyLangID);

            // do not copy the filename from the original item, this would cause problems
            // create a new filename
            if (!isset($data['text_1'])) {
                $data['text_1'] = ItemHelper::buildInitialFilenameDirectory($this->getDirectory(), getFileExtension($item->getUrl()), $item->getID(), $tempLang->getLocale(), time());
            }
            if(!isset($data['unique_name'])) {
            	$data['unique_name'] = $this->buildUniqueNameSafe($data['name'], getFileExtension($data['name']));
            } else {
                $data['unique_name'] = $this->buildUniqueNameSafe($data['unique_name'], getFileExtension($data['unique_name']));
            }

            // some columns are not replicated, becuase they are language dependend (text_1)
            // or handled differently (unique_name)
            $data['name']       = (!isset($data['name']))       ? $item->getName() . ' ['.$tempLang->getLocale().']' : $data['name'];
            $data['mimetype']   = (!isset($data['mimetype']))   ? $item->getMimetype()      : $data['mimetype'];
            $data['parentid']   = (!isset($data['parentid']))   ? $item->getParentID()      : $data['parentid'];
            $data['workflow']   = (!isset($data['workflow']))   ? $item->getWorkflowName()  : $data['workflow'];
            //$data['text_1']     = (!isset($data['text_1']))     ? $item->getItemText('1')   : $data['text_1'];
            $data['text_2']     = (!isset($data['text_2']))     ? $item->getItemText('2')   : $data['text_2'];
            $data['text_3']     = (!isset($data['text_3']))     ? $item->getItemText('3')   : $data['text_3'];
            $data['text_4']     = (!isset($data['text_4']))     ? $item->getItemText('4')   : $data['text_4'];
            $data['text_5']     = (!isset($data['text_5']))     ? $item->getItemText('5')   : $data['text_5'];
            $data['num_1']      = (!isset($data['num_1']))      ? $item->getItemNum('1')    : $data['num_1'];
            $data['num_2']      = (!isset($data['num_2']))      ? $item->getItemNum('2')    : $data['num_2'];
            $data['num_3']      = (!isset($data['num_3']))      ? $item->getItemNum('3')    : $data['num_3'];
            $data['num_4']      = (!isset($data['num_4']))      ? $item->getItemNum('4')    : $data['num_4'];
            $data['num_5']      = (!isset($data['num_5']))      ? $item->getItemNum('5')    : $data['num_5'];
            $data['date_1']     = (!isset($data['date_1']))     ? $item->getItemDate('1')   : $data['date_1'];
            $data['date_2']     = (!isset($data['date_2']))     ? $item->getItemDate('2')   : $data['date_2'];
            $data['date_3']     = (!isset($data['date_3']))     ? $item->getItemDate('3')   : $data['date_3'];
            $data['date_4']     = (!isset($data['date_4']))     ? $item->getItemDate('4')   : $data['date_4'];
            $data['date_5']     = (!isset($data['date_5']))     ? $item->getItemDate('5')   : $data['date_5'];

            $this->_insertItem($data, $id);

            return new AdminRequestResult(true, $data['text_1']);
        }
        return new AdminRequestResult(false, 'No Language ID was passed!');
    }


    // ---------------------------------------------------------------------------------------------
    // ------------------------------------- [HISTORY METHODS] -------------------------------------

    /**
     * Gets the specified History version and uses its Content to
     * replace the Content of the Items published Version.
     * The replaced version (the actual one) will become a history version,
     * to make sure no information will be lost.
     */
    function refreshHistoryVersionContent($itemid, $languageid, $modifiedDate)
    {
        $ihs = new ItemHistoryService($this->getItemtypeID());
        $entr = $ihs->getHistoryVersion($itemid, $languageid, $modifiedDate);
        return $this->updateItemContent($itemid, $languageid, $entr->getContent());
    }

    /**
     * Creates a History version for the linked File and returns the Last Modified Timestamp.
     * With that you are always able to create the full qulified History Filename.
     * @access private
     */
    private function _createContentHistoryVersion($id, $langid)
    {
        $is = $this->getItemService();
        $temp = $is->getItem($id, ITEM_LOAD_FULL, $langid);
        $historyName = ItemHelper::getHistoryURLFull($temp);

        if(file_exists($temp->getFullURL()) && is_file($temp->getFullURL()))
        {
            if (copy($temp->getFullURL(), $historyName)) {
                $GLOBALS['LOGGER']->logDebug('History version file: "' . $historyName . '"');
            } else {
                $GLOBALS['LOGGER']->logError('Failed creating history version in "' . $historyName . '"');
            }
        }

        return $temp->getLastDate();
    }

    /**
     * Updates the values of a History Version.
     * @access private
     */
    private function _updateDatabaseHistoryVersion($id, $langid, $modifieddate, $values)
    {
        $values = $this->_prepareSqlValues($id, $langid, $values);
        $values['MODIFIED_DATE'] = $modifieddate;

        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_history_update');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);

        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
     * Creates a full backup of the given Version and returns the new Filename.
     * @access private
     */
    private function _createDatabaseHistoryVersion($id, $langid)
    {
        $values = $this->_prepareSqlValues($id, $langid, array());

        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_history_create');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);

        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
    * Creates a full backup of the given Version and returns the new Filename.
    * @access private
    */
    private function _createFullHistoryVersion($id, $langid)
    {
        $modifiedts = $this->_createContentHistoryVersion($id, $langid);
        $this->_createDatabaseHistoryVersion($id, $langid);

        $is = $this->getItemService();
        $item = $is->getItem($id, ITEM_LOAD_FULL, $langid);
        $values = array('text_1' => ItemHelper::getHistoryURLForTS($item, $modifiedts));

        $this->_updateDatabaseHistoryVersion($id, $langid, $modifiedts, $values);
        return $modifiedts;
    }

    // ---------------------------------------------------------------------------------------------
    // ------------------------------------- [PRIVATE METHODS] -------------------------------------

    /**
     * Inserts a new Item into the Database.
     * Sets modifiedby, modifiedts and position automatically.
     * @access private
     */
    private function _insertItem($data, $newid)
    {
        $createdBy = (isset($data['userid']))         ? $data['userid']   : ((isset($GLOBALS['_BIGACE']['SESSION'])) ? $GLOBALS['_BIGACE']['SESSION']->getUserID() : _BIGACE_SUPER_ADMIN);
        $desc      = (isset($data['description']))    ? $data['description'] : '';
        $catch     = (isset($data['catchwords']))     ? $data['catchwords'] : '';
        $name      = (isset($data['name']))           ? $data['name']     : $data['filename'];
        $mimetype  = (isset($data['mimetype']))       ? $data['mimetype'] : '';
        $langid    = (isset($data['langid']))         ? $data['langid']   : (defined(_ULC_) ? _ULC_ : 1);
        $parentid  = (isset($data['parentid']))       ? $data['parentid'] : _BIGACE_TOP_LEVEL;
        $workflow  = (isset($data['workflow']))       ? $data['workflow'] : '';
        $url       = (isset($data['text_1']))         ? $data['text_1']   : '';
        $original  = (isset($data['text_2']))         ? $data['text_2']   : '';
        $text3     = (isset($data['text_3']))         ? $data['text_3']   : '';
        $text4     = (isset($data['text_4']))         ? $data['text_4']   : '';
        $text5     = (isset($data['text_5']))         ? $data['text_5']   : '';
        $num1      = (isset($data['num_1']))          ? (int)$data['num_1']    : 'NULL';
        $num2      = (isset($data['num_2']))          ? (int)$data['num_2']    : 'NULL';
        $num3      = (isset($data['num_3']))          ? $data['num_3']    : FLAG_NORMAL;
        $num4      = (isset($data['num_4']))          ? $data['num_4']    : '0';
        $num5      = (isset($data['num_5']))          ? $data['num_5']    : $createdBy;
        $date1     = (isset($data['date_1']))         ? $data['date_1']   : time();
        $date2     = (isset($data['date_2']))         ? $data['date_2']   : '0000000000';
        $date3     = (isset($data['date_3']))         ? $data['date_3']   : '0000000000';
        $date4     = (isset($data['date_4']))         ? $data['date_4']   : '0000000000';
        $date5     = (isset($data['date_5']))         ? $data['date_5']   : '0000000000';

        //calculate max position
        $res = $this->getMaxPositionForParentID($parentid, $langid);
        if ($res->isSuccessful()) {
            $num4 = $res->getID();
        }
        $num4++;

        // prepare values
        $ext = (getFileExtension($original) === false) ? '' : getFileExtension($original);
        $unique_id = isset($data['unique_name']) ? $data['unique_name'] : $this->buildUniqueNameSafe($name, $ext);
        $name      = ItemHelper::fixSqlValue($name);
        $desc      = ItemHelper::fixSqlValue($desc);
        $catch     = ItemHelper::fixSqlValue($catch);

		$text5 = $GLOBALS['_BIGACE']['SQL_HELPER']->quoteAndEscape($text5);

        /* Creating Database Entry for new Item */
        $sql = "'".$langid."', '".$mimetype."', '".$name."', '".$parentid."', '".$desc."', '".$catch."', '".time()."', '".$createdBy."', '".time()."', '".$createdBy."'";
        $sql .= ", '".$workflow."', '".$url."', '".$original."', '".$text3."', '".$text4."', ".$text5.", ".$num1.", ".$num2;
        $sql .= ", ".(int)$num3.", ".(int)$num4.", ".(int)$num5.", '".(int)$date1."', '".(int)$date2."', '".(int)$date3."', '".(int)$date4."', '".(int)$date5."'";

        $values = array( 'TABLE'       => $this->getTableName(),
                         'ITEM_ID'     => $newid,
                         'VALUES'      => $sql,
                         'CID'         => (isset($data['cid']) ? $data['cid'] : _CID_) );

        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_create');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sqlString);

        // indicate errors
        if(!is_null($res) && $res === FALSE)
            return false;

        bigace_set_unique_name($this->getItemtypeID(),$newid,$langid,$unique_id);

        return true;
    }

    function buildUniqueName($name, $extension) {
        return bigace_build_unique_name($name, $extension);
    }

    function buildUniqueNameSafe($name, $extension, $startCounter = 0, &$lastCounter = null) {
    	$unique_name = $this->buildUniqueName($name, $extension);
    	$delim = ConfigurationReader::getConfigurationValue('seo', 'word.delimiter', '-');
		if(bigace_unique_name_raw($unique_name) != null) {
            // find current max count
            $xx = bigace_unique_name_max(getNameWithoutExtension($name) . $delim);
            if($xx !== false && $xx > $startCounter)
                $startCounter = $xx;

			$xxx=$startCounter;
			while(bigace_unique_name_raw($unique_name) != null) {
				$xxx++;
				$temptemp = getNameWithoutExtension($name) . $delim . $xxx;
				$unique_name = $this->buildUniqueName($temptemp, $extension);
			}
            // set counter to last value to pass back to user for example for multiple uploads
            if(!is_null($lastCounter))
                $lastCounter = $xxx;
		}
		return $unique_name;
    }
	/**
     * Executes an Update on the given Item Language Version.
     * @access private
     */
    private function _changeItem($id, $langid, $values)
    {
        return $this->_changeItemLanguageWithTimestamp($id, $langid, $values, time());
    }

    /**
     * $flag must be one of:
     * - FLAG_NORMAL
     * - FLAG_HIDDEN
     * - FLAG_TRASH)
     *
     * @param int $id the item id
     * @param string $language the item language
     * @param int $flag the item flag
     * @return mixed the result: false if update could not be executed.
     */
    function setItemFlag($id, $language, $flag = FLAG_NORMAL)
    {
    	if(!is_null($id) && !is_null($language)) {
   			return $this->changeItemColumnLanguage($id, $language, 'num_3', $flag);
    	}
    	return false;
    }

    /**
     * Move an Item to a new Parent.
     *
     * @param $id the item id to move
     * @param $newParent the new parent (item id) to move item below
     * @return boolean indicates whether the item movement was successful
     */
    function setParent($id, $newParent)
    {
    	if(!is_null($id) && !is_null($newParent)) {
   			return $this->_changeItemColumn($id, 'parentid', $newParent);
    	}
    	return false;
    }

    /**
     * Changes an Items by passing the Column and Value.
     * @access private
     */
    private function _changeItemColumn($id, $columnName, $columnValue)
    {
        $cols = $this->_prepareSqlValues($id, '', array($columnName => $columnValue));
        $cols['COLUMN_NAME'] = $columnName;
        $cols['COLUMN_VALUE'] = $columnValue;
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_change_column');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $cols);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    function changeItemColumnLanguage($id, $langid, $columnName, $columnValue)
    {
        $this->_changeItemColumnLanguage($id, $langid, $columnName, $columnValue);
    }

    /**
     * Changes an Items by passing the Column and Value.
     * @access private
     */
    private function _changeItemColumnLanguage($id, $langid, $columnName, $columnValue)
    {
        $cols = $this->_prepareSqlValues($id, $langid, array($columnName => $columnValue));
        $cols['COLUMN_NAME'] = $columnName;
        $cols['COLUMN_VALUE'] = $columnValue;
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_change_column_language');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $cols);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
     * Executes an Update on the given Item Language Version.
     * @access protected
     */
    protected function _changeItemLanguageWithTimestamp($id, $langid, $values, $timestamp)
    {
        $val = $this->_prepareSqlValues($id, $langid, $values);
        $val['TIMESTAMP'] = $timestamp;

        Hooks::do_action('update_item', $this->getItemtypeID(), $id, $langid, $val, $timestamp);

        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_change');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $val);

        if(isset($values['unique_name'])) {
        	bigace_set_unique_name($this->getItemtypeID(),$id,$langid,$values['unique_name']);
        }

        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
     * Prepares an Array with SQL Values.
     * @access private
     */
    private function _prepareSqlValues($id, $langid, $values = array())
    {
        $vals = ItemHelper::prepareSqlValues($id, $langid, $values);
        $vals['TABLE']       = $this->getTableName();
        $vals['ITEM_ID']     = $id;
        $vals['ITEMTYPE']    = $this->getItemtypeID();
        $vals['LANGUAGE_ID'] = $langid;
        return $vals;
    }

}
