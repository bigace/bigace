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
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.classes
 * @subpackage consumer
 */

import('classes.configuration.IniHelper');
import('classes.consumer.ConsumerService');

/**
 * This class provides extended methods for manipulating Consumer Settings.
 *  
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage consumer
 */
class ConsumerHelper extends ConsumerService
{
	
	/**
	 * This function sets the given Community as Default.
	 */
	function setDefaultConsumer($domain)
	{
		return $this->duplicateConsumerValues($domain, DEFAULT_COMMUNITY);
	}
    
    /**
     * @see getConsumerByName(DEFAULT_COMMUNITY)
     */
    function getDefaultConsumer() 
    {
    	return $this->getConsumerByName(DEFAULT_COMMUNITY);
    }

    function removeDefaultConsumer() 
    {
    	return $this->removeConsumerByDomain(DEFAULT_COMMUNITY);
    }
	
	/**
	 * Sets an Array of Consumer Config Entrys and saves it afterwards.
	 * Returns TRUE on success otherwise FALSE.
	 * 
	 * ATTENTION: Each entry that is empty will be removed!
	 * If you want to remove one of the Consumer Config Entrys, simply
	 * use <code>addConsumerConfig($domain, array('foo' => ''))</code>. 
	 * 
	 * @param String the Domain Name
	 * @param Array the Values to set
	 * @return boolean TRUE on success, otherwise FALSE
	 */
	function addConsumerConfig($domain, $values) 
	{
		if ($domain != '' && is_array($values)) 
		{
			$domain = strtolower($domain);
			$CONFIG = $this->loadConsumerConfiguration();
			foreach($values AS $key => $value) {
				$CONFIG[$domain][$key] = $value;	
			}
			$GLOBALS['LOGGER']->logDebug('Trying to write Consumer Config for Domain: ' . $domain);
			return $this->writeConfig($CONFIG);
		}
		return FALSE;
	}

    /**
     * Activates all Consumer Mappings for the given Consumer ID.
     */
    function activateConsumer($cid)
    {
        return $this->setConsumerValues($cid, 'active', (int)true);
    }
    
    /**
     * Deactivates all Consumer Mappings for the given Consumer ID.
     */
    function deactivateConsumer($cid)
    {
        return $this->setConsumerValues($cid, 'active', (int)false);
    }

    /**
     * Sets the Key-Value pair for all Consumers matching the given CID.
     */
    function setConsumerValues($cid, $newKey, $value)
    {
        $CONFIG = $this->loadConsumerConfiguration();
        foreach($CONFIG AS $cName => $entrys) {
            foreach($entrys AS $key => $val) {
                if($key == 'id' && $val == $cid) {
                    $CONFIG[$cName][$newKey] = $value;
                    $GLOBALS['LOGGER']->logDebug('Set Value "'.$value.'" for Key "'.$newKey.'" for Consumer by Name ('.$cName.') for ID: '.$cid.'.');
                } 
            }
        }
        // clear cache
        return $this->writeConfig($CONFIG);
    }

    /**
     * Copies all Key-Value pairs from $domain to $newDomain.
     * @return boolean true on success, otherwise false 
     */
    function duplicateConsumerValues($domain, $newDomain)
    {
        $CONFIG = $this->loadConsumerConfiguration();
        if (isset($CONFIG[$domain]))
        {
            $CONFIG[$newDomain] = $CONFIG[$domain];
            return $this->writeConfig($CONFIG);
        } 
        return false;
    }
	
	/**
	 * Removes a complete Consumer from the Configuration.
	 * You have to submit a Consumer ID!
	 * 
	 * @param int the Consumer ID
	 * @return boolean TRUE on success, otherwise FALSE
	 */
	function removeConsumerByID($cid) 
	{
		$names = $this->getNamesForID($cid);
		
		if (count($names) > 0)
		{
			$CONFIG = $this->loadConsumerConfiguration();
			foreach($names AS $name) 
			{
				$GLOBALS['LOGGER']->logDebug('Removing Consumer (ID: '.$cid.',Name: '.$name.') from Consumer Config.');
				unset($CONFIG[$name]);
			}
			return $this->writeConfig($CONFIG);
		}
		// could not delete from Consumer config
		return FALSE;
	}
	
	/**
	 * Removes a complete Consumer from the Configuration.
	 * You have to submit the Domain Name!
	 * 
	 * @param Stringt the Consumers Domain to remove
	 * @return boolean TRUE on success, otherwise FALSE
	 */
	function removeConsumerByDomain($name)
	{
		$CONFIG = $this->loadConsumerConfiguration();
		if (isset($CONFIG[$name])) 
		{
			unset($CONFIG[$name]);
			$GLOBALS['LOGGER']->logDebug('Removing Consumer ('.$name.') from Consumer Config.');
			return $this->writeConfig($CONFIG);
		}
		$GLOBALS['LOGGER']->logDebug('Could not find Consumer ('.$name.') to be removed.');
		return FALSE;		
	}

	/**
	 * Returns all currently existing Consumer IDs within the System.
	 * If none is installed an empty array is returned!
	 * 
	 * @return array all IDs as array
	 */
	function getAllConsumerIDs() 
	{
		$ids = array();
		$CONFIG = $this->loadConsumerConfiguration();
		foreach($CONFIG AS $cName => $entrys) {
			if (isset($entrys['id']) && array_search($entrys['id'],$ids) === false) {
				array_push($ids, $entrys['id']);
			}
		}
		return $ids;
	}

    /**
     * This function checks if there is a Consumer existing wihtin the System.
     * @return boolean TRUE if there is at least one Consumer, otherwise FALSE
     */
    function isConsumerExisting()
    {
        return (count($this->loadConsumerConfiguration()) > 0);
    }
    
	/**
	 * Returns all currently existing Consumer Names within the System.
	 * If none is installed an empty array is returned!
	 * 
	 * @return array all Consumer Names as array
	 */
	function getAllConsumerNames() 
	{
		$names = array();
		$CONFIG = $this->loadConsumerConfiguration();
		foreach($CONFIG AS $cName => $entrys) {
			array_push($names, $cName);
		}
		return $names;
	}
		
	/**
	 * Writes the given Config Array to the Consumer Config File.
	 * @access private
	 */
	function writeConfig($CONFIG) 
	{
		$comment = 'Written at: ' . date("F j, Y, g:i a");
		$GLOBALS['LOGGER']->logDebug('Trying to write Consumer Config File!');
        $this->_expireCache();
		return IniHelper::write_ini_file(_BIGACE_DIR_ROOT.'/system/config/consumer.ini', $CONFIG, $comment, TRUE);
	}
	
	/**
	 * Get an associative Array with all existing Consumer.
	 * Arrays key is Consumer ID and value the <code>Consumer</code>. 
	 */
	function getAllConsumer() 
	{
		$allConsumer = array();
		foreach($this->getAllConsumerNames() AS $domain)
		{
			if($domain != DEFAULT_COMMUNITY) {
				$consumer = $this->getConsumerByName($domain);	
				if(!isset($allConsumer[$consumer->getID()])) {
					$allConsumer[$consumer->getID()] = $consumer;
				}
			}
		}
		return $allConsumer;
	}

	/**
	 * Returns an Consumer by its ID. If the Consumer does not
	 * exist it returns null.
	 * @return Consumer the Consumer or null
	 */
	function getConsumerByID($id)
	{
		$names = $this->getNamesForID($id);
		if(count($names) > 0)
			return $this->getConsumerByName($names[0]);
		return null;			
	}

}

?>