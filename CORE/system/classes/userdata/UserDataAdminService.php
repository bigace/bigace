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
 * @subpackage userdata
 */

/**
 * DO NOT USE THIS CLASS ANY LONGER, IT WILL BE REMOVED IN ONE OF THE NEXT VERSIONS!
 * 
 * Use PrincipalService instead:
 * <code>
 * $services = ServiceFactory::get();
 * $p = $services->getPrincipalService();
 * $attributes = $p->getAttributes();
 * </code>
 * 
 * @deprecated
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage userdata
 */
class UserDataAdminService 
{
	/**
	 * @access private
	 */
	var $userid;
	
	function UserDataAdminService($uid) {
        $GLOBALS['LOGGER']->logError('USING DEPRECATED CLASS: ' . get_class($this));
	    $this->userid = $uid;
	}
	
	function getUserID() {
		return $this->userid;
	}


	/**
	* This changes the User Data within BIGACE.
	*
    * @param    String the Users first name
    * @param    String the Users last name     
    * @param    String the Users email address
    * @param    String the Users homepage
    * @param    String the Users Phone number
    * @param    String the Users Mobile number
    * @param    String the Users Fax number
    * @param    String the Users Company
    * @param    String the Users Street
    * @param    String the Users City
    * @param    String the Users CityCode
    * @param    String the Users Country
	*/
	function changeUserData($firstname, $lastname, $email, $homepage, $phone, $mobile, $fax, $company, $street, $city, $citycode, $country)
	{
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        $p = $PRINCIPALS->lookupByID($this->userid);
        if($p != null) {
            $PRINCIPALS->setAttribute($p,'firstname', $firstname);
            $PRINCIPALS->setAttribute($p,'lastname' , $lastname);
            $PRINCIPALS->setAttribute($p,'homepage' , $homepage);
            $PRINCIPALS->setAttribute($p,'phone' , $phone);
            $PRINCIPALS->setAttribute($p,'mobile' , $mobile);
            $PRINCIPALS->setAttribute($p,'fax' , $fax);
            $PRINCIPALS->setAttribute($p,'company' , $company);
            $PRINCIPALS->setAttribute($p,'street' , $street);
            $PRINCIPALS->setAttribute($p,'city' , $city);
            $PRINCIPALS->setAttribute($p,'citycode' , $citycode);
            $PRINCIPALS->setAttribute($p,'country' , $country);
        }
        $PRINCIPALS->setParameter($p, PRINCIPAL_PARAMETER_EMAIL, $email);
	}

	function changeUserDataValues($dataArray)
	{
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        $p = $PRINCIPALS->lookupByID($this->userid);
		if($p != null) {
    		foreach ($dataArray AS $attribute => $value) {
				$PRINCIPALS->setAttribute($p, $attribute, $value);
    		}
        }
        return false;
	}

	/**
	* Deletes the given User Data for the given User ID.
	*
	* @param int userid the User ID for which the Data should be deleted
	* @return mixed the result of this deletion process
	*/
	function deleteUserData()
	{
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        $p = $PRINCIPALS->lookupByID($this->userid);
        if ($p != null)
            return $PRINCIPALS->deleteAttributes($p);
        
		return false;
	}


}

?>