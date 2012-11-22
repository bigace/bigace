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
 * Use Authenticator instead:
 * <code>
 * $services = ServiceFactory::get();
 * $auth = $services->getAuthenticator();
 * $principal = $auth->authenticate(name,password);
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
class UserAuthenticate 
{
	/**
	 * @access private
	 */
	var $auth;
	
	/**
	 * Identifies a User in BIGACE by comapring Username/Password against the existing User Data.
	 * This Object will treat the User as a anonymous (all+) User if name or password is empty,
	 * being sure that a User cannot have an empty password.
	 *
	 * @param	String	the Username within BIGACE
	 * @param	String	the Password for that User
	 */
	function UserAuthenticate($username, $pass)
	{
        $GLOBALS['LOGGER']->logError('USING DEPRECATED CLASS: ' . get_class($this));
        $services = ServiceFactory::get();
        $a = $services->getAuthenticator();
        $this->auth = $a->authenticate($username,$pass); 
	}

	/**
	 * Shows if the User is registered within BIGACE.
	 * It returns true even if the Users status is inactive!
	 * 
	 * @return boolean	true if user is known, else false
	 */
	function isValidUser()
	{
        return ($this->auth != null && $this->auth != AUTHENTICATE_UNKNOWN);
	}

	/**
	 * Gets the Information if user is anonymous.
	 *
	 * @return boolean if User is anonymous
	 */
	function isAnonymous()
	{
        if($this->isValidUser()) {
            $a = $this->auth;
            return $a->isAnonymous();
        }
		return true;
	}

	/**
	 * Gets the Users status within BIGACE, if false the User will not be able to LOG IN or 
	 * handle any other thing in BIGACE. This is useful if you want to keep all Right Information 
	 * and personal data but don't let the user work for now.
	 * If User is not known it will return the same as <code>isValidUser()</code>.
	 *
	 * @return boolean the Users status for BIGACE
	 */
	function isActive()
	{
        if($this->isValidUser()) {
            $a = $this->auth;
            return $a->isActive();
        }
        return false;
	}


	/**
	 * Gets the User ID.
	 *
	 * @return int the User ID in BIGACE
	 */
	function getID() {
        if($this->isValidUser()) {
            $a = $this->auth;
            return $a->getID();
        }
        return _AID_;
	}


	/**
	 * Fetches the users language ID.
	 *
	 * @return int the language ID
	 */
	function getLanguageID() {
        if($this->isValidUser()) {
            $a = $this->auth;
            return $a->getLanguageID();
        }
        return 'en';
	}
	
}

?>