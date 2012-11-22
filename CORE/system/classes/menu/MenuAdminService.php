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
 * @subpackage menu
 */

import('classes.item.ItemAdminService');
import('classes.item.ItemHelper');
import('classes.menu.Menu');

/**
 * Class used for administrating BIGACE "Menu" Items
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage menu
 */
class MenuAdminService extends ItemAdminService
{
    private $COPY_CONTENT   = 1;
    private $EMPTY_CONTENT  = 2;
    private $minWordLength = 4;

    /**
    * Instantiates a MenuAdminService.
    */
    function __construct() {
        $this->initItemAdminService(_BIGACE_ITEM_MENU);
        $this->minWordLength = ConfigurationReader::getConfigurationValue("search", "minimum.word.length", $this->minWordLength);
    }

    /**
     * More comfort for using the menu api.
     *
     * @access private
     */
    function prepareMenuDataArray($data) {
        // more comfort for using the menu api
        if(isset($data['template']) && !isset($data['text_4']))
            $data['text_4'] = $data['template'];
        if(isset($data['modul']) && !isset($data['text_3']))
            $data['text_3'] = $data['modul'];
        if(isset($data['module']) && !isset($data['text_3']))
            $data['text_3'] = $data['module'];

        return $data;
    }

    /**
    * Creates Menu with given values.
    *
    * @return the new Menu ID or FALSE
    */
    function createMenu($data)
    {
        if (isset($data['langid']))
        {
            $data['text_2'] = rawurlencode($data['name']);
            //file name
            $data['text_1'] = '';
            // set mimetype
            $data['mimetype'] = (isset($data['mimetype'])) ? $data['mimetype'] : 'text/html';

            $data = $this->prepareMenuDataArray($data);

            // create database item
            $new_id = $this->createItem($data);

            // if this failed stop...
            if($new_id === false)
                return false;

            // change the modifieddate to the one within the menus name
            // and the menu name to the one we will create later on
            $timestamp = time();
            //create name
            $filename = ItemHelper::buildInitialFilenameDirectory($this->getDirectory(), 'html', $new_id, $data['langid'], $timestamp);
            // update item with name and modified date AND possible plugins that catch the 'update_item' event
            $this->_changeItemLanguageWithTimestamp($new_id, $data['langid'], array('text_1' => $filename), $timestamp);

            // save menus content in a file
            if(isset($data['content']))
                ItemHelper::saveContent($this->getDirectory().$filename, $data['content']);
            else
                ItemHelper::saveContent($this->getDirectory().$filename, '');

            return $new_id;
        }
        return false;
    }

    function createItemLanguageVersion($id, $copyLangID, $data)
    {
        if (isset($data['langid']))
        {
            $data['text_2'] = rawurlencode($data['name']);
            $data['mimetype'] = (isset($data['mimetype'])) ? $data['mimetype'] : 'text/html';

            $requestResult = $this->createLanguageVersion($id, $copyLangID, $data);
            $filename = $requestResult->getMessage();

            return ItemHelper::saveContent($this->getDirectory().$filename, '');
        }
        else
        {
            return false;
        }
    }

    /**
     * This method prepares the Content and Meta information to be saved correctly.
     * If you update Menu Content, always use this method and NOT the generic method <code>updateItemContent(...)</code>!
     *
     * - Absolute links will be replaced by relative ones
     * - Removes all possible Session IDs from links and image tags
     * - Prepares the Search Content
     *
     * After all then Menu will be updated with these Informations, including timestamp and author.
     */
    function updateMenuContent($id, $langid, $content, $data = array())
    {
        $data = $this->prepareMenuDataArray($data);

        // prepare the content to be indexed
        $searchIndexer = $this->_getRealTextContent($content);
        if(!isset($data['text_5'])) {
            $data['text_5'] = '';
        }
        $data['text_5'] .= ' ' . $searchIndexer;

        // replace absolute links with relative ones to be able to switch easily to a different host
        $content = str_replace('src="'.$GLOBALS['_BIGACE']['DOMAIN'],'src="/',$content);
        $content = str_replace('href="'.$GLOBALS['_BIGACE']['DOMAIN'],'href="/',$content);

        // remove possible session information from links
        $sessString = bigace_session_name() . '=' . bigace_session_id();
        $pos = strpos($content, $sessString);
        if($pos !== false) {
            $content = str_replace('?' . $sessString . '"', '"', $content);
            $content = str_replace('&' . $sessString . '"', '"', $content);
            $content = str_replace('?' . $sessString . '&', '?', $content);
            $content = str_replace('&' . $sessString . '&', '&', $content);
        }

        // now go and update the item
        return $this->updateContent($id, $langid, $content, $data);
    }

    /**
     * Overwritten to make sure to get each update.
     */
    function updateItemContent($id, $langid, $content, $data = array())
    {
        return $this->updateMenuContent($id, $langid, $content, $data);
    }

    /**
     * overwritten to add proper file extension
     * @access protected
     */
    function buildUniqueName($name, $extension = '.html', $delim = '_') {
        return parent::buildUniqueName($name, ConfigurationReader::getConfigurationValue('seo', 'menu.default.extension', $extension));
    }

    /**
     * Used for getting the real text from the html content.
     * The result will be used by stored within the DB and then used for the Search.
     * Make sure to store only important data (remove html tags)!
     * @access private
     */
    function _getRealTextContent($content)
    {
        // make strings like "hello<br/>world" to "hello <br/> world" for better split results
        $content = str_replace('>', '> ', $content);
        $content = str_replace('<', ' <', $content);

        // now get rid of all tags, which we do not want to be searchable
        $content = strip_tags($content);

        // defines the characters that should be replaced by a space for better split results.
        // these character are normally used for splitting words or complete sentences
        $unwantedChars = array('.','!','(',')',':',',','.','?',"'",'"','/','\r\n','\r','\n');

        $newContent = str_replace($unwantedChars, ' ', $content);

        /*
        // the parsed content result will be kept here
        $newContent = '';

        // split content by space
        $allWords = split(" ", $content);

        foreach($allWords AS $singleWord)
        {
            if(strlen($singleWord) >= $this->minWordLength) {
                $newWord = str_replace($unwantedChars, ' ', $singleWord);
                $newWords = split(" ", $newWord);
                foreach($newWords AS $newSingleWord)
                {
                    // remove all probably left whitespaces, after removing all slashes
                    $newSingleWord = trim( stripslashes($newSingleWord) );
                    if(strlen($newSingleWord) >= $this->minWordLength) {
                        $newContent .= $newSingleWord . ' ';
                    }
                }
            }
        }
        */

        // handle html tags...
        // $newContent = html_entity_decode($newContent);


        return $newContent;
    }

}