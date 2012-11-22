<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.<br>Copyright (C) Kevin Papst.
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
 * @subpackage administration
 */

/**
 * A AdminRequestResult can be used as Generic Result Type for Admin requests.
 * Currently it is used in the ItemAdminService.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage administration
 */
class AdminRequestResult
{
    /**
     * @access private
     */
    var $resultMsg = '';
    /**
     * @access private
     */
    var $result = false;
    /**
     * @access private
     */
    var $values = array();
    
    function AdminRequestResult($success, $msg = '') {
        $this->setIsSuccessful($success);
        $this->setMessage($msg);
    }
    
    function getMessage() {
        return $this->resultMsg;
    }

    function setMessage($msg) {
        return $this->resultMsg = $msg;
    }

    function isSuccessful() {
        return $this->result;
    }

    function setIsSuccessful($success) {
        return $this->result = $success;
    }
    
    function getID() {
        return $this->getValue('id');
    }

    function setID($id) {
        $this->setValue('id', $id);
    }
    
    function getName() {
        return $this->getValue('name');
    }

    function setName($name) {
        $this->setValue('name', $name);
    }
    
    /**
    * Sets any result value. 
    * Used for settings that may be passed and do not match any given Method like Name or ID.
    */ 
    function setValue($key, $value) {
        $this->values[$key] = $value;
    }
    
    /**
    * Gets any result value. 
    */
    function getValue($key) {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }
}

?>