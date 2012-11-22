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
 * @subpackage updates
 */

define('ZIP_DIR_SEPARATOR', '/');
define('DIR_SEPARATOR', '/');

/**
 * Returns all available Upate Modules. If you pass an Array, it works
 * as Ignore File List. All found Module Names will be compared to these
 * Ignore Files and not be included within the Method Result.
 *
 * Returns an Array of UpdateModul Objects.
 */
function getAllUpdateModules($ignoreList) 
{
    $modules = array();

    //fetch all Update Module Names
    $names = getAllUpdateModulNames($ignoreList);
    foreach($names AS $moduleName)
    {
        //fetch all available Module Names
        $module = getUpdateModul($moduleName);
        
        // if a valid modul is found that might be installed for the 
        // current Consumer, push it to the array
	    if ($module->isValid() && $module->isAllowedForConsumer()) {
        	//push UpdateModule to array
            array_push($modules, $module);
        }
    }
    // return all found and valid Modules
    return $modules;
}

function getUpdateModul($modulName) {
    $mod = new UpdateModul($modulName);
    $mod->addSetting('installURL', createAdminLink($GLOBALS['MENU']->getID(), array( PARAM_DIRECTORY => urlencode($mod->getName()), PARAM_MODE => MODE_INSTALL )));
	return $mod;
}

function getGroupedUpdateModules($ignoreList)
{
    $modules =  getAllUpdateModules($ignoreList);
    $grouped = array();
    foreach ($modules AS $module)
    {
        if ($module->isValid()) 
        {
            $type = 'default';
            if ($module->getUpdateType() != null) {
                $type = $module->getUpdateType();
            }
            if (!isset($grouped[$type])) {
                $grouped[$type] = array();
            }
            $grouped[$type][] = $module;
            //echo "$grouped".'['."$type".'][] = '.$module->getName() . '<br>';
        }
    }
    return $grouped;
}

/**
 * Returns all available Module Names. If you pass an Array, it works
 * as Ignore File List. All found Module Names will be compared to these
 * Ignore Files and not be included within the Method Result.
 */
function getAllUpdateModulNames($ignoreList) 
{
    $allNames = array();
    if (file_exists(UPDATE_PATH) && is_dir(UPDATE_PATH))
    {
        $handle=opendir( UPDATE_PATH ); 
    
        while ($file = readdir ($handle)) 
        { 
            if (is_dir(UPDATE_PATH.$file)) {
                $useFileForUpdate = TRUE;
                foreach ($ignoreList AS $ignoreFile) {
                    if ($file == $ignoreFile) {
                        $useFileForUpdate = FALSE;
                    }
                }
                
                if ($useFileForUpdate) {
                    array_push($allNames, $file);
                }
            }
        }
        closedir($handle);
    }
    else
    {
        $GLOBALS['LOGGER']->logError( 'Could not find Update Directoty ('.UPDATE_PATH.') ... Trying to create it!' );
        IOHelper::createDirectory(UPDATE_PATH);
    }

    return $allNames;
}

/**
 * This tries to verify if the given Archive is an valid BIGACE Update.
 */
function checkArchive($zipName) {
    if(!file_exists(UPDATE_PATH . $zipName)) {
        displayError('GIVEN ARCHIVE IS NOT AVAILABLE: ' . $zipName);
        return FALSE;
    }

    if(function_exists('zip_open')) {
        $zip = zip_open(UPDATE_PATH . $zipName);
        if($zip !== false)
        {
            while($zipEntry = zip_read($zip)) 
            {
                $name = zip_entry_name($zipEntry);
                $npu = strpos($name,'update.ini');
                if($npu !== false && $npu == 0)
                    return true;
            }
        }

        return unzip_phpzip($zipName, $extractTo);
    }
    else {
        $unzip = new SimpleUnzip(UPDATE_PATH . $zipName);
        foreach($unzip->Entries as $oI) {
            if($oI->Error == 0) {
                if(($oI->Path . '/' . $oI->Name) == '/update.ini')
                    return TRUE;
            }
        }
    }
    
    return false;
}

// strip off the extension and if available the version number "_2.x.zip" or ".zip"
function getUpdateNameFromZip($zipName) {
    $pos = 0;
    if(strrpos($zipName, '_') === FALSE)
        $pos = strrpos($zipName, '.');
    else
        $pos = strrpos($zipName, '_');

    return substr($zipName, 0, $pos);
}

/**
 * Creates the Update directory from the ZIP Archive in misc/updates/.
 */
function extractUpdateFromZip($zipName, $debug = false) 
{
    if(!file_exists(UPDATE_PATH . $zipName)) {
        displayError('Archive does not exist: ' . $zipName);
        return FALSE;
    }

    if(!checkArchive($zipName)) {
        displayError('Given Archive is not a valid BIGACE Update Archive: ' . $zipName);
        return FALSE;
    }

    //$newDirName = getNameWithoutExtension($zipName);
    $newDirName = getUpdateNameFromZip($zipName);
    $extractTo = UPDATE_PATH . $newDirName . DIR_SEPARATOR;
        
    // save messages within these arrays
    $errors = array();
    $files = array();
      
  	$oldUmask = umask(_BIGACE_DEFAULT_UMASK_DIRECTORY);

    if(file_exists($extractTo)) {
        if(!IOHelper::deleteFile($extractTo))
            displayError('Could not remove directory before Update: ' . $extractTo);
        else
            if($debug) displayMessage('Removed directory before Update: ' . $extractTo);
    }

    $startUpdate = true;
    // create directory to extract to    
    if(!file_exists($extractTo)) {
        if(!IOHelper::createDirectory($extractTo)) {
			displayError('Could not create update directory: ' . $extractTo);
			$startUpdate = false;
        }
		else {
			if($debug)
				displayMessage('Created directory for Update: ' . $extractTo);
		}
    }
    
    if($startUpdate)
    {
	    if(!is_writable($extractTo)) {
	        echo displayError('Not writeable: ' . $extractTo);
	    }

        $errors = unzip_unknown($zipName, $extractTo);

        foreach($errors as $err)
            displayError($err);

        if($debug) {
            foreach($files as $f)
                displayMessage($f);
        }
    	return true;
    }
    return false;
}

function unzip_unknown($zipName, $extractTo)
{
    if(function_exists('zip_open')) {
        return unzip_phpzip($zipName, $extractTo);
    }
    else {
        return unzip_simpleunzip($zipName, $extractTo);
    }
}

function unzip_simpleunzip($zipName, $extractTo)
{
    $errors = array();
    
    $unzip = new SimpleUnzip(UPDATE_PATH . $zipName);

    foreach($unzip->Entries as $oI) 
    {
        if($oI->Error == 0)
        {
            $fullpath = $extractTo . $oI->Path . DIR_SEPARATOR;
            
            clearstatcache();
    
            if(!file_exists($fullpath)) 
            {
                $paths = explode(ZIP_DIR_SEPARATOR,$oI->Path);
                $last =  '';
                foreach($paths AS $pathElement) {
                    $last .= $pathElement . DIR_SEPARATOR;
                    clearstatcache();
                    if(!file_exists($extractTo . $last)) 
                    {
                        if(!IOHelper::createDirectory($extractTo.$last))
                            $errors[] = 'Could not create directory: ' . $extractTo.$last;
                    }
                }
            }
            
            $filename = $fullpath . $oI->Name;
            
            if (!IOHelper::write_file($filename, $oI->Data))
                $errors[] = 'Failed writing content to File: ' . $filename;
        }
        else {
            displayError('Problems extracting: ' . $oI->ErrorMsg . '('.$oI->Path.DIR_SEPARATOR.$oI->Name.')');
        }
    }
    return $errors;      
}

function unzip_phpzip($zipName, $extractTo)
{
    $errors = array();
    
    $zip = zip_open(UPDATE_PATH . $zipName);
    if($zip === false)
        $errors[] = "Could not open ZIP file";
        
    if($zip !== false)
    {
        while($zipEntry = zip_read($zip)) 
        {
            $name = zip_entry_name($zipEntry);
            
            $fullpath = $extractTo . $name;

            $pos = strrpos($name, ZIP_DIR_SEPARATOR);

            clearstatcache();
            if(!file_exists($fullpath)) {
                $paths = explode(ZIP_DIR_SEPARATOR, $name);
                // only one file = directly in the base directory
                if(count($paths) > 1)
                {
                    $last =  '';
                    for($i=0; $i < count($paths)-1; $i++) {
                        $pathElement = $paths[$i];
                        $last .= $pathElement . DIR_SEPARATOR;
                        clearstatcache();
                        if(!file_exists($extractTo . $last)) {
                            if(!IOHelper::createDirectory($extractTo.$last))
                                $errors[] = 'Could not create directory: ' . $extractTo.$last;
                        }
                    }
                }
            }

            if(zip_entry_open($zip, $zipEntry, "r")) 
            {
                $writeFile = (zip_entry_filesize($zipEntry) > 0);
                if(!$writeFile) {
                    $pos = strrpos($name, ZIP_DIR_SEPARATOR);
                    if($pos != strlen($name)-1)
                        $writeFile = true;
                }

                if($writeFile) {
                    $contents = zip_entry_read($zipEntry, zip_entry_filesize($zipEntry));
                    if (!IOHelper::write_file($fullpath, $contents)) {
                        $errors[] = 'Failed writing (2) content to file: ' . $fullpath;
                    }
                }
                zip_entry_close($zipEntry);
                zip_entry_close($zipEntry);
            }
        }
        zip_close($zip);
    }
    
    return $errors;
}
