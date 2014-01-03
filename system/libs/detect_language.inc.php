<?php
//
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS                                           |
// +------------------------------------------------------------------------+
// | Copyright (c) Kevin Papst                                              |
// | Web           http://www.bigace.de                                     |
// | Sourceforge   http://sourceforge.net/projects/bigace/                  |
// +------------------------------------------------------------------------+
// | This source file is subject to version 2 or (at your option) any later |
// | version, of the GNU General Public License as published by the Free    |
// | Software Foundation, available at:                                     |
// | http://www.gnu.org/licenses/gpl.html                                   |
// +------------------------------------------------------------------------+
// | BIGACE is distributed in the hope that it will be useful,              |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// +------------------------------------------------------------------------+
//

/**
 * Helper function for accepted browser languages and for fniding out
 * languages to be used for language independent item calls. 
 * 
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @deprecated DO NOT USE ANY LONGER!
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @version $Id$
 * @package bigace.libs
 */

import('classes.language.LanguageEnumeration');

/**
 * Returns an array of locales, which the users browser has configured.
 * The first value is the most wanted language...
 */
function get_accept_browser_languages() 
{
	$langs = array();
	if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && strlen($_SERVER['HTTP_ACCEPT_LANGUAGE']) > 0)
	{
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			// break up string into pieces (languages and q factors)
			preg_match_all('/([a-z]{2}(-[a-z]{2})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);

			if (count($lang_parse[1])) {
				// create a list like "en" => 0.8
				$langs = array_combine($lang_parse[1], $lang_parse[4]);
			
				// set default to 1 for any without q factor
				foreach ($langs as $lang => $val) {
				    if ($val === '') $langs[$lang] = 1;
				}

				// sort list based on value	
				arsort($langs, SORT_NUMERIC);
			}
		}
	}
	return $langs;
}

/** 
 * Checks the browser accepted locales against the array of given locales.
 * The best match that can be found is returned.
 */
function get_preferred_language($available)
{
	$langs = get_accept_browser_languages();

	// look through sorted list and use first one that matches our languages
	foreach ($langs as $lang => $val) {
		foreach($available AS $check) {
			if (strpos($lang, $check) === 0) {
				return $check;
			} 
		}
	}
	return null;
}

/**
 * Returns the language that should be used for a bigace url without language parameter.
 */
function get_accepted_language($fallback) 
{
	$available = array();

	$le = new LanguageEnumeration();
	for($i=0; $i < $le->count(); $i++) {
		$l = $le->next();
		$available[] = $l->getLocale();
	}

	$useLang = get_preferred_language($available);
	if(is_null($useLang)) {
		return $fallback;
	}
	return $useLang;
}

?>