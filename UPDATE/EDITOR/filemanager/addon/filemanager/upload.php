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
 * @version $Id$
 * @author Kevin Papst
 * @package bigace.addon.filemanager
 */
require_once(dirname(__FILE__).'/environment.php');
require_once(dirname(__FILE__).'/listings.php');

Translations::loadGlobal('administration');
Translations::loadGlobal('upload');

if(!defined('_BIGACE_FILEMANAGER'))	die('An error occured.');
if($itemtype == null) die('No Itemtype selected.');

import('classes.util.CMSLink');
import('classes.util.LinkHelper');
import('classes.util.formular.CategorySelect');
import('classes.util.html.Option');
import('classes.language.LanguageEnumeration');
import('classes.item.ItemtypeHelper');
import('classes.seo.UniqueNameService');
import('classes.seo.UniqueNameAdminService');
import('classes.item.ItemService');
import('classes.image.ImageService');
import('classes.file.FileService');
import('classes.item.ItemAdminService');
import('classes.image.ImageAdminService');
import('classes.file.FileAdminService');


$selfLink = "upload.php?itemtype=".$itemtype.'&'.$parameter;
$data = extractVar('data', array());

showHtmlHeader();

if (!isset($_FILES['userfile']['name']))
{
	uploadForm($data,$selfLink);
}
else
{
	$amount = count($_FILES['userfile']['name']);
	$namingType = (isset($_POST['namingType']) ? $_POST['namingType'] : '');

	if($namingType == 'namingCount' && (!isset($data['name']) || (isset($data['name']) && trim($data['name']) == ''))) {
		$namingType = 'namingFile';
	}
    else {
		$origName = $data['name'];
		$counter = 0;
		$successIDs = array();
	    $ith = new ItemtypeHelper();

        // {NAME}       = $origName
        // {FILENAME}   = $fileToUpload['name']
        // {COUNTER}    = $counter
        $uniquePattern = "{FILENAME}";

		if($data['unique_name'] != '') {
            $uniquePattern = $data['unique_name'];
        }

		for($i = 0; $i < $amount; $i++)
		{
			$fileToUpload = array(
					'name' 	=> $_FILES['userfile']['name'][$i],
					'type' 	=> $_FILES['userfile']['type'][$i],
					'error' => $_FILES['userfile']['error'][$i],
					'size'	=> $_FILES['userfile']['size'][$i],
					'tmp_name'	=> $_FILES['userfile']['tmp_name'][$i]
			);

			if($fileToUpload['name'] != '')
			{
				$type = $ith->getItemtypeForFile($fileToUpload['name'], $fileToUpload['type']);
				if($type == $itemtype)
				{
					$admin = getAdminServiceForFile($fileToUpload);

					// increase counter
					$counter++;

			    	// allow to upload files without entering a name
		    		if(strlen(trim($origName)) == 0)
		    			$origName = $fileToUpload['name'];

					// build file name
					if($namingType == 'namingCount')
						$data['name'] = $origName . ' ('.$counter.')';
					else if($namingType == 'namingFile')
						$data['name'] = $fileToUpload['name'];
					else
						$data['name'] = $origName;

					// build unique name
	                $data['unique_name'] = str_replace("{NAME}",$origName,$uniquePattern);
	                $data['unique_name'] = str_replace("{COUNTER}",$counter,$data['unique_name']);
	                $data['unique_name'] = str_replace("{FILENAME}",$fileToUpload['name'],$data['unique_name']);

                    if(defined('GALLERY_PARENT') && !is_null(GALLERY_PARENT)) {
						$data['parentid'] = GALLERY_PARENT;
                    }

					// check if unique name exists, if so: create a different one
					$data['unique_name'] = $admin->buildUniqueNameSafe($data['unique_name'],getFileExtension($fileToUpload['name']));

					$result = processUpload( $admin, $data, $fileToUpload );

					if ( $result->isSuccessful() ) {
						$successIDs[] = array('id' 			=> $result->getID(),
											  'language' 	=> $result->getValue('langid'),
											  'name'		=> $data['name'],
											  'type'		=> $type
						);
					}
					else {
						$counter--;
					    // not supported
					    if($result->getValue('code') != null && $result->getValue('code') == '2') {
					        displayError($result->getMessage() . ' ' . $fileToUpload['type'] . '<br/>' . getTranslation('name') . ': ' . $fileToUpload['name'] );
					    }
					    else {
						    displayError( getTranslation('upload_unknown_error') . ': ' . $fileToUpload['name'] . '<br>' . ($result->getMessage() == '' ? ': ' . $result->getMessage() : ''));
					    }
					}
				}
			} // file[name] != ''
		}	// foreach files

		if(count($successIDs) == 0) {
	        echo uploadForm($data, $selfLink);
		}
		else {
			$allItems = array();

			if($itemtype == _BIGACE_ITEM_FILE)
				$is = new FileService();
			else if($itemtype == _BIGACE_ITEM_IMAGE)
				$is = new ImageService();
			else
				$is = new ItemService($type);

			foreach($successIDs AS $uploadResult) {
				//$uploadResult['type'];
				//$uploadResult['name'];
				$allItems[] = $is->getItem($uploadResult['id'],ITEM_LOAD_FULL,$uploadResult['language']);
			}
			render_listing($itemtype, $allItems);
		}
    }
}

showHtmlFooter();

function getItemServiceForFile($file)
{
    $ith = new ItemtypeHelper();
    $type = $ith->getItemtypeForFile($file['name'], $file['type']);

    if($type == _BIGACE_ITEM_IMAGE) {
        $admin = new ImageAdminService();
    } else if($type == _BIGACE_ITEM_FILE) {
        $admin = new FileAdminService();
    } else {
        $admin = new ItemAdminService($type);
    }

    return $admin;
}

function getAdminServiceForFile($file)
{
    $ith = new ItemtypeHelper();
    $type = $ith->getItemtypeForFile($file['name'], $file['type']);

    if($type == _BIGACE_ITEM_IMAGE) {
        $admin = new ImageAdminService();
    } else if($type == _BIGACE_ITEM_FILE) {
        $admin = new FileAdminService();
    } else {
        $admin = new ItemAdminService($type);
    }

    return $admin;
}

function processUpload($admin, $data, $file)
{
    if (isset($file['name']) && $file['name'] != '')
    {
        $result = $admin->registerUploadedFile($file, $data);

        if ( $result->isSuccessful() )
        {
            if (isset($data['category']))
            {
                if (!is_array($data['category'])) {
                    $data['category'] = array( $data['category'] );
                }

                foreach($data['category'] AS $catid) {
                    if ($catid != _BIGACE_TOP_LEVEL) {
                        $admin->addCategoryLink( $result->getID(), $catid );
                    }
                }
            }
        }
        return $result;
    }
    else
    {
        $result = new AdminRequestResult(false, 'Could not process File Upload. You have to select a File and a Name for the Item!');
        $result->setValue('code', 400);
        return $result;
    }

}

// the upload html formular
function uploadForm($data, $link)
{
    if (!isset($data['name'])        ) $data['name']            = '';
    if (!isset($data['description']) ) $data['description']     = '';
    if (!isset($data['langid'])      ) $data['langid']          = $GLOBALS['_BIGACE']['SESSION']->getLanguageID();
    if (!isset($data['category'])    ) $data['category']        = _BIGACE_TOP_LEVEL;
    if (!isset($data['unique_name']) ) $data['unqiue_name']     = "";

    $tpl = getTemplateService();

    $tpl->assign("ACTION_LINK", $link);
    $tpl->assign("DATA_NAME", $data['name']);
    $tpl->assign("DATA_DESCRIPTION", $data['description']);
    $tpl->assign("CATEGORY_STARTID", _BIGACE_TOP_LEVEL);
    $tpl->assign("ITEMTYPE", _BIGACE_ITEM_FILE); // only for the image to display
    $tpl->assign("UNIQUE_NAME", $data['unqiue_name']);

    $s = new CategorySelect();
    $s->setID('category');
    $s->setName('data[category][]');
    $s->setIsMultiple();
    $s->setSize(5);
    $e = new Option();
    $e->setText(getTranslation('please_choose'));
    $e->setValue(_BIGACE_TOP_LEVEL);
    $e->setIsSelected();
    $s->addOption($e);
    $s->setStartID(_BIGACE_TOP_LEVEL);
    $tpl->assign("CATEGORY_SELECTOR", $s->getHtml());

    $langEnum = new LanguageEnumeration();
    $languages = array();
    $selected = '';
    for ($i = 0; $i < $langEnum->count(); $i++)
    {
        $temp = $langEnum->next();
        $languages[$temp->getName()] = $temp->getID();
		if(!isset($data['langid'])) $data['langid'] = $GLOBALS['BIGACE']['SESSION']->getLanguageID();
        if ($data['langid'] == $temp->getID())
            $selected = $temp->getID();
    }

    $tpl->assign("LANGUAGE_SELECTED", $selected);
    $tpl->assign("LANGUAGES", $languages);
    $tpl->display("FM_UploadFormular.tpl");
}