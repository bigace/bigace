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
 * @package bigace.api
 * @subpackage principal
 */

/**
 * The Parameter to set the Principals email adress.
 */
define('PRINCIPAL_PARAMETER_EMAIL', 'EMAIL');
/**
 * The Parameter to mark a Principal as active.
 */
define('PRINCIPAL_PARAMETER_ACTIVE', 'ACTIVE');
/**
 * The Parameter to mark a Principals Language.
 */
define('PRINCIPAL_PARAMETER_LANGUAGE', 'LANGUAGE');
/**
 * The Parameter to mark a Principals Password.
 */
define('PRINCIPAL_PARAMETER_PASSWORD', 'PASSWORD');

/**
 * This Service Interface holds methods for loading and manipulating Prinicpals.
 * 
 * Receive an PrincipalService instance by calling:
 * <code>
 * $services = ServiceFactory::get();
 * $principalService = $services->getPrincipalService();
 * </code>
 * 
 * Make sure to use the right funtion to set User values:
 * 
 * setAttributes() for all Metadata like the values within the User Admin Plugin.
 * setParemeter() for special values like User Language and active Flag.
 *  
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.api
 * @subpackage principal
 */
class PrincipalService
{

    function PrincipalService()
    {
    }

    /**
     * Returns the Principal Attributes as a key-value mapped Array.
     * If no Attributes could be found it returns an empty array.
     *
     * @return array the Principal Attributes
     */
    function getAttributes($principal) {
        return array();
    }
    
    /**
     * Sets the attribute-value mapping for the given Principal. 
     * @return boolean true on success otherwise false
     */
    function setAttribute($principal, $attribute, $value) {
        return false;
    }
    
    /**
     * Deletes all attributes for the given Principal.
     * @return boolean true on success otherwise false
     */
    function deleteAttributes($principal) {
    	/*     * If second parameter is null or an empty array all attributes will be removed,
     * otherwise we try to remove all Attributes provided in the array:
     * <code>array('phone','street')</code>
    */	
        return false;
    }
    
    /**
     * Get an Array with all available Principals.
     * @return array an array with Principal instances
     */
    function getAllPrincipals() {
        return array();
    }
    
    /**
     * Tries to find a Principal with the given Name.
     * Returns null if none could be found.
     * @return mixed a Principal or null
     */
    function lookup($principalName) {
        return null;
    }

    /**
     * Tries to find a Principal with the given ID.
     * Returns null if none could be found.
     * @return mixed a Principal or null
     */
    function lookupByID($principalID) {
        return null;
    }
    
    /**
     * Creates a Principal.
     * Returns false if the Principal could not be created.
     * @return mixed the Principal or false
     */
    function createPrincipal($name, $password, $language) {
        return false;
    }
    
    /**
     * Deletes a Principal.
     * Returns false if the Principal could not be deleted.
     * @return boolean true on success otherwise false
     */
    function deletePrincipal($principal) {
        return false;
    }
    
    /**
     * Sets the given Parameter for the Principal.
     * 
     * The allowed Parameter are:
     * - PRINCIPAL_PARAMETER_PASSWORD
     * - PRINCIPAL_PARAMETER_ACTIVE
     * - PRINCIPAL_PARAMETER_LANGUAGE
     * 
     * @return boolean true on success otherwise false
     */
    function setParameter($principal, $parameter, $value) {
        return false;
    }

    /**
     * Gets the given Parameter for the Principal.
     * If the passed Parameter could not be found or is invalid
     * null will be returned.
     * 
     * The allowed Parameter are:
     * 
     * - PRINCIPAL_PARAMETER_PASSWORD
     * - PRINCIPAL_PARAMETER_ACTIVE
     * - PRINCIPAL_PARAMETER_LANGUAGE
     * 
     * @return mixed the value or null if not found
     */
    function getParameter($principal, $parameter) {
        return null;
    }

}

?>