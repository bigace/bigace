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
 * @subpackage language
 */

/**
 * This represents a Language.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage language
 */
class Language 
{
	/**
	 * @access private
	 */
	private $language = array();
	
    /**
     * Loads the specfied Language by its Locale.
     * @param locale the Language Locale
     */
	function Language($locale)
	{
		if(is_null($locale) || strlen($locale) == 0) {
	        $GLOBALS['LOGGER']->logError("Attempt to load language for empty locale", true);
		}
		else {
		    static $languageCache;
		    if(isset($languageCache[$locale])) {
		        $this->language = $languageCache[$locale];
		    }
		    else {
		        $name = _BIGACE_LANGUAGE_PATH . $locale . '.ini';
		        if (file_exists($name)) {
		            $languageCache[$locale] = parse_ini_file($name,true);
		            $this->language = $languageCache[$locale];
		        }
		        else {
		            $GLOBALS['LOGGER']->logError("Failed loading Language '$locale'!");
		        }
		    }

		    // fallback for old language definitions
		    if(!isset($this->language['administration'])) {
		        $this->language['administration'] = false;
		    }
		}
	}
	
	/**
	 * Returns whether this language is valid.
	 * @return boolean
	 */
	function isValid() {
		return (count($this->language) > 0);
	}
	/**
	 * Returns the ID of this Language.
	 * Better try to use <code>getLocale()</code> instead, cause Locales are System independent.
     * @return String the Language Locale
	 */
	function getID() {
		return $this->getLocale();
	}

	/**
	 * @see getLocale()
	 * @deprecated use <code>getLocale()</code> instead.
	 */
	function getLanguageID() {
		return $this->getLocale();
	}

	/**
	 * Returns the Name of this Language in the requested Locale.
     * If no Locale is passed, the current User Locale is used!
     * See <code>_ULC</code>.
	 * @return String the Language name
	 */
	function getLanguageName($locale = '')
	{
        if ($locale == '' && defined('_ULC_')) $locale = _ULC_;
        if ($locale != '' && isset($this->language["translations"]["name_".$locale]))
            return $this->language["translations"]["name_".$locale];
		return $this->language["name"];
	}
	
	/**
	 * Returns the default Name of this Language, normally the Name of the Language
	 * written in the language itself.
	 * @return String the default Name of the Language
	 */
	function getDefaultLanguageName() {
		return $this->language["name"];
	}

	/**
	 * @see getLanguageName()
     * @return String the Language name
	 */
	function getName($locale = '') {
	    return $this->getLanguageName($locale);
	}
	
	/**
	 * Returns the Locale of this Language.
	 * @return String the Locale of this Language
	 */
	function getLocale() {
		return $this->language["locale"];
	}
	
	/**
	 * @see getLocale()
	 */
	function getShortLocale() {
	    return $this->getLocale();
	}

	/**
	 * Might returns the same as <code>getLocale()</code>, but can return
	 * a longer version, like locale_Country.
	 * @return String the Full Locale of this Language
	 */
	function getFullLocale() {
		return $this->language["full"];
	}

	/**
	 * Returns the Character Set this Language uses. 
	 * This is normally the HTML encoding used in the Browser.
	 * @return String the encoding to use for this language
	 */
	function getCharset() {
		return $this->language["charset"];
	}

	/**
	 * Get the translation Key for this Language. 
	 * Use this methods result for loading Language Files.
	 * @return String the Identifier to load Language Files for 
	 */
	function getTranslation() {
		return $this->language["translation"];
	}
	
    /**
     * Returns whether this Language can be used to display 
     * the Administration. 
     * @return boolean if this Lanugage has Core translations for the Admin Panel 
     */
    function isAdminLanguage() {
        return (bool)$this->language['administration'];
    }

	/**
	 * Returns the Locale.
	 */
	function toString() {
		return $this->getLocale();
	}

}
