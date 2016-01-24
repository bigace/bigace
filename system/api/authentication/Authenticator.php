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
 * @package bigace.api
 * @subpackage authentication
 */

/**
 * This Flag marks the given Name as unknown (no Principal with this name). 
 */
define('AUTHENTICATE_UNKNOWN', false);

/**
 * The Authenticator Interface defines methods that must be 
 * implemented to check Principals and perform the Login comand. 
 * 
 * Receive an Authenticator instance by calling:
 * <code>
 * $services = ServiceFactory::get();
 * $services->getAuthenticator();
 * </code>
 *  
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.api
 * @subpackage authentication
 */
class Authenticator
{

    /**
     * This performs an Authentication check.
     * This returns <code>Principal</code> if authentication was correct,
     * otherwise one of the listed Flags will be returned.
     * 
     * The currently returned Flags are:
     * - AUTHENTICATE_UNKNOWN
     * 
     * @return Principal|mixed the Flag or a Principal is returned
     */
    function authenticate($name, $password) {
        return AUTHENTICATE_UNKNOWN;
    }

    /**
     * Creates a hash to be used as password.
     * Do not store or query plain text values, always use encrypted data!
     *
     * @param string $password
     * @return string the hashed password
     */
	function createHash($password) {
		$password = md5($password);
		if(defined('BIGACE_AUTH_SALT')) {
			$length = defined('BIGACE_SALT_LENGTH') ? ((BIGACE_SALT_LENGTH < 32 && BIGACE_SALT_LENGTH > 0) ? intval(BIGACE_SALT_LENGTH) : 16 ) : 16;
			$password = md5(BIGACE_AUTH_SALT.sha1(substr($password,0,$length).BIGACE_AUTH_SALT.substr($password,$length)).BIGACE_AUTH_SALT);
		}
		return $password;
	}

}
