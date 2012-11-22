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
 
import('classes.util.BigaceResourceBundle');
 
/**
 * Translations is a Class that provides helper methods for accessing Translation
 * Files.
 * It provides namespaces for easy handling of several translation files and has
 * static accessor methods!
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util
 */
class Translations
{
	
	/**
	 * Returns a translation object or null.
	 * @return BigaceResourceBundle or null
	 */
	public static function get($baseName, $locale = _ULC_, $namespace = null, $directory = null)
	{
		if($namespace == null) {
			 $namespace = 'bigaceglobal';
		}
	
		if(!isset($GLOBALS['_BIGACE']['namespace#'.$namespace])) {
			Translations::load($baseName, $locale, $namespace, $directory);
		}
		
		if(isset($GLOBALS['_BIGACE']['namespace#'.$namespace])) {
			return $GLOBALS['_BIGACE']['namespace#'.$namespace];
		}
		
		return null;
	}
	
	/**
	 * Loads a Translation into the given Namespace.
	 * If no Locale is passed, we take the current User Locale.
	 * If no Namespace is provided, we take the global Namespace.
	 * If you want to search within a different directory, than the default ones,
	 * you can submit an absolute Path.
	 */
	public static function load($baseName, $locale = _ULC_, $namespace = null, $directory = null)
	{
		if($namespace == null) {
			 $namespace = 'bigaceglobal';
		}
	
		if(isset($GLOBALS['_BIGACE']['namespace#'.$namespace])) {
			return $GLOBALS['_BIGACE']['namespace#'.$namespace]->load($baseName, $locale, $directory);
		}
		else {
			$res = BigaceResourceBundle::getBundle($baseName, $locale, $directory);
	
			if($res != null) {
				$GLOBALS['_BIGACE']['namespace#'.$namespace] = $res;
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Loads a Translation into the Global Namespace.
	 */
	public static function loadGlobal($baseName, $locale = _ULC_, $directory = null)
	{
		return Translations::load($baseName, $locale, 'bigaceglobal', $directory);
	}
	
	/**
	 * Fetches a translation from the Global Namespace.
	 */
	public static function translateGlobal($key, $fallback = null)
	{
		return Translations::translate($key, 'bigaceglobal', $fallback);
	}
	
	/**
	 * Fetches a Translation from the requested Namespace.
	 * If no match could be found, it returns the <code>fallback</code>.
	 * If this is not submitted (what is suggested for missing key search), it
	 * returns <code>'??'.$namespace.'#'.$key.'??'</code>.
	 */
	public static function translate($key, $namespace = 'bigaceglobal', $fallback = null)
	{
			if(isset($GLOBALS['_BIGACE']['namespace#'.$namespace]))
				return $GLOBALS['_BIGACE']['namespace#'.$namespace]->getString($key, $fallback);
			
		if($fallback != null)
			return $fallback;
			
		return '??'.$namespace.'#'.$key.'??';
	}

}