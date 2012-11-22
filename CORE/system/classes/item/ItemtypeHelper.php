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
 * @subpackage item
 */

/**
 * The ItemtypeHelper holds useful methods for matching
 * files (e.g. Uploads) against BIGACE Itemtypes.
 *
 * Have a look at the following config file:
 * <code>/system/config/mimetypes.php</code>
 *
 * It defines all allowed Filetypes and their Itemtype mapping.
 * If you cannot upload a special file, try to add a definition in
 * the config file.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage file
 */
class ItemtypeHelper
{
    private static $mimetypeInfo = null;

    /**
     * Returns an array of mimetype information.
     *
     * @return array(string=>array)
     */
    public static function getMimetypeInformation()
    {
        if (self::$mimetypeInfo === null) {
            self::$mimetypeInfo = include_once(_BIGACE_DIR_ROOT.'/system/config/mimetypes.php');
        }

        return self::$mimetypeInfo;
    }

    /**
     * Detectes the Mimetype for the given file.
     *
     * @return string|null
     */
    public static function getMimetypeForFile($filename)
    {
        $mimetypeDefinition = self::getMimetypeInformation();

        foreach ($mimetypeDefinition as $itemtype => $mimetypes) {
            $itemtype = substr($itemtype, 5);
            foreach ($mimetypes as $mimetypeDef => $extensions) {
                $ext = explode(',', $extensions);
                foreach($ext as $fileExtension) {
                    if ($fileExtension != '' && preg_match("/.".strtolower($fileExtension)."/i", strtolower($filename))) {
                        return $mimetypeDef;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Tries to find the Itemtype for the given Filename and/or Mimetype.
     * You can leave one of the parameter empty (pass null).
     *
     * @return integer|null
     */
    public static function getItemtypeForFile($filename = null, $mimetype = null)
    {
        $mimetypeDefinition = self::getMimetypeInformation();

        if($filename === null && $mimetype === null) {
            return null;
        }

        // try to find a matching mimetype
        if ($mimetype != null) {
            foreach ($mimetypeDefinition as $itemtype => $mimetypes) {
                $itemtype = substr($itemtype, 5);
                if(in_array($mimetype, array_keys($mimetypes))) {
                    return (int)$itemtype;
                }
            }
        }

        // try to find itemtype in configuration
        if ($filename === null) {
            return null;
        }

        foreach ($mimetypeDefinition as $itemtype => $mimetypes) {
            $itemtype = substr($itemtype, 5);
            foreach ($mimetypes as $mimetypeDef => $extensions) {
                // check file extension
                $ext = explode(',', $extensions);
                foreach($ext as $fileExtension) {
                    if ($fileExtension != '' && preg_match("/.".strtolower($fileExtension)."/i", strtolower($filename))) {
                        return (int)$itemtype;
                    }
                }
            }
        }

        return null;
    }

}