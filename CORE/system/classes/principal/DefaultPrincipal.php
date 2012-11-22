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
 * @subpackage principal
 */

import('api.principal.Principal');

/**
 * This represents a Principal within the internal User Database.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage principal
 */
class DefaultPrincipal extends Principal
{
    /**
     * @access private
     */
    var $userx;
    /**
     * @access private
     */
    var $valid = true;
    
    /**
    * Initializes the Object with the User by the given ID
    *
    * @param    int the User ID
    */
    function DefaultPrincipal($id)
    {
        if (strlen($id) < 1) {
            $id = _AID_;
        }
        
	    $values = array( 'USER_ID'  => $id,
	                     'CID'      => _CID_ );
        $sqlString = "SELECT * FROM {DB_PREFIX}user WHERE id={USER_ID} and cid={CID}";
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
	    $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	    if ($res->isError()) {
	    	$this->valid = false;
	    } else {
        	$this->userx = $res->next();
       	}
    }

    /**
     * This shows if the User is registered within BIGACE.
     * It returns true even if the Users status is inactive!
     * 
     * @return boolean true if user is known
     */
    function isValidUser() {
        return $this->valid;
    }


    /**
     * Gets the Information if user is anonymous.
     *
     * @return boolean if User is anonymous
     */
    function isAnonymous() {
        if ($this->isValidUser() && $this->getID() == _AID_) {
            return true;
        }
        return false;
    }
    
    function isSuperUser() {
        if ($this->isValidUser() && $this->getID() == _BIGACE_SUPER_ADMIN) {
                return true;
        }
        return false;
    }


    /**
     * Gets the Users status within BIGACE.
     * If User is not known it will return the same as isValidUser().
     *
     * @return boolean value for the Users status.
     */
    function isActive() {
        if ($this->isValidUser()) {
            if ($this->getID() == _BIGACE_SUPER_ADMIN || ((int) $this->getActiveStatusID()) > 0) {
                return true;
            }
        }
        return false;
    }


    /**
     * Gets the Status ID as it is hold in the DB
     *
     * @return   int     the Status ID for the initialized User
     */
    function getActiveStatusID() {
        return $this->userx["active"];
    }

    /**
     * Returns the Users email adress.
     *
     * @return String the users email
     */
    function getEmail() {
        return $this->userx["email"];
    }
    
    /**
     * Gets the Users ID
     * @return int the Users ID
     */
    function getUserID() {
        return $this->userx["id"];
    }

    /**
     * Gets the Users ID
     * @return int the Users ID
     */
    function getID() {
        return $this->userx["id"];
    }

    /**
     * Gets the User name.
     *
     * @return   String  the Username in BIGACE
     */
    function getName() {
        return $this->userx["username"];
    }

    /**
     * Gets the Language ID for this User.
     *
     * @return   int     the language ID
     */
    function getLanguageID() {
        return $this->userx["language"];
    }

    /**
     * Fetches the users consumer ID.
     *
     * @return   int     the consumer ID
     */
    function getCID() {
        return $this->userx["cid"];
    }    
    
}
