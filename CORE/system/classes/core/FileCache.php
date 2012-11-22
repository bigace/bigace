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
 * @subpackage core
 */

/**
 * The default Itemtype for caching, if none was submitted.
 * We use an not existing Itemtype to be aware of confusion.
 */
define('_BIGACE_DEFAULT_CACHE_ITEMTYPE', '0');

/**
 * This core class handles File caching.
 * 
 * These functions provide simple mechanism for caching both item and non item information.
 * Use these functions if you have anythinf to do that should normally not been done on the fly.
 * 
 * For example you are going to rescale an Image, do it and then save it as cache File!
 * 
 * Remember to delete your Cache Files from time to time!
 *
 * Caching will be done in the subdirectory "cache" below your Consumer Home:
 * <code>/consumer/cid{CID}/cache/</code>.
 * Where {CID} must be replaced by your Consumer ID.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage core
 */
class FileCache
{
    /**
     * Checks if a Cache File exists for the given parameter combination.
     * @param int the Itemtype ID
     * @param int the ItemID
     * @param mixed the Options (that MUST be serializable - like simple arrays or Strings)
     * @return boolean whether the requested Item exists for given Options, or not
     */
    static function existsItemCacheFile($itemtype, $itemid, $options = '')
    {
        return file_exists(FileCache::_createItemCacheName($itemtype, $itemid, $options));
    }
    
    /**
     * Creates a Cache File.
     *
     * @return boolean if Cache File could be created or not
     */
    static function createItemCacheFile($itemtype, $itemid, $content, $options = '')
    {
        return FileCache::_writeCacheFile(FileCache::_createItemCacheName($itemtype, $itemid, $options), $content);
    }
    
    /**
     * Reads the content from Cache File.
     *
     * @return mixed content if file exists, else false!
     */    
    static function getItemCacheContent($itemtype, $itemid, $options = '')
    {
        return FileCache::_readCacheFile( FileCache::_createItemCacheName($itemtype, $itemid, $options) );
    }
    
    static function get_cache_url($name,$options = '') {
    	return FileCache::_createItemCacheName(_BIGACE_DEFAULT_CACHE_ITEMTYPE, $name, $options);
    }
    
    /**
     * Removes physically the Cache File from the filesystem
     * @return boolean whether this cached Item File could be removed or not
     */
    static function deleteItemCacheFile($itemtype, $itemid, $options = '')
    {
        return FileCache::_unlinkCacheFile ( FileCache::_createItemCacheName($itemtype, $itemid, $options) );
    }
    
    /**
     * Works as existsItemCacheFile(_BIGACE_DEFAULT_CACHE_ITEMTYPE, $name, $options)
     *
     * @see existsItemCacheFile
     */
    static function existsCacheFile($itemid, $options = '')
    {
        return FileCache::existsItemCacheFile(_BIGACE_DEFAULT_CACHE_ITEMTYPE, $itemid, $options);
    }
    
    /**
     * Tries to expire all cached Files for the given Itemtype/ItemID combination.
     * We do not care about the Options while creating the Cache Files.
     */
    static function expireAllCacheFiles($itemtype, $itemid) {
        if ($dh = opendir($GLOBALS['_BIGACE']['DIR']['cache'])) 
        {
            $name = FileCache::_createItemCachePreFileName($itemtype, $itemid);
            while ($file = readdir($dh))  {
                
                if ($file != '.' && $file != '..' && substr_count($file, ".bigace.cache") > 0 && substr_count($file, $name) > 0) {
                    FileCache::_unlinkCacheFile($GLOBALS['_BIGACE']['DIR']['cache'] . $file);
                }
            }
            closedir($dh);
        }
    }
    
    /**
     * Works as createItemCacheFile(_BIGACE_DEFAULT_CACHE_ITEMTYPE, $name, $options, $content)
     *
     * @see createItemCacheFile
     */
    static function createCacheFile($name, $content, $options = '')
    {
        return FileCache::createItemCacheFile(_BIGACE_DEFAULT_CACHE_ITEMTYPE, $name, $content, $options);
    }
    
    
    /**
     * Works as deleteItemCacheFile(_BIGACE_DEFAULT_CACHE_ITEMTYPE, $name, $options)
     * 
     * @see deleteItemCacheFile
     */
    static function deleteCacheFile($name, $options = '')
    {
        return FileCache::deleteItemCacheFile(_BIGACE_DEFAULT_CACHE_ITEMTYPE, $name, $options);
    }
    
    /**
     * Works as getItemCacheContent(_BIGACE_DEFAULT_CACHE_ITEMTYPE, $name, $options)
     *
     * @see getItemCacheContent
     */
    static function getCacheContent($name, $options = '')
    {
        return getItemCacheContent(_BIGACE_DEFAULT_CACHE_ITEMTYPE, $name, $options);
    }
    
    /**
     * Creates a Unique Cache Name. But remember, this name is only unique for a Combination of Itemtype, ItemID and Options.
     * It these are submitted twice with the same settings, it will return the same key.
     * @access private
     * @return the unique cache name for the given parameter combination
     */
    static function _createItemCacheName($itemtype, $itemid, $options = '') 
    {
        return $GLOBALS['_BIGACE']['DIR']['cache'] . FileCache::_createItemCachePreFileName($itemtype, $itemid) . '_' . md5(serialize($options)) . '.bigace.cache';
    }
    
    /**
     * @access private
     * @return the unique cache name for the given parameter combination
     */
    static function _createItemCachePreFileName($itemtype, $itemid) 
    {
        return $itemtype . '_' . $itemid;
    }
    
    /**
     * @access private
     */
    static function _readCacheFile($name)
    {
        if (!($fh = fopen($name, "r"))) {
            return false;
        }
    
        $data = fread($fh, filesize($name));
        fclose($fh);
        return $data;
    }
    
    /**
     * Writes content into a named cache File.
     * Make sure the name is the unique cache name!
     * @access private
     */
    static function _writeCacheFile($name, $content)
    {
        if (!$fh = fopen($name, "w")) {
            return false;
        }
    
        flock($fh,LOCK_EX);
        fwrite($fh, $content);
        flock($fh,LOCK_UN);
        fclose($fh);
        return true;
    }
    
    
    /**
     * Trys to unlink the given cache file. 
     * @access private
     */
    static function _unlinkCacheFile($file)
    {
        if (file_exists($file)) {
            return unlink($file);
        }
        return false;
    }
    
    
    /**
     * This fluishes the entire Cache. 
     * Before using this function aks yourself: "Do I really want to delete all Cache Files?
     * Answer: YES - allright, go ahed.
     * Answer: NO/DON'T KNOW - forget it, the result might be a performance leak within Scripts 
     * that extensively use caching as Performance Strategy. Try to remove Cache Files by explicit Identifiers.
     */
    static function doFullGarbageCollection()
    {
        if ($dh = opendir($GLOBALS['_BIGACE']['DIR']['cache'])) 
        {
            while ($file = readdir($dh)) 
            {
                if ($file != '.' && $file != '..' && substr_count($file, ".bigace.cache") > 0 ) {
                    FileCache::_unlinkCacheFile($file);
                }
            }
            closedir($dh);
        }
    }    
}

?>