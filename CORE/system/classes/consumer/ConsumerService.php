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

import('classes.consumer.Consumer');

/**
 * If a Domain was requested that has no Community mapping, we perform a lookup for
 * this ID to show a fallback Community, if configured.
 * If this Community does not exist either, we show the well-known Error Screen.
 */
define('DEFAULT_COMMUNITY', '*');

/**
 * This class provides basic methods for reading Consumer Settings.
 * 
 * ATTENTION:
 * This class handles the Domain Name always lower-case!
 * 
 * The Consumer Configuration File has the Format:
 * <code>
 * [www.example.com]
 * id = 0
 * </code>
 * You can define a Default Consumer by using the DEFAULT_COMMUNITY constant as Domain:
 * <code>
 * [*]
 * id = 0
 * </code>
 * A lookup for the default Community is done, if no mapping for the requested Domain
 * could be found.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage consumer
 */
class ConsumerService
{
    
    /**
     * @access private
     */
    private $_config = null;

    /**
     * @access private
     */
    private function _getCached($reload = false) 
    {
        if ($reload || is_null($this->_config)) {
            $this->_config = parse_ini_file(_BIGACE_DIR_ROOT.'/system/config/consumer.ini', TRUE);
        }
        return $this->_config;
    }
    
    /**
     * @access protected
     */
    protected function _expireCache() {
        $this->_config = null;
    }
	
   	/**
	 * Returns the Consumer ID for a Domain Name. 
	 * If no match could be found, it returns -1. 
	 * If no Consumer could be found at all (none installed maybe), it returns -2.
	 * It returns -3 if the Domain Name could not be processed (empty String or invalid Host URL).
	 * ATTENTION: This function handles Domain Names lower-case.
	 * 
	 * @param String the Domain Name to find the Consumer ID for
	 * @return int the Consumer ID or -1 if no match could be found 
	 */
	function getConsumerIdForDomain($domain) 
	{
		if (isset($domain) && $domain != '') {
			return $this->getConsumerConfigEntry($domain, 'id');
		}
		
		return -3;
	}
	
	/**
	 * Returns the Consumer Config Entry for the given Domain.
	 * 
	 * If no Consumer could be found at all (none installed maybe), it returns -2.
	 * It returns -1 if the Config Entry is not existing.
	 * 
	 * @param String the Consumer Domain
	 * @param String the Name of the Config Entry
	 * @return mixed the Config Value or -1 or -2
	 */
	function getConsumerConfigEntry($domain, $key) 
	{
		$config = $this->getConfigForDomain($domain);

		if(is_array($config) && isset($config[$key]))
			return $config[$key];

		return $config;
	}

	/**
	 * Finds the Consumer Config for the given Domain. 
	 * 
	 * @param String the domain to fetch the configuration for
	 * @param String the path to fetch the configuration for
	 */
	function getConfigForDomain($domain, $path = '') 
	{
		$CONSUMER = $this->loadConsumerConfiguration();
		if(count($CONSUMER) > 0)
		{
            $bestMatch = null;
            $pos = 999;
			$domain = $this->getDomainName($domain);
            $domain = $domain.$path;

			foreach($CONSUMER AS $k => $l) {
				// full match, return directly
				if(strcmp($domain, $k) == 0) {
					$l['domain'] = $k;
					return $l;
                }
				else if(($a = stripos($domain, $k)) !== false) {
                    // $a < $pos, so subdomains match better than domains - when path is the same in both
                    if($a < $pos || 
                        ($a == $pos && 
                            // if they are equal, the longer (deepest path) one counts
                            (is_null($bestMatch) || (strlen($k) > strlen($bestMatch['domain'])))
                        )
                    ) {
                        $pos = $a;
					    $l['domain'] = $k;
					    $bestMatch = $l;
                    }
				}
    		}

            if(!is_null($bestMatch))
                return $bestMatch;

			// fallback for old scenario, shouldn't be reached 
			if(isset($CONSUMER[$domain]) && is_array($CONSUMER[$domain])) {
				return $CONSUMER[$domain];
			}
	        return -1;
		}
		return -2;
	}
	
	/**
	 * Returns the complete Consumer Configuration for this System,
	 * including ALL settings for ALL Consumer.
	 * @return array the Consumer Configuration
	 */
	function loadConsumerConfiguration($reload = false) 
	{
        if ($reload) $this->_expireCache();
        return $this->_getCached($reload);
	}
	
	/**
	 * Gets all Domain names matching the given Consumer ID.
	 * Returns an empty array if the given CID could not be found.
	 * 
	 * @param int the Consumer ID
	 * @return array the Array with all Names matching the given CID
	 */
	function getNamesForID($cid) 
	{
		$names = array();
		$CONFIG = $this->loadConsumerConfiguration();
		foreach($CONFIG AS $cName => $entrys) {
			foreach($entrys AS $key => $value) {
				if($key == 'id' && $value == $cid) {
					array_push($names, $cName);
				} 
			}
		}
		
		return $names;
	}
 
	/**
	 * Returns the Domain Name, lower-case and not empty.
	 * @access private
	 */
	function getDomainName($domain = '') 
	{
		if ($domain == '') { 
			$domain = $_SERVER['HTTP_HOST'];
		}
		return strtolower($domain);
	}
    
    /**
     * Fetches a full configured Consumer for the given Domain or null 
     * if no Consumer could be found for this URL.
     * @param String domain the Domain Name to fetch the Consumer for 
     * @return Consumer the Consumer for the given Domain or null
     */
    function getConsumerByName($domain, $path = '') 
    {
        $domain = $this->getDomainName($domain);
		$config = $this->getConfigForDomain($domain, $path);

		// not found or none installed
		if(!is_array($config))
			$id = $config;
		else if(!isset($config['id']))
			$id = -1;
		else
	        $id = $config['id'];
    	    
	    // no need for any more work
        if ($id == -2) {
			import('classes.exception.ExceptionHandler');
			import('classes.exception.CoreException');
	        ExceptionHandler::processSystemException( new CoreException('consumer', 'No Community installed!') );
	        exit;
        } 
	        
        if ($id < 0 && $domain != DEFAULT_COMMUNITY) {
            // try to fetch default community and switch community name
            $config = $this->getConfigForDomain(DEFAULT_COMMUNITY);
			if(!is_array($config) || !isset($config['id'])) {
				import('classes.exception.ExceptionHandler');
				import('classes.exception.CoreException');
            	ExceptionHandler::processSystemException( new CoreException('consumer', "No Community found for '".$domain."'.") );
                exit;
            }
			$id = $config['id'];
        }
        
        if ($id > -1) {
            $alias = $this->getNamesForID($id);
			$domain = (($config['domain'] == DEFAULT_COMMUNITY) ? $_SERVER['HTTP_HOST'] : $config['domain']);
            return new Consumer($domain, $config, $alias);
        }
        return null;
    }
	
}

?>