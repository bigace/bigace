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

import('classes.configuration.IniHelper');
 
/**
 * This class provides methods for loading Translations Resources.
 * 
 * The file can be of two kinds.
 * 
 * First is PHP File formatted like this:
 * <code>
 * &lt;?php
 * // a comment
 * $LANG['key'] = 'translation';
 * ...
 * ?&gt;
 * </code>
 * 
 * Second one is a properties file (INI format) like this:
 * <code>
 * ; a comment
 * key = "translation"
 * </code>
 * 
 * To load a ResourceBunlde type something like this:
 * <code>
 * BigaceResourceBundle::getBundle('foo');
 * </code> 
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util
 */
class BigaceResourceBundle
{
	private $translations = array(); 
	
	/**
	 * Fetches the Bundle with the given basename.
	 * If none Translation File could be found with this name it returns null.
	 * 
	 * This method should be accessed static!
	 */
	public static function getBundle($baseName, $locale = _ULC_, $directory = null)
	{
		$name = BigaceResourceBundle::getFileName($baseName, $locale, $directory);
		if($name != null)
		{
			$res = new BigaceResourceBundle();
			$res->loadTranslation($name);
			return $res;
		}
		return null;
	}
	
	/**
	 * Find the translation file.
	 */
	public static function getFileName($baseName, $locale = _ULC_, $directory = null)
	{
		$default = (isset($GLOBALS['_BIGACE']['DEFAULT_LANGUAGE']) ? $GLOBALS['_BIGACE']['DEFAULT_LANGUAGE'] . '/' : '');

		$names = array();
			      
		if($directory != null) {
			array_push($names, $directory. '/'. $locale . '/' . $baseName);
			array_push($names, $directory. '/'. $baseName . '_' . $locale);
			array_push($names, $directory. '/'. $baseName);
			array_push($names, $directory. '/'. $default . $baseName);
		}
		
		$names = array_merge($names, array(
		    _BIGACE_DIR_CID . 'language/' . '/'. $locale . '/' . $baseName,
            _BIGACE_DIR_CID . 'language/' . '/'. $baseName . '_' . $locale,
            _BIGACE_DIR_CID . 'language/' . '/'. $baseName,	
            _BIGACE_DIR_CID . 'language/' . '/'. $default . $baseName,
            _BIGACE_LANGUAGE_PATH. $locale . '/' . $baseName,
            _BIGACE_LANGUAGE_PATH. $baseName . '_' . $locale,
            _BIGACE_LANGUAGE_PATH. $baseName,
            _BIGACE_LANGUAGE_PATH. $default . $baseName
            )
        );
		
		foreach($names As $filename)
		{
			if(file_exists($filename . '.lang.php')) {
			    $GLOBALS['LOGGER']->logDebug('Found Translation File: ' . $filename . '.lang.php');
				return $filename . '.lang.php';
			}
			else if(file_exists($filename . '.properties')) {
			    $GLOBALS['LOGGER']->logDebug('Found Translation File: ' . $filename . '.properties');
				return $filename . '.properties';
			}
		}
	    $GLOBALS['LOGGER']->logError('Could not find translation: '.$baseName.', '.$locale.', '.$directory);
		return null;
	}
	// -------------------------------------------------------------
	
	/**
	 * Merge the translations from the given file into the current object.
	 * @return boolean
	 */
	function load($baseName, $locale = _ULC_, $directory = null)
	{
		$name = BigaceResourceBundle::getFileName($baseName, $locale, $directory);
		if($name != null) {
			return $this->loadTranslation($name);
		}
		return false;
	}

	/**
	 * Returns the internal translation Object.
	 * @access protected
	 */
	function getTranslation()
	{
		return $this->translations;
	}

	/**
	 * Loads the Translation file by adding all keys to the current namespace.
	 * Probably existing keys will be overwritten.
	 */
	private function loadTranslation($filename)
	{
        if( file_exists( $filename ) ) 
        {
        	if(strpos(strtolower($filename), '.php') === false) {
	 		    return $this->addTranslation(IniHelper::loadIniFile($filename, TRUE));
        	}
        	else {
				$LANG = array();
				 // do not use include_once - use same file in multiple context
				 // TODO sanitize filename
	            include( $filename );
	 		    return $this->addTranslation($LANG);
        	}
        }
        return false;
	}
	
	/**
	 * @return boolean whether we could add the translations or not
	 */
	private function addTranslation($trans = null)
	{
		if($trans != null) {
 		    $this->translations = array_merge($this->translations, $trans);
 		    return true;
		}
		return false;
	}
	
	/**
	 * Return the Translation String with the given Key.
	 * If this translation could not be found and <code>$fallback != null</code> this returns 
	 * <code>$fallback</code>, otherwise it returns <code>'???' . $key . '???'</code> 
	 */
	function getString($key, $fallback = null) 
	{
		if(isset($this->translations[$key]))
			return $this->translations[$key];
			
		if($fallback != null)
			return $fallback;
		
		return '???' . $key . '???';
	}
	
	/**
	 * TODO not implemented yet!
	 */
	function getFormattedString($key, $replacements)
	{
		return $this->getString($key) . ' => getFormattedString not implemented yet!';	
	}
	
}