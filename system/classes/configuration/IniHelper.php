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
 * @subpackage configuration
 */

/**
 * This class provides Helper methods for handling Ini Files.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage configuration
 */
class IniHelper
{
	/**
	 * Writes an array to an Ini File.
	 * Knows how to handle Subarrays and how to keep global variables even
	 * if they appear after an subarray.
	 *
	 * @param String the full qualified Filename
	 * @param array the Array to save as Ini File
	 * @param String Comment line at the beginning of the File
	 * @param boolean if set to TRUE each empty key will be left out
	 * @return boolean true on success, false on error
	 */
	static function write_ini_file($filename, $assoc_array, $comment = '', $removeEmptyKeys = FALSE)
	{
	   $content = '';
	   $sections = '';

	   // add comments if set
	   if (is_array($comment)) {
	   	 foreach($comment AS $line)
	   		$content .= '; ' . $line . "\n";
	   } else {
	   	 $content .= '; ' . $comment . "\n";
	   }

	   foreach ($assoc_array as $key => $item)
	   {
	       if (is_array($item))
	       {
	           $sections .= "\n[{$key}]\n";
	           foreach ($item as $key2 => $item2)
	           {
		       	   if(strlen($item2) != 0 || !$removeEmptyKeys) {
		               if (is_numeric($item2) || is_bool($item2))
		                   $sections .= "{$key2} = {$item2}\n";
		               else
		                   $sections .= "{$key2} = \"{$item2}\"\n";
		       	   }
	           }
	       }
	       else
	       {
	       	   if(strlen($item) != 0 || !$removeEmptyKeys) {
		           if(is_numeric($item) || is_bool($item))
		               $content .= "{$key} = {$item}\n";
		           else
		               $content .= "{$key} = \"{$item}\"\n";
	       	   }
	       }
	   }

	   $content .= $sections;

       import('classes.util.IOHelper');
       return IOHelper::write_file($filename, $content);
	}

	/**
	 * Loads an Ini File.
	 *
	 * @param String Name of the Ini File to load
	 * @param boolean whether to parse Sections within the Ini File or not
	 */
	public static function loadIniFile($filename, $process_sections = FALSE)
	{
		if($GLOBALS['LOGGER']->isDebugEnabled()) {
			$GLOBALS['LOGGER']->logDebug('Parsing Ini File at "'.$filename .'" with Process-Sections: ' . $process_sections);
		}
		return parse_ini_file($filename, $process_sections);
	}

}