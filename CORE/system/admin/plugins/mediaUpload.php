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
 */

/**
 * Script used to upload new media files.
 */

check_admin_login();
admin_header();

import('classes.item.ItemtypeHelper');
import('classes.image.Image');
import('classes.image.ImageAdminService');
import('classes.file.File');
import('classes.file.FileAdminService');
import('classes.language.LanguageEnumeration');
import('classes.util.IOHelper');
import('classes.util.formular.CategorySelect');
import('classes.util.formular.LanguageSelect');
import('classes.util.html.Option');
import('classes.seo.UniqueNameService');
import('classes.seo.UniqueNameAdminService');

loadLanguageFile('upload', ADMIN_LANGUAGE);

define ('MODE_UPLOAD', 	'upload');
define('MODE_IMPORT', 'importFiles');

$mode = extractVar('mode', null);
$data = extractVar('data', array('id' => _BIGACE_TOP_LEVEL));


if (is_null($mode)) {
    echo uploadForm($data);
}
else if ($mode == MODE_UPLOAD || $mode == MODE_IMPORT)
{
	$namingType = (isset($_POST['namingType']) ? $_POST['namingType'] : '');

	if($namingType == 'namingCount' && (!isset($data['name']) || (isset($data['name']) && trim($data['name']) == ''))) {
		$namingType = 'namingFile';
        //displayError( getTranslation('upload_choose_name') );
        //echo uploadForm($data);
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

        $amount = 0;
        if($mode == MODE_UPLOAD) {
        	$amount = count($_FILES['userfile']['name']);        
        }
        else if($mode == MODE_IMPORT) {
            $all = explode(PHP_EOL, $_POST['importURLs']);
        	$amount = count($all);
        }
        
        if(!isset($data['importURLs']))
        	$data['importURLs'] = "";

        $uniqueNameCounter = 0;
                	
		for($i = 0; $i < $amount; $i++)
		{
            $orgFileName = null;
            $orgFileMimetype = null;
            
            if($mode == MODE_UPLOAD) {
			    $fileToUpload = array(
					    'name' 	=> $_FILES['userfile']['name'][$i],
					    'type' 	=> $_FILES['userfile']['type'][$i],
					    'error' => $_FILES['userfile']['error'][$i],
					    'size'	=> $_FILES['userfile']['size'][$i],
					    'tmp_name'	=> $_FILES['userfile']['tmp_name'][$i]
			    );

                $orgFileName = trim($fileToUpload['name']);
                $orgFileMimetype = $fileToUpload['type'];
            }
            else if($mode == MODE_IMPORT) {
                $cur = trim(str_replace(PHP_EOL,"",$all[$i]));
                if(strlen($cur) < 11)
                    continue;
                $urlParts = parse_url($cur);
                if(!isset($urlParts['scheme']) || !isset($urlParts['host']) || !isset($urlParts['path']))
                    continue;

                $orgFileName = basename($cur);
                $mimetypeTemp = ItemtypeHelper::getMimetypeForFile($orgFileName);
                if(!is_null($mimetypeTemp)) {
                    $orgFileMimetype = $mimetypeTemp;
                    $data['mimetype'] = $orgFileMimetype;
                    $data['importURLs'] .= $cur . PHP_EOL;
                }
                else {
                    continue;
                }
            }

			
			if($orgFileName != '')
			{
				$type = $ith->getItemtypeForFile($orgFileName, $orgFileMimetype);
				$admin = getAdminServiceForFile($type);

				// increase counter
				$counter++;

		    	// allow to upload files without entering a name
	    		if(strlen(trim($origName)) == 0)
	    			$origName = $orgFileName;

				// build item name
				if($namingType == 'namingCount')
					$data['name'] = $origName . ' ('.$counter.')';
				else if($namingType == 'namingFile')
					$data['name'] = $orgFileName;
				else 
					$data['name'] = $origName;

				// build unique name
                $data['unique_name'] = str_replace("{NAME}",$origName,$uniquePattern);
                $data['unique_name'] = str_replace("{COUNTER}",$counter,$data['unique_name']);
                $data['unique_name'] = str_replace("{FILENAME}",$orgFileName,$data['unique_name']);

				// check if unique name exists, if so: create a different one
				$data['unique_name'] = $admin->buildUniqueNameSafe($data['unique_name'], getFileExtension($orgFileName), $uniqueNameCounter, $uniqueNameCounter);				
	
                if($mode == MODE_UPLOAD) {
				    $result = processUpload($admin, $data, $fileToUpload);
                }
                else if($mode == MODE_IMPORT) {
				    $result = processImport($admin, $data, $cur, $orgFileName);
                }

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
			        if($result->getValue('code') != null && $result->getValue('code') == '2') {
			            displayError($result->getMessage() . ' ' . $orgFileMimetype . '<br/>' . getTranslation('name') . ': ' . $orgFileName);
			        }
			        else {
				        displayError(getTranslation('upload_unknown_error') . ': ' . $orgFileName . '<br>' . ($result->getMessage() == '' ? ': ' . $result->getMessage() : ''));
			        }
			    }
			} // file[name] != ''
		}	// foreach files

		if(count($successIDs) == 0) {
	        echo uploadForm($data);
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

	        displayAnswer(getTranslation('upload_success'), $msg, createAdminLink('mediaUpload'), array(), getTranslation('upload_more_files'));
		}

    }
}

admin_footer();


function getAdminServiceForFile($type) 
{
    if($type == _BIGACE_ITEM_IMAGE) {
        $admin = new ImageAdminService();
    } else if($type == _BIGACE_ITEM_FILE) {
        $admin = new FileAdminService();
    } else {
        $admin = new ItemAdminService($type);
    }

    return $admin;
}

function processImport($admin, $data, $url, $filename)
{
    if (strlen(trim($url)) > 0)
    {
        $url = trim(str_replace(PHP_EOL,"",$url));
        $content = download_remote_file($url);
        if($content === false) {
            $result = new AdminRequestResult(false, 'Failed downloading file from: ' . $url);
            $result->setValue('code', 400);
            return $result;
        }
        return $admin->registerAsFile($filename, $content, $data);
    }

    $result = new AdminRequestResult(false, 'Could not process File Upload. You have to select a File and a Name for the Item!');
    $result->setValue('code', 400);
    return $result;
}

function processUpload($admin, $data, $file)
{
    if (isset($file['name']) && $file['name'] != '') {
        return $admin->registerUploadedFile($file, $data);
    }

    $result = new AdminRequestResult(false, 'Could not process File Upload. You have to select a File and a Name for the Item!');
    $result->setValue('code', 400);
    return $result;
}

// --------------------------- [START DOWNLOAD METHODS] ------------------------
function http_get_file($url)  {
	
	$errno = 0;
	$errstr = "";
    $buffer = "";
    $url_stuff = parse_url($url);
    $port = isset($url_stuff['port']) ? $url_stuff['port']:80;

    $fp = fsockopen($url_stuff['host'], $port, $errno, $errstr);
    if($fp === false) {
    	displayError("Could not download file. Error " . $errno . ": " . $errstr);
        return null;
    }

    $query  = 'GET ' . $url_stuff['path'] . " HTTP/1.0\n";
    $query .= 'Host: ' . $url_stuff['host'];
    $query .= "\n\n";

    fwrite($fp, $query);

    while ($line = fread($fp, 8192)) {
      $buffer .= $line;
    }

    preg_match('/Content-Length: ([0-9]+)/', $buffer, $parts);
    if(isset($parts[1]))
        return substr($buffer, - $parts[1]);
    else 
        return null;    
}

function http_get_fopen($filename)
{
    $fp = fopen($filename, 'br');
    if($fp === false)
        return null;
    $buffer = "";
    while ($line = fread($fp, 8192)) {
        $buffer .= $line;
    }
    fclose($fp);
    return $buffer;
}

function http_get_curl($url)
{
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)"); // TODO do we need that ???
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function download_remote_file($from)
{
    $temp = null;
    if(ini_get('allow_url_fopen'))
        return http_get_fopen($from);
    if(is_null($temp))
        $temp = http_get_file($from);
    if(is_null($temp) && method_exists('curl_init'))
        $temp = http_get_curl($from);
    return $temp;
}
// ---------------------------- [END DOWNLOAD METHODS] -------------------------

// the upload html formular
function uploadForm($data)
{

    if (!isset($data['name'])        ) $data['name']            = '';
    if (!isset($data['description']) ) $data['description']     = '';
    if (!isset($data['langid'])      ) $data['langid']          = $GLOBALS['_BIGACE']['SESSION']->getLanguageID();
    if (!isset($data['category'])    ) $data['category']        = _BIGACE_TOP_LEVEL;
    if (!isset($data['parentid'])    ) $data['parentid']        = _BIGACE_TOP_LEVEL;
    if (!isset($data['unique_name']) ) $data['unique_name']     = "";
    if (!isset($data['importURLs'])  ) $data['importURLs']      = "";

    import('classes.menu.MenuService');
    import('classes.util.links.MenuChooserLink');
    import('classes.util.html.JavascriptHelper');

    $ms = new MenuService();
    $parent = $ms->getMenu($data['parentid']);

    $link = new MenuChooserLink();
    $link->setJavascriptCallback('setParent');
    $parentChooser1 = JavascriptHelper::createJSPopup('parentSelector', 'SelectParent', '400', '350', LinkHelper::getUrlFromCMSLink($link), array(), 'yes');

    $link->setJavascriptCallback('setParentImport');
    $parentChooser2 = JavascriptHelper::createJSPopup('parentSelector2', 'SelectParent', '400', '350', LinkHelper::getUrlFromCMSLink($link), array(), 'yes');

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

    $ls = new LanguageSelect(ADMIN_LANGUAGE);
    $ls->setPreSelected(ADMIN_LANGUAGE);
    $ls->setName('data[langid]');

    $smarty = getAdminSmarty();
    $smarty->assign("ACTION_LINK", createAdminLink($GLOBALS['MENU']->getID()));
    $smarty->assign("MODE_UPLOAD", MODE_UPLOAD);
    $smarty->assign("MODE_IMPORT", MODE_IMPORT);
    $smarty->assign("MAX_FILE_SIZE", UPLOAD_MAX_SIZE);
    $smarty->assign("DATA_NAME", $data['name']);
    $smarty->assign("DATA_DESCRIPTION", $data['description']);
    $smarty->assign("CATEGORY_STARTID", _BIGACE_TOP_LEVEL);
    $smarty->assign("ITEMTYPE", _BIGACE_ITEM_FILE); // only for the image to display
    $smarty->assign("CATEGORY_SELECTOR", $s->getHtml());
    $smarty->assign("UNIQUE_NAME", $data['unique_name']);
    $smarty->assign("IMPORT_URLS", $data['importURLs']);
    $smarty->assign("PARENT_ID", $data['parentid']);
    $smarty->assign("PARENT_NAME", $parent->getName());
    $smarty->assign("PARENT_CHOOSER1", $parentChooser1);
    $smarty->assign("PARENT_CHOOSER2", $parentChooser2);
    $smarty->assign("LANGUAGES", $ls->getHtml());
    $smarty->display('ImportMedia.tpl');

    return "";
}
