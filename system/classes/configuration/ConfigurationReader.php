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
 * @subpackage configuration
 */

import('classes.configuration.ConfigurationEntry');

/**
 * This class provides methods for reading Database driven Configurations.
 * It is designed to be used as static class, to avoid instantiation.
 * <br>
 * Use it as follows:
 * <code>
 * $bar = ConfigurationReader::getConfigurationEntry('foo', 'bar');
 * </code>
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage configuration
 */
class ConfigurationReader
{
    
    /**
     * Gets all Configuration Values available.
     * Calls will never be cached.
     * @return array all Configurations as ConfigurationEntry objects in an Array
     */
    public static function getAll() 
    {
        $values = array();
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadAndPrepareStatement('configuration_read_all', $values);
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $results = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        return ConfigurationReader::getArrayFromResult($results);
    }

    /**
     * All Value of the Package will be fetched and returned
     * as an Array of <code>ConfigurationEntry</code> instances.
     * 
     * This method call caches its Results (if not NULL).
     * <br>
     * If a Package was read, Calls against the same package within the methods
     * <code>getConfigurationEntry()</code> and <code>getConfigurationValue()</code> 
     * will fetch their results from this cached Package.
     * <br>
     * If you want to read more than ONE entry/value of a Package it is always
     * recommended to call <code>getPackage($package)</code> first to fill the cache and speed 
     * up the Calls (less DB calls).
     * 
     * @param String the Package Name to fetch
     * @return array an Array of ConfigurationEntry Objects with keys of Config Names
     */
    public static function getPackage($package) 
    {
        $res = ConfigurationReader::cachePackage($package);
        if($res == NULL)
        {
	        $values = array('PACKAGE' => $package);
	        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadAndPrepareStatement('configuration_read_package', $values);
	        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
	        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	        if ($res != NULL) {
    	        $res = ConfigurationReader::getArrayFromResult($res, true);
	        	ConfigurationReader::cachePackage($package, $res);
	        }
        }
        
        return $res;
    }
    
    /**
     * Fetch a known ConfigurationEntry from a Configuration Package.
     * 
     * @param String the Package Name to fetch
     * @param String Name of the Configuration Name to fetch
     * @return ConfigurationEntry the ConfigurationEntry Object or null
     */
    public static function getConfigurationEntry($package, $name) 
    {
        $res = ConfigurationReader::cacheEntry($package, $name);
        if($res == NULL)
        {
            $values = array('PACKAGE' => $package, 'NAME' => $name);
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadAndPrepareStatement('configuration_read_entry', $values);
            $result = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
            $res = ConfigurationReader::getEntryFromResult($result);
            if ($res != NULL)
            	ConfigurationReader::cacheEntry($package, $name, $res);
        }
        return $res;
    }

    /**
     * @deprecated see ConfigurationReader::getValue($package, $name, $undefined)
     */
    public static function getConfigurationValue($package, $name, $undefined = null)
    {
    	return ConfigurationReader::getValue($package, $name, $undefined);
    } 
    
    /**
     * Returns the Value of a ConfigurationEntry, or <code>$undefined</code> if the 
     * requested Configuration Entry could not be found.
     * 
     * No need of explicit casting for:
     * - CONFIG_TYPE_BOOLEAN
     * 
     * @param String package the Parameter Package
     * @param String name the Parameter Name
     * @param mixed undefined fallback value for not found Configuration Entry
     * @return mixed the Value or NULL
     */
    public static function getValue($package, $name, $undefined = null)
    {
        $entry = ConfigurationReader::getConfigurationEntry($package, $name);
        if ($entry != NULL)
        {
	        if ($entry->getType() == CONFIG_TYPE_BOOLEAN) {
	            return (boolean)$entry->getValue();
	        }
	        else if ($entry->getType() == CONFIG_TYPE_CLASSNAME) {
	        	return createInstance($entry->getValue());
	        }
	        return $entry->getValue();
        }
        $GLOBALS['LOGGER']->logError("Configuration could not be found. Package '".$package."', Name '".$name."'");
        return $undefined;
    }
    
    /**
     * Sets or returns a Cache Entry.
     * @access private
     */
    public static function cacheEntry($package, $name, $entry = NULL) 
    {
        static $configs;
        if ($entry == NULL) 
        {
        	// if entry is cached return it
            if (isset($configs[$package][$name])) {
                return $configs[$package][$name];
            }
            
            // check if package is already cached and return entry from there
            $cachedPackage = ConfigurationReader::cachePackage($package);
            if ($cachedPackage != NULL && isset($cachedPackage[$name])) {
            	return $cachedPackage[$name];
            }
        } 
        else {
            $configs[$package][$name] = $entry;
        }
        return NULL;
    }

    /**
     * Sets or returns a Cache Entry.
     * @access private
     */
    public static function cachePackage($package, $entrys = NULL) 
    {
        static $packageConfigs;
        if ($entrys == NULL) 
        {
            if (isset($packageConfigs[$package]))
                return $packageConfigs[$package];
        } 
        else 
        {
            $packageConfigs[$package] = $entrys;
        }
        return NULL;
    }
    
    /**
     * Create a Entry from a Database Result.
     * @access private
     */
    public static function getEntryFromResult($result) 
    {
        if (!$result->isError() && $result->count() > 0) 
        {
            return new ConfigurationEntry($result->next());
        }
        return NULL;
    }

    /**
     * Create an Bunch of Entrys from a Database Result.
     * @access private
     */
    public static function getArrayFromResult($result, $assoc = false) 
    {
        $temp = array();
        for ($i=0; $i < $result->count(); $i++) {
            $r = ConfigurationReader::getEntryFromResult($result);
            if ($r != NULL)
            {
                if ($assoc == true)
                    $temp[$r->getName()] = $r;
                else
                    array_push($temp, $r);
            }
        }
        return $temp;
    }
}