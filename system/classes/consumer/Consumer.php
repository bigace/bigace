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
 
/**
 * This class is a wrapper that should not be instantiated!
 * It is a simple container that is returned by the <code>ConsumerService</code>.
 *  
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage consumer
 */
class Consumer
{
    /**
     * @access private
     */
    private $values;
    /**
     * @access private
     */
    private $domain;
    /**
     * @access private
     */
    private $alias;
    
    function Consumer($domain, $values, $alias) 
    {
        if ($domain == '') {
            $domain = $_SERVER['HTTP_HOST'];
        }
            
        $this->values = $values;
        $this->alias = $alias;
        $this->domain = strtolower($domain);
    }
    
    /**
     * Returns the ID for this Consumer.
     * @return int the Consumer ID
     */
    function getID() {
        return $this->_getSetting('id', null);
    }
    
    /**
     * Get the Domain for this Consumer.
     * @return String the Domain Name
     */
    function getDomainName() {
        return $this->domain;
    }
    
    /**
     * @return array an Array with all Alias Names for this Consumer
     */
    function getAlias() {
        return $this->alias;
    }
    
    /**
     * Return whether this Consumer is activated or not.
     */
    function isActivated() {
        return (bool)$this->_getSetting('active', true);
    }
    
    /**
     * Returns the Filename where this Consumer saves its Maintenance Message.
     * @return String the Filename for the Maintenance HTML
     */
    function getMaintenanceFilename() {
        return _BIGACE_DIR_CID . 'config/maintenance.html';
    }
    
    /**
     * Get the HTML that should be displayed if the Consumer is deactivated.
     * @return String the HTML Message
     */
    function getMaintenanceHTML() {
        if(file_exists($this->getMaintenanceFilename()))
            return file_get_contents($this->getMaintenanceFilename());
        return '';
    }

    /**
     * Return the base path of this consumer.
     */
    function getPath() {
        return $this->_getSetting('path', '');
    }

    /**
     * Returns th language for this community if configured, otherwise null.
     *
     * @return string the locale or null
     */
    function getLanguage() {
        return $this->_getSetting('language', null);
    }
    
    /**
     * @access private
     */
    private function _getSetting($key, $fallback) {
        if(isset($this->values[$key]))
            return $this->values[$key];

        return $fallback;
    }
	
}

?>