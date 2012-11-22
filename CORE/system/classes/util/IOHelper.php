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
 * @subpackage util
 */


/**
 * This class provides static helper methods for Filesystem IO.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util
 */
class IOHelper
{
    /**
     * Tries to delete the given File or Directory.
     * Directories will be deleted recursive. If any file could not be deleted,
     * the method stops immediately and returns false.
     * If all files could be deleted, it returns true.
     * You should perofrm a check for file_exists() after an delete attempt.
     * @return boolean whether all files could be deleted (recursive) or not
     */
    static function deleteFile($filename) {
        $fullName = $filename;
        if(file_exists($fullName)) {
            if(is_file($fullName)) {
                if(!is_writable($fullName)) {
                    return false;
                } else {
                    if(!@unlink($fullName))
                        return false;
                }
            }
            else if(is_dir($fullName)) {
	        	$handle=opendir($fullName);
	        	while (false !== ($file = readdir ($handle))) {
	        		if($file != "." && $file != "..") {
                        //stderr('Found: ' . str_replace('//','/',$filename.'/'.$file));
                        IOHelper::deleteFile(str_replace('//','/',$filename.'/'.$file));
                    }
                }
      		    closedir($handle);
                if(!is_writable($fullName)) {
                    return false;
                } else{
                    if(!@rmdir($fullName))
                        return false;
                }
            }
        }
        return true;
    }

    /**
	 * Returns an Array of all Files from a given Directory with the defined File Extension.
	 * If no File Extension is given we return all found Files.
	 * If the Last parameter is set, we return the full Filename including the Directory,
	 * otherwise we return only the File name itself.
	 */
	public static function getFilesFromDirectory($directory, $fileExtension = '', $includeDir = true)
	{
		$allFiles = array();
		if (is_dir($directory)) {
			$handle=opendir($directory);
			while (false !== ($file = readdir ($handle))) {
				if(is_file($directory . $file) && $file != "." && $file != "..")
				 if ($fileExtension == '' || getFileExtension($file) == $fileExtension) {
				    if ($includeDir) {
				        $file = $directory . $file;
				    }
			    	array_push($allFiles, $file);
				}
			}
			closedir($handle);
		}
		return $allFiles;
	}

    /**
     * Creates a directory with the preconfigured rights.
     * Returns TRUE if the Directory already exists OR if the Directoty was created!
     */
    static function createDirectory($name, $rights = _BIGACE_DEFAULT_RIGHT_DIRECTORY, $mask = _BIGACE_DEFAULT_UMASK_DIRECTORY)
    {
        $success = true;
        if (!file_exists($name))
        {
            $oldumask = umask($mask);
            if (!@mkdir($name, $rights))
            {
                $success = false;
                $GLOBALS['LOGGER']->logError( 'Failed creating Directory: ' . $name );
            }
            umask($oldumask);
        }
        return $success;
    }


	static function get_file_contents($file)
	{
		if(!file_exists($file)) {
			$GLOBALS['LOGGER']->logError('Cannot return File Content, File is missing: ' . $file);
			return FALSE;
		}

 		if (function_exists('file_get_contents')) return file_get_contents($file);

		$f = fopen($file,'r');
		if (!$f) return '';
		$t = '';

		while ($s = fread($f,100000)) $t .= $s;
			fclose($f);

		return $t;
	}

    static function write_file($filename, $content)
    {
    	$done = false;
        if($handle = @fopen($filename, 'wb'))
        {
            if ($content === null) {
                $done = true;
            } else {
                if (@fwrite($handle, $content) !== false) {
                	$done = true;
                }
            }

            @fclose($handle);
            @chmod($filename, _BIGACE_DEFAULT_RIGHT_FILE);
        }

        return $done;
    }


}

// deprecated method
function createDirectory($name, $rights = 0, $mask = _BIGACE_DEFAULT_UMASK_DIRECTORY)
{
    $GLOBALS['LOGGER']->logError( 'USING DEPRECATED METHOD createDirectory, CALL IOHelper::createDirectory INSTEAD!' );
    return IOHelper::createDirectory($name, $rights, $mask);
}



// ----------------------------------------------------------------------------------
// -------------------------------- TO BE REFACTORED --------------------------------
// ----------------------------------------------------------------------------------


/**
* Returns the File Extension excluding the Dot Separator "."
*/
function getFileExtension($filename) {
    $temp = explode('.', $filename);
    $which = count($temp);
    if (isset($temp[$which-1])) {
        return @$temp[$which-1];
    }
    return false;
}

/**
* Returns File Name without Extension.
*/
function getNameWithoutExtension($filename) {
    $temp = explode('.', $filename);
    if (isset($temp[0])) {
        return $temp[0];
    }
    return $filename;
}

/**
 * Splits a Directory Name by the Delimitier /.
 */
function splitDirectoryName($filename, $count = 0) {
    $temp = explode('/', $filename);
    $which = count($temp);
    $name = '';
    for ($a = 0; $a < $which-$count; $a++) {
        $name .= $temp[$a] . '/';
    }
    return $name;
}

/**
 * Returns the Filename without the File Extension, which is identified
 * by the Last Dot (.) separator .
 */
function stripFileExtension($filename) {
    $temp = explode('.', $filename);
    $which = count($temp);
    return $temp[0];
}


/**
 * Copies a File. Tries to set the correct Rights and Owner.
 * @return boolean TRUE if the Copy Command processed successful, otherwise FALSE
 */
function copyFile($from, $to, $rights = '')
{
    $success = FALSE;
    if ($rights == '') {
        $rights = _BIGACE_DEFAULT_RIGHT_DIRECTORY;
    }
    $oldumask = umask(_BIGACE_DEFAULT_UMASK_FILE);
    if (copy($from, $to)) {
        $success = TRUE;
        if(!chmod($to, $rights))
    		$GLOBALS['LOGGER']->logError( 'Could not chmod ('.$rights.'): ' . $to );
    }
    umask($oldumask);
    return $success;
}
