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
 * @subpackage userdata
 */

import('classes.core.ServiceFactory');

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
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage userdata
 */
class UserData 
{
    /**
     * @access private
     */
    var $userdata;
    /**
     * @access private
     */
    var $principal;
    
    /**
    * This initalizes the Object with the given User
    *
    * @param    int the User ID
    */
    function UserData($userid)
    {
        $GLOBALS['LOGGER']->logError('USING DEPRECATED CLASS: ' . get_class($this));
        $this->init($userid);
    }
    
    function init($id)
    {
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        $this->principal = $PRINCIPALS->lookupByID($id);
        $this->userdata = $PRINCIPALS->getAttributes($this->principal);
    }

    function getExtension($id) 
    {
        return $this->getAttribute('extended' . $id);
    }

    /**
    * Returns the User ID for that this Object was initalized
    *
    * @return int the Users ID
    */
    function getUserID() 
    {
        return $this->getAttribute('userid');
    }
    
    function getFirstName()
    {
        return $this->getAttribute('firstname');
    }

    function getLastName()
    {
        return $this->getAttribute('lastname');
    }

    function getEmail()
    {
    	$p = $this->principal;
        return $p->getEmail();
    }

    function getHomepage()
    {
        return $this->getAttribute('homepage');
    }
    
    function getPhone()
    {
        return $this->getAttribute('phone');
    }

    function getMobile()
    {
        return $this->getAttribute('mobile');
    }

    function getFax()
    {
        return $this->getAttribute('fax');
    }

    function getCompany()
    {
        return $this->getAttribute('company');
    }
    
    function getStreet()
    {
        return $this->getAttribute('street');
    }
    
    function getCity()
    {
        return $this->getAttribute('city');
    }
    
    function getCityCode()
    {
        return $this->getAttribute('citycode');
    }
    
    function getCountry()
    {
        return $this->getAttribute('country');
    }
    
    function getAttribute($name) {
        if(!isset($this->userdata[$name]))
            return '';
        return $this->userdata[$name];    
    }

}

?>