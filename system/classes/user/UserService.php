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
 * @subpackage user
 */

import('classes.core.ServiceFactory');

/**
 * DO NOT USE THIS CLASS ANY LONGER, IT WILL BE REMOVED IN ONE OF THE NEXT VERSIONS!
 * 
 * Use PrincipalService instead:
 * <code>
 * $services = ServiceFactory::get();
 * $principalService = $services->getPrincipalService();
 * </code>
 * 
 * @deprecated
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage user
 */
class UserService 
{

	/**
     * Default Constructor.
	 */
	function UserService()
	{
        $GLOBALS['LOGGER']->logError('USING DEPRECATED CLASS: ' . get_class($this));
	}

	/**
	* This returns a Enumeration of all User
	*
	* @return   Object  a new Enumeration of all User
	*/
	function getUserEnumeration()
	{
	    return new UserEnumeration();
	}

	/**
	* This returns a new instance of the Principal Object
	*
	* @param	int     the user ID to get the information for
	* @return	Principal  a new instance of Principal
	*/
	function getUserInfo($id)
	{
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        return $PRINCIPALS->lookupByID($id);
	}
	
	/**
	* This returns the User ID for the searched Name or the ID for the anonymous User _AID_.
    * 
	* @deprecated use PrincipalService->lookup() instead!
	* @param	String	the user Name to lookup
	* @return	int     the User ID or _AID_
	*/
	function getUserByName($name) 
	{
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        $p = $PRINCIPALS->lookup($name);
        if($p != null)
            return $p->getID();
        return _AID_;
	}
	
}

?>