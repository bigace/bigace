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

/**
 * This class MUST be used when creating a new Consumer.
 * It is a Bean that transports all values for a new Consumer to 
 * the Installer class.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage consumer
 */
class ConsumerDefinition
{
    /**
     * @access private
     */
    var $cid = '';
    /**
     * @access private
     */
    var $domain = '';
    /**
     * @access private
     */
    var $adminPassword = '';
    /**
     * @access private
     */
    var $adminUser = '';
    /**
     * @access private
     */
    var $adminStyle = '';
    /**
     * @access private
     */
    var $defaultEditor = '';
    /**
     * @access private
     */
    var $writeStatistics = '';
    /**
     * @access private
     */
    var $mailServer = '';
    /**
     * @access private
     */
    var $webmasterEmail = '';
    /**
     * @access private
     */
    var $defaultLanguage = '';
    /**
     * @access private
     */
    var $isDefault = false;
    /**
     * @access private
     */
    var $sitename = '';
    var $missing = null;
    
    function setID($cid) {
        $this->cid = $cid;
    }

    function getID() {
        return $this->cid;
    }    

    function setDomain($domain) {
        $this->domain = $domain;
    }
    
    function getDomain() {
        return $this->domain;
    }

    function setAdminPassword($adminPassword) {
        $this->adminPassword = $adminPassword;
    }

    function getAdminPassword() {
        return $this->adminPassword;
    }
    
    function setAdminUser($adminUser) {
        $this->adminUser = $adminUser;
    }

    function getAdminUser() {
        return $this->adminUser;
    }
    
    function setAdminStyle($adminStyle) {
        $this->adminStyle = $adminStyle;
    }

    function getAdminStyle() {
        return $this->adminStyle;
    }
    
    function setSitename($sitename) {
        $this->sitename = $sitename;
    }

    function getSitename() {
        return $this->sitename;
    }
    
    function setDefaultEditor($defaultEditor) {
        $this->defaultEditor = $defaultEditor;
    }

    function getDefaultEditor() {
        return $this->defaultEditor;
    }

    function setWriteStatistics($writeStatistics) {
        $this->writeStatistics = $writeStatistics;
    }

    function getWriteStatistics() {
        return $this->writeStatistics;
    }
    
    function setMailServer($mailServer) {
        $this->mailServer = $mailServer;
    }

    function getMailServer() {
        return $this->mailServer;
    }
    
    function setWebmasterEmail($webmasterEmail) {
        $this->webmasterEmail = $webmasterEmail;
    }
    
    function getWebmasterEmail() {
        return $this->webmasterEmail;
    }            

    function setDefaultLanguage($defaultLanguage) {
        $this->defaultLanguage = $defaultLanguage;
    }

    function getDefaultLanguage() {
        return $this->defaultLanguage;
    }

    /**
     * @param boolean isDefault whether this Consumer should be the default one
     */
    function setIsDefaultConsumer($isDefault) {
        $this->isDefault = $isDefault;
    }
    
    function getIsDefaultConsumer() {
        return $this->isDefault;
    }
    
    function isDefined() 
    {
        $this->missing = null;
        
        if ($this->domain == '')
            $this->missing = 'domain';

        if ($this->adminPassword == '')
            $this->missing = 'adminPassword';

        if ($this->adminUser == '')
            $this->missing = 'adminUser';

        if ($this->adminStyle == '')
            $this->missing = 'adminStyle';

        if ($this->defaultEditor == '')
            $this->missing = 'defaultEditor';

        if ($this->writeStatistics == '')
            $this->missing = 'writeStatistics';

        if ($this->defaultLanguage == '')
            $this->missing = 'defaultLanguage';
            
        return (is_null($this->missing));
    }
    
    function getMissingField()
    {
        return $this->missing;
    }

}
