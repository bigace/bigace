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
 * @package bigace.administration
 * @subpackage item
 */

/**
 * Include to upload a File by using an upload form.
 */
import('classes.item.ItemtypeHelper');
import('classes.image.Image');
import('classes.image.ImageAdminService');
import('classes.file.File');
import('classes.file.FileAdminService');
import('classes.language.LanguageEnumeration');
import('classes.util.IOHelper');
import('classes.util.formular.CategorySelect');
import('classes.util.html.Option');
import('classes.seo.UniqueNameService');
import('classes.seo.UniqueNameAdminService');

loadLanguageFile('upload', ADMIN_LANGUAGE);

$mode = extractVar('mode', _MODE_SHOW_UPLOAD_FORM);
$data = extractVar('data', array('id' => _BIGACE_TOP_LEVEL));

if ($mode == _MODE_SHOW_UPLOAD_FORM)
{
    echo uploadForm($data, _MODE_PROCESS_UPLOAD);
}
else if ($mode == _MODE_PROCESS_UPLOAD)
{
	$amount = count($_FILES['userfile']['name']);
	$namingType = (isset($_POST['namingType']) ? $_POST['namingType'] : '');

	if($namingType == 'namingCount' && (!isset($data['name']) || (isset($data['name']) && trim($data['name']) == ''))) {
        displayError( getTranslation('upload_choose_name') );
        echo uploadForm($data, _MODE_PROCESS_UPLOAD);
	}
    else {

		$origName = $data['name'];
		$counter = 0;
		$successIDs = array();
	    $ith = new ItemtypeHelper();

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
				$admin = getAdminServiceForFile($fileToUpload);

				// increase counter
				$counter++;
	
				// build file name
				if($namingType == 'namingCount')
					$data['name'] = $origName . ' ('.$counter.')';
				else if($namingType == 'namingFile')
					$data['name'] = $fileToUpload['name'];
				else 
					$data['name'] = $origName;
		
				// check if unique name exists, if so: create a different one
				$data['unique_name'] = $admin->buildUniqueNameSafe($data['name'], getFileExtension($fileToUpload['name']));
	
				$result = processUpload( $admin, $data, $fileToUpload );

				if ( $result->isSuccessful() )
				{
					$successIDs[] = array('id' 			=> $result->getID(), 
										  'language' 	=> $result->getValue('langid'),
										  'name'		=> $data['name'],
										  'type'		=> $type
					);
				}
				else
				{
					$counter--;

				    // not supported
				    if($result->getValue('code') != null && $result->getValue('code') == '2')
				    {
				        displayError($result->getMessage() . ' ' . $fileToUpload['type'] . '<br/>' . getTranslation('name') . ': ' . $fileToUpload['name'] );
				    }
				    else
				    {
					    displayError( getTranslation('upload_unknown_error') . ': ' . $fileToUpload['name'] . '<br>' . ($result->getMessage() == '' ? ': ' . $result->getMessage() : ''));
				    }
				}
			} // file[name] != ''
		}	// foreach files

		if(count($successIDs) == 0) {
	        echo uploadForm($data, _MODE_PROCESS_UPLOAD);
		}
		else {
	        // Successful created new Item with File Upload.
	        // Display result to User and show Formular to go to the new Item.
	        include_once(_ADMIN_INCLUDE_DIRECTORY.'answer_box.php');

			$msg = array();
			$ccc = 0;
			foreach($successIDs AS $uploadResult)
			{
				$ccc++;
		        $link = '';
		        $hidden = array('mode'         => _MODE_EDIT_ITEM,
		                        'data[id]'     => $uploadResult['id'],
		                        'data[langid]' => $uploadResult['language'] );
		        if ($uploadResult['type'] == _BIGACE_ITEM_IMAGE) {
	                $link = createAdminLink(_ADMIN_ID_IMAGE_MAIN, $hidden);
		        } else if ($uploadResult['type'] == _BIGACE_ITEM_FILE) {
	                $link = createAdminLink(_ADMIN_ID_FILE_MAIN, $hidden);
		        }
				$msg['ID: '.$uploadResult['id']] = '<img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'item_'.$uploadResult['type'].'_new.png" border="0"> <a href="'.$link.'">'.$uploadResult['name'].'</a>';
			}

	        displayAnswer(getTranslation('upload_success'), $msg, createAdminLink('mediaUpload'), array(), 'Mehr Dateien hochladen');
		}

    }
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
    if (isset($data['name']) && trim($data['name']) != '' && isset($file['name']) && $file['name'] != '')
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
function uploadForm($data, $mode = 'uploadMode')
{

    if (!isset($data['name'])        ) $data['name']            = '';
    if (!isset($data['description']) ) $data['description']     = '';
    if (!isset($data['langid'])      ) $data['langid']          = $GLOBALS['_BIGACE']['SESSION']->getLanguageID();
    if (!isset($data['category'])    ) $data['category']        = _BIGACE_TOP_LEVEL;

    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("UploadFormular.tpl.htm", false, true);

    $tpl->setCurrentBlock("uploadForm");
    $tpl->setVariable("ACTION_LINK", createAdminLink($GLOBALS['MENU']->getID()));
    $tpl->setVariable("MODE", $mode);
    $tpl->setVariable("MAX_FILE_SIZE", UPLOAD_MAX_SIZE);
    $tpl->setVariable("DATA_NAME", $data['name']);
    $tpl->setVariable("DATA_DESCRIPTION", $data['description']);
    $tpl->setVariable("CATEGORY_STARTID", _BIGACE_TOP_LEVEL);
    $tpl->setVariable("ITEMTYPE", _BIGACE_ITEM_FILE); // only for the image to display
		
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
    $tpl->setVariable("CATEGORY_SELECTOR", $s->getHtml());

    $langEnum = new LanguageEnumeration();
    for ($i = 0; $i < $langEnum->count(); $i++)
    {
        $temp = $langEnum->next();
        $languages[$temp->getName(ADMIN_LANGUAGE)] = $temp->getID();
        $tpl->setVariable("LANGUAGE_ID", $temp->getID());
        $tpl->setVariable("LANGUAGE_NAME", $temp->getName(ADMIN_LANGUAGE));
        $selected = '';
		if(!isset($data['langid'])) $data['langid'] = ADMIN_LANGUAGE;
        if ($data['langid'] == $temp->getID())
            $selected = ' selected';
        $tpl->setVariable("LANGUAGE_SELECTED", $selected);
        $tpl->setCurrentBlock("language");
        $tpl->parseCurrentBlock("language");
    }

    $tpl->parseCurrentBlock("uploadForm");

    return $tpl->get();
}
