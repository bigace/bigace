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
 * Holds static helper methods for building informations out of Items and/or for Items.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage item
 */
class ItemHelper
{

    /**
     * @return String the Items History Version full name including directory
     */
    static function getHistoryURLFull($item)
    {
        return ItemHelper::getHistoryURLFullForTS($item, $item->getLastDate());
    }

    /**
     * @return String the Items History Version full name including directory for timestamp
     */
    static function getHistoryURLFullForTS($item, $timestamp)
    {
        return ItemHelper::buildHistoryURLComplete($item->getDirectory(), getFileExtension($item->getUrl()), $item->getID(), $item->getLanguageID(), $timestamp, $item->getURL());
    }

    /**
     * @return String the Items History Version full name without directory for timestamp
     */
    static function getHistoryURLForTS($item, $timestamp)
    {
        return ItemHelper::buildHistoryURLComplete('', getFileExtension($item->getUrl()), $item->getID(), $item->getLanguageID(), $timestamp, $item->getURL());
    }

    /**
     * @return String the Items History Version full name without directory
     */
    static function getHistoryURL($item)
    {
        return ItemHelper::buildHistoryURLComplete('', getFileExtension($item->getUrl()), $item->getID(), $item->getLanguageID(), $item->getLastDate());
    }

    /**
     * @access private
     */
    static function buildHistoryURLComplete($directory, $extension, $itemid, $languageid, $modifieddate)
    {
        return ItemHelper::buildURLComplete($directory, $extension, $itemid, $languageid, $modifieddate);
    }

    // -------------------------------------------

    static function buildURLComplete($directory, $extension, $itemid, $languageid, $timestamp = '')
    {
        return $directory . ItemHelper::buildInitialFilename($extension,$itemid,$languageid,$timestamp);
    }

    /**
     * Build a Filename that can be used for new Items or Language Versions.
     * This method gurantees to return a Filename that is unique within the given
     * Directory and that can be used for creating new Files.
     * It returns the new Filename WITHOUT the directory.
     */
    static function buildInitialFilenameDirectory($directory, $extension, $id = '', $langid = '', $timestamp = '') {

        $name = ItemHelper::buildInitialFilename($extension, $id, $langid, $timestamp);

        while (file_exists($directory.$name)) {
            $name = ItemHelper::buildInitialFilename($extension, $id, $langid, time());
        }

        return $name;
    }


    /**
     * Build a Filename that can be used for new Items or Language Versions.
     * If Item ID or Language ID is not passed, a random string will be generated,
     * if the timestamp is missing, it uses the current timestamp.
     *
     * @param extension the File Extension (for example 'html')
     * @param id the Item ID or an empty String
     * @param langid the Language ID or an empty String
     * @param timestamp the Timestamp or an empty String
     */
    static function buildInitialFilename($extension, $id = '', $langid = '', $timestamp = '') {
        $pre = '';

        if ($langid == '' || $id == '') {
            $pre = getRandomString();
        } else {
            $pre = $id . '_' . $langid;
        }

        if($timestamp == '') {
            $timestamp = time();
        }

        return $pre . '_' . $timestamp . '.' . $extension;
    }

    // -------------------------------------------

    static function getFutureURLFull($item)
    {
        return ItemHelper::buildFutureURLComplete($item->getDirectory(), getFileExtension($item->getUrl()), $item->getID(), $item->getLanguageID(), $item->getURL());
    }

    static function getFutureURL($item)
    {
        return ItemHelper::buildFutureURLComplete('', getFileExtension($item->getUrl()), $item->getID(), $item->getLanguageID(), $item->getURL());
    }

    static function buildFutureURLComplete($directory, $extension, $itemid, $languageid)
    {
        return ItemHelper::buildURLComplete($directory, $extension, $itemid, $languageid, 'future');
    }

    // ------------------------- STATIC METHODS -------------------------

    /**
     * Static method to prepare a SQL Substring.
     * @return array an Array with SQL Replacern
     */
    static function prepareSqlValues($id, $langid, $values = array())
    {
        $sql = "";

        if (isset($values['name']))        { $sql .= ", name='".ItemHelper::fixSqlValue($values['name'])."' "; }
        if (isset($values['mimetype']))    { $sql .= ", mimetype='".$values['mimetype']."' "; }
        if (isset($values['description'])) { $sql .= ", description='".ItemHelper::fixSqlValue($values['description'])."' "; }
        if (isset($values['parentid']))    { $sql .= ", parentid='".$values['parentid']."' "; }
        if (isset($values['catchwords']))  { $sql .= ", catchwords='".ItemHelper::fixSqlValue($values['catchwords'])."' "; }
        if (isset($values['workflow']))    { $sql .= ", workflow='".$values['workflow']."' "; }
        if (isset($values['activity']))    { $sql .= ", activity='".$values['activity']."' "; }
        if (isset($values['unique_name'])) { $sql .= ", unique_name='".$values['unique_name']."' "; }

        for ($i=1;$i<6;$i++) {
            if (isset($values['text_'.$i])) { $sql .= ", text_".$i."='".ItemHelper::fixSqlValue($values['text_'.$i])."' "; }
        }

        for ($i=1;$i<6;$i++) {
            if (isset($values['num_'.$i])) {
                if(is_null($values['num_'.$i]) || strlen($values['num_'.$i]) == 0)
                    $sql .= ", num_".$i."=null ";
                else
                    $sql .= ", num_".$i."='".ItemHelper::fixSqlValue($values['num_'.$i])."' ";
            }
        }

        for ($i=1;$i<6;$i++) {
            if (isset($values['date_'.$i])) { $sql .= ", date_".$i."='".ItemHelper::fixSqlValue($values['date_'.$i])."' "; }
        }

        $val = array('ITEM_ID'     => $id,
                     'LANGUAGE_ID' => $langid,
                     'VALUES'      => $sql,
                     'USER_ID'     => $GLOBALS['_BIGACE']['SESSION']->getUserID(),
                     'TIMESTAMP'   => time());

        return $val;
    }

    static function fixSqlValue($str)
    {
        $str = strip_tags($str);
        return addSlashes( $str ) ; //str_replace('"', '&quot;', $str) );
    }

    /**
     * Static method to check if the given File is acessable and writeable.
     * @return boolean if file could be written
     */
    static function checkFile($filename)
    {
        // if the file exist we check if it is writeable
        if(file_exists($filename)) {
            return (is_file($filename) && is_writeable($filename));
        }

        // if it currently does not exist, we check if it could be created
        $path_parts = pathinfo($filename);
        return (is_dir($path_parts["dirname"]) && is_writeable($path_parts["dirname"]));
    }

    /**
     * Saves the Content to the given File, by replacing the old File content.
     */
    public static function saveContent($filename, $content)
    {
        if (ItemHelper::checkFile($filename))
        {
            $GLOBALS['LOGGER']->logDebug("Writing content to file '" . $filename . "'");
            import('classes.util.IOHelper');
            return IOHelper::write_file($filename, $content);
        }
        $GLOBALS['LOGGER']->logError("File '" . $filename . "' is not writeable!");
        return false;
    }

}