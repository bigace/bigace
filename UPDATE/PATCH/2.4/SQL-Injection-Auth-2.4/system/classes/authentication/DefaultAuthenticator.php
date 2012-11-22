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
 * @subpackage authentication
 */

import('api.authentication.Authenticator');
import('classes.principal.DefaultPrincipal');

/**
 * The DefaultAuthenticator uses the internal User Management Database. 
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage authentication
 */
class DefaultAuthenticator extends Authenticator
{

    function DefaultAuthenticator()
    {
    }
    
    /**
     * Performs a Login against the internal User Database.
     * @return mixed the Flag or a Principal is returned
     */
    function authenticate($name, $password) {

        if ( strlen ($name) < 1) {
            return AUTHENTICATE_UNKNOWN;
        }
        
        if (!preg_match("/all-/i", $name) && !preg_match("/all+/i", $name)) {
            
            $values = array( 'NAME'     => $name,
                             'PASSWORD' => md5($password),
                             'CID'      => _CID_ );
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('user_select_by_authentication');
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
            echo $sqlString;
            $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
            
            if ($res->isError() || $res->count() == 0) {
                $this->handleWrongPassword($name);
                return AUTHENTICATE_UNKNOWN;
            }
            
            $temp = $res->next();
            $principal = new DefaultPrincipal($temp['id']);
            
            return $principal;
        }
        return AUTHENTICATE_UNKNOWN;
    }
    
    /**
     * @access private
     */
    function handleWrongPassword($name) 
    {
    	if (ConfigurationReader::getConfigurationValue('login', 'deactivate.on.failures', false)) {
	        if ( !$GLOBALS['_BIGACE']['SESSION']->isValueSet('LOG_IN_FAILURE') )  {
	            $GLOBALS['_BIGACE']['SESSION']->setSessionValue('LOG_IN_FAILURE', 1);
	        } 
	        else  {
	            $loginFailures = $GLOBALS['_BIGACE']['SESSION']->getSessionValue('LOG_IN_FAILURE');
	            if ($loginFailures > ConfigurationReader::getConfigurationValue('login', 'failures.before.deactivate', 5)) 
	            {
	                import('classes.core.ServiceFactory');
	                $services = ServiceFactory::get();
	                $PRINCIPALS = $services->getPrincipalService();
	                $prince = $PRINCIPALS->lookup($name);
	                if ($prince != null && !$prince->isSuperUser() && $prince->getID() != _AID_ && $prince->isActive()) {
	                    $PRINCIPALS->setParameter($prince, PRINCIPAL_PARAMETER_ACTIVE, false);
	                    $GLOBALS['LOGGER']->logError('User ('.$prince->getName().') failed logging in for more than ' . $loginFailures . ' times. Deactivating for security reasons!');
	                }
	            }
	            $loginFailures++;
	            $GLOBALS['_BIGACE']['SESSION']->setSessionValue('LOG_IN_FAILURE', $loginFailures);
	        }
    	}
    }

}

?>
