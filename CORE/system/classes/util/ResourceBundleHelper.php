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
 * This class provides methods for manipulating and saving translations.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util
 */
class ResourceBundleHelper
{

    /**
     * @return array an array where each entry is a new comment line
     */
    function getComment($locale = null) {
        $comment = array();
        $comment[] = 'Translation file for: ';
        if($locale != null) {
            $comment[] = 'Locale: ' . $locale;
        }
        $comment[] = "";
        $comment[] = 'For further information go to http://www.bigace.de/';
        $comment[] = "";
        $comment[] = '@package bigace.translation';
        $comment[] = "\n";
        return $comment;
    }

    /**
     * Saves an associative Array as translation INI file.
     * If the target File is existing, it will be overwritten!
     */
    function saveArrayAsConsumerBundle($basename, $locale, $assocEntrys)
    {
        return $this->saveArrayAsBundle(_BIGACE_DIR_CID . 'language/', $basename, $locale, $assocEntrys);
    }

    /**
     * Saves an associative Array as translation INI file.
     * If the target File is existing, it will be overwritten!
     */
    function saveArrayAsSystemBundle($basename, $locale, $assocEntrys)
    {
        return $this->saveArrayAsBundle(realpath(dirname(__FILE__).'/../../language/'), $basename, $locale, $assocEntrys);
    }

    /**
     * @access private
     */
    function saveArrayAsBundle($path, $basename, $locale, $assocEntrys)
    {
        if($locale != null)
            $path .= '/' . $locale . '/';
        return IniHelper::write_ini_file($path . $basename . '.properties', $assocEntrys, $this->getComment($locale), FALSE);
    }
}
