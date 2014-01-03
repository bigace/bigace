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
 * This Interface represents a Principal.
 * You can fetch an instance for example by calling:
 * 
 * <code>
 * $services = ServiceFactory::get();
 * $principalService = $services->getPrincipalService();
 * $principal = $principalService->lookupByID($userID); 
 * </code>
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.api
 * @subpackage principal
 */
class Principal
{

    function Principal()
    {
    }

    /**
     * Returns whether the User is anonymous or not.
     *
     * @return boolean true if User is anonymous
     */
    function isAnonymous() {
        return true;
    }
    
    /**
     * Returns whether the User is Super User or not.
     * For a Super User Permissions will NOT be checked.
     * A Super User therefor has even more rights than a normal Administrator. 
     *
     * @return boolean true if the Principal is a Super User
     */
    function isSuperUser() {
        return false;
    }

    /**
     * Gets the Users status. 
     * Returns if the Principal is active or not.
     * Deactivated Principaly can not log in.
     *
     * @return boolean value for the Principal status.
     */
    function isActive() {
        return false;
    }

    /**
     * Returns the Principal ID.
     *
     * @return mixed the unqiue ID identifying the Principal
     */
    function getID() {
        return '';
    }

    /**
     * Gets the Principal name.
     *
     * @return String the Principal Name
     */
    function getName() {
        return '';
    }

    /**
     * Gets the Language ID for this Principal.
     *
     * @return mixed the language ID
     */
    function getLanguageID() {
        return '';
    }

    /**
     * Returns the Users email.
     *
     * @return String the Users email adress or null 
     */
    function getEmail() {
    	return null;
    }
}

?>