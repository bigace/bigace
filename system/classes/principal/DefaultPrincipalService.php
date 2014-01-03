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
 
import('classes.core.ServiceFactory');
import('api.principal.PrincipalService');
import('classes.principal.DefaultPrincipal');

/**
 * Default implementation of the BIGACE PrincipalService.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage principal
 */
class DefaultPrincipalService extends PrincipalService
{

    function DefaultPrincipalService()
    {
    }

    /**
     * Returns an Array with the the Principal Attributes key-value mapped.
     * If no Attributes could be found it returns an empty array.
     *
     * @return array the Principal Attributes
     */
    function getAttributes($principal) {
        $attributes = array();
        $values = array( 'USER_ID' => $principal->getID() );
        $sqlString = "SELECT attribute_name, attribute_value FROM {DB_PREFIX}user_attributes WHERE userid={USER_ID} and cid={CID}";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        for($i=0; $i < $res->count(); $i++) {
            $userdata = $res->next();
            $attributes[$userdata['attribute_name']] = $userdata['attribute_value']; 
        }
        return $attributes;
    }
    
    /**
     * Sets the attributes for the given Principal. 
     * @return boolean true on success otherwise false
     */
    function setAttribute($principal, $attribute, $value) {
        $values = array( 'USER_ID'          => $principal->getID(),
                         'ATTRIBUTE_VALUE'  => $value,
                         'ATTRIBUTE_NAME'   => $attribute );
        $sqlString = "REPLACE INTO {DB_PREFIX}user_attributes SET attribute_value={ATTRIBUTE_VALUE}, attribute_name={ATTRIBUTE_NAME}, userid={USER_ID}, cid={CID}";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        if($res->isError()) {
            $GLOBALS['LOGGER']->logError('Failed setting attribute ('.$attribute.'=>'.$value.') for user ('.$principal->getID().')');
            return false;
        }
        return true;
    }
    
    /**
     * Deletes the attributes for the given Principal.
     * @return boolean true on success otherwise false
     */
    function deleteAttributes($principal) {
        $values = array( 'USER_ID' => $principal->getID() );
        $sqlString = "DELETE FROM {DB_PREFIX}user_attributes WHERE userid={USER_ID} and cid={CID}";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        return !$res->isError();
    }
    
    /**
     * Get an Array with all available Principals.
     * @return array an array with Principal instances
     */
    function getAllPrincipals() {
        $principals = array();
        $values = array( 'EXCLUDE' => _BIGACE_SUPER_ADMIN );
        $sqlString = "SELECT * FROM {DB_PREFIX}user WHERE id<>{EXCLUDE} AND cid={CID}";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $alluser = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        // FIXME - anonymous user???
        for($i=0; $i < $alluser->count(); $i++) {
            $temp = $alluser->next();
            if($temp["id"] != _BIGACE_SUPER_ADMIN)
                $principals[] = new DefaultPrincipal($temp["id"]); 
        }
        return $principals;
    }

    /**
     * Tries to find a Principal with the given Name.
     * Returns null if none could be found.
     * @return mixed a Principal or null
     */
    function lookup($principalName) {
        $values = array( 'NAME'     => $principalName,
                         'CID'      => _CID_ );
        $sqlString = "SELECT id FROM {DB_PREFIX}user WHERE cid={CID} AND username = {NAME}";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        if ($res->count() > 0)  {
            $temp = $res->next();
            return $this->lookupByID($temp['id']);
        }
        return null;
    }
    
    /**
     * Tries to find a Principal with the given ID.
     * Returns null if none could be found.
     * @return mixed a Principal or null
     */
    function lookupByID($principalID) {
        $p = new DefaultPrincipal($principalID);
        if($p->isValidUser())
            return $p;
        return null;
    }
    
    /**
     * Returns an array with all Principals where the attribute-value pair matches.
     * The array can be empty if none could be found.
     * The lookup will ONLY find EXACT matches of the value!
     * @return array an array of Principals
     */
    function lookupByAttribute($attribute, $value) {
        $values = array( 'ATTRIBUTE_VALUE'  => $value,
                         'ATTRIBUTE_NAME'   => $attribute,
                         'CID'      		=> _CID_ );
        $sqlString = "SELECT a.id FROM {DB_PREFIX}user a, {DB_PREFIX}user_attributes b WHERE b.cid={CID} AND b.attribute_name={ATTRIBUTE_NAME} AND b.attribute_value={ATTRIBUTE_VALUE} AND a.cid={CID} AND a.id = b.userid";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $ps = array();
        if ($res->count() > 0)  {
        	for($i=0; $i < $res->count(); $i++) {
	            $temp = $res->next();
	            $p = $this->lookupByID($temp['id']);
	            if($p != null)
	            	$ps[] = $p; 
        	}
        }
        return $ps;
    }

    /**
     * Creates a Principal.
     * Returns false if the Principal could not be created.
     * @return mixed the Principal or false
     */
    function createPrincipal($name, $password, $language) {

		$services = ServiceFactory::get();
		$AUTHENTICATOR = $services->getAuthenticator();
    	
        $values = array( 'NAME'     => $name,
                         'PASSWORD' => $AUTHENTICATOR->createHash($password),
                         'LANGUAGE' => $language );
        $sqlString = "INSERT INTO {DB_PREFIX}user (cid, username, password, language) VALUES ({CID}, {NAME}, {PASSWORD}, {LANGUAGE})";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        
        $result = $GLOBALS['_BIGACE']['SQL_HELPER']->insert( $sqlString );
        if($result === FALSE) {
            $GLOBALS['LOGGER']->logError('Could not create user (Name: ' . $name . ', Language: ' . $language . '), see previous errors.');
            return false;
        }
        $GLOBALS['LOGGER']->logInfo('Created user (Name: ' . $name . ', Language: ' . $language . ')');
        return $this->lookup($name);
    }
    
    /**
     * Deletes a Principal.
     * Returns false if the Principal could not be deleted.
     * 
     * This will not work for the BIGACE and ANONYMOUS USER!
     * We do not have to delete any right, because each is in a Group 
     * and the rights are group dependend.
     * 
     * @return boolean true on success otherwise false
     */
    function deletePrincipal($principal) {
        $id = $principal->getID();
        if (!$principal->isSuperUser() && $id != _AID_) {
            if($this->deleteAttributes($principal)) {
            	import('classes.group.GroupAdminService');
            	$gas = new GroupAdminService();
            	$gas->removeAllMemberships($id);	
            
                $values = array( 'USER_ID' => $id );
                $sqlString = "DELETE FROM {DB_PREFIX}user WHERE id={USER_ID} and cid={CID}";
                $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
                $GLOBALS['LOGGER']->logInfo('Deleting User with ID: ' . $id);
                return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
            }
            else
            {
                $GLOBALS['LOGGER']->logError('Could not delete User attributes, did not delete User with ID: ' . $id);
            }
        } 

        return false;
    }
    
    /**
     * Sets the given Parameter for the Principal.
     * The allowed Parameter are:
     * - PRINCIPAL_PARAMETER_PASSWORD
     * - PRINCIPAL_PARAMETER_ACTIVE
     * - PRINCIPAL_PARAMETER_LANGUAGE
     * @return boolean true on success otherwise false
     */
    function setParameter($principal, $parameter, $value) {
        $id = $principal->getID();
        if ($parameter == PRINCIPAL_PARAMETER_LANGUAGE) {
            $values = array( 'LANGUAGE' => $value,
                             'USER_ID'  => $id );
            $sqlString = "UPDATE {DB_PREFIX}user SET language={LANGUAGE} WHERE id={USER_ID} and cid={CID}";
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
            $GLOBALS['LOGGER']->logInfo('Setting language ('.$value.') for user ('.$id.')');
            return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        }
        else if ($parameter == PRINCIPAL_PARAMETER_EMAIL) {
            $values = array( 'EMAIL'		=> $value,
                             'USER_ID'  	=> $id );
            $sqlString = "UPDATE {DB_PREFIX}user SET email={EMAIL} WHERE id={USER_ID} and cid={CID}";
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
            $GLOBALS['LOGGER']->logInfo('Changing Email adress for User with ID: ' . $id);
            return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        }
        else if ($parameter == PRINCIPAL_PARAMETER_ACTIVE) {
        	$values = array( 'USER_ID' => $id );
            if(!$value) {
                $sqlString = "UPDATE {DB_PREFIX}user SET active='0' WHERE id={USER_ID} and cid={CID}";
                $GLOBALS['LOGGER']->logInfo('Deactivating User with ID: ' . $id);
            } else {
                $sqlString = "UPDATE {DB_PREFIX}user SET active='1' WHERE id={USER_ID} and cid={CID}";
                $GLOBALS['LOGGER']->logInfo('Activating User with ID: ' . $id);
            }
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
            return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        } 
        else if ($parameter == PRINCIPAL_PARAMETER_PASSWORD) {
			$services = ServiceFactory::get();
			$AUTHENTICATOR = $services->getAuthenticator();
        	
            $values = array( 'PASSWORD' => $AUTHENTICATOR->createHash($value),
                             'USER_ID'  => $id );
            $sqlString = "UPDATE {DB_PREFIX}user SET password={PASSWORD} WHERE id={USER_ID} and cid={CID}";
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
            $GLOBALS['LOGGER']->logInfo('Changing Password for User with ID: ' . $id);
            return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        }
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
        switch($parameter) {
            case PRINCIPAL_PARAMETER_PASSWORD:
                return null;
            case PRINCIPAL_PARAMETER_ACTIVE:
                return $principal->isActive();
            case PRINCIPAL_PARAMETER_LANGUAGE:
                return $principal->getLanguageID();
        }
        return null;
    }

}

?>