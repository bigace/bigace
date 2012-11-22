<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.<br>Copyright (C) Kevin Papst.
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

import('classes.language.Language');
import('classes.util.IOHelper');

/**
 * The LanguageEnumeration is an Enumeration above all
 * registered System Languages.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage language
 */
class LanguageEnumeration
{
	/**
	 * @access private
	 */
	var $languages;
	/**
	 * @access private
	 */
	var $count = 0;

	function LanguageEnumeration()
	{
		$this->languages = IOHelper::getFilesFromDirectory(_BIGACE_LANGUAGE_PATH, 'ini', false);
		$this->count = count($this->languages);
	}

	/**
	 * @return int the amount of registered Languages
	 */
	function count() {
		return $this->count;
	}

	/**
	 * @return Language the next Language
	 */
	function next() {
	    return new Language(stripFileExtension(array_pop($this->languages)));
	}

}