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
 * $allUser = $principalService->getAllPrincipals();
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
class UserEnumeration 
{
	/**
	 * @access private
	 */
	var $alluser;
    /**
     * @access private
     */
    var $counter;
	
	function UserEnumeration()
	{
        $GLOBALS['LOGGER']->logError('USING DEPRECATED CLASS: ' . get_class($this));
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        $this->alluser = $PRINCIPALS->getAllPrincipals();
        $this->counter = 0;
	}


	/**
	* Counts all Users registered in BIGACE.
	*
	* @return int the value for all User
	*/
	function count()
	{
		return count($this->alluser);
	}


	/**
	* Gets the next User in the list.
	*
	* @return Principal the next User in the list
	*/
	function next()
	{
        $temp = $this->alluser[$this->counter];
        $this->counter++;
        return $temp;
	}

}

?>