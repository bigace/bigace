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

import('classes.fright.FrightAdminService');
import('classes.userdata.UserDataAdminService');
import('classes.core.ServiceFactory');
import('classes.group.GroupAdminService');

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
class UserAdminService 
{
    function UserAdminService() {
        $GLOBALS['LOGGER']->logError('USING DEPRECATED CLASS: ' . get_class($this));
    }

    /**
    * Deletes the given User.
    * This will not work for the BIGACE and ANONYMOUS USER!
    * We do not have to delete any right, because each user has a group and the rights are group dependend!
    * @deprecated 
    * @param    int     the User ID for the User that will be deleted
    * @return   mixed   the result of this deletion process
    */
    function deleteUser($id)
    {
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        $p = $PRINCIPALS->lookupByID($id);
        if($p != null)
            return $PRINCIPALS->deletePrincipal($p);
        return false;
    }

    /**
     * @deprecated 
     */
    function changePassword($id, $newpass)
    {
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        $p = $PRINCIPALS->lookupByID($id);
        return $PRINCIPALS->setParameter($p, PRINCIPAL_PARAMETER_PASSWORD, $newpass);
    }

    /**
    * This creates a User within BIGACE.
    * @deprecated
    * @param    String  the Username 
    * @param    String  the password (will be crypted while saving to DB)
    * @param    int     the language ID
    * @param    boolean A boolean representing rhe Active Status for this User within BIGACE
    * @return   int     the ID for the new generated User
    */
    function createUser($username, $password, $language = 'en', $aktiv = false, $groupID = 0)
    {
        $active = 0;
        if ($aktiv) {
            $active = 1;
        }

        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        $p = $PRINCIPALS->createPrincipal($username, $password, $language);
        if($p !== false) {
            $PRINCIPALS->setParameter($p, PRINCIPAL_PARAMETER_ACTIVE, $active);
            $groupAdmin = new GroupAdminService();
            $groupAdmin->addToGroup($groupID, $p->getID());
            return true;
        }
        return false;
    }



    /**
     * @deprecated 
     */
    function deactivateUser($id) 
    {
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        $p = $PRINCIPALS->lookupByID($id);
        return $PRINCIPALS->setParameter($p, PRINCIPAL_PARAMETER_ACTIVE, false);
    }

    /**
     * @deprecated 
     */
    function activateUser($id) 
    {
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        $p = $PRINCIPALS->lookupByID($id);
        return $PRINCIPALS->setParameter($p, PRINCIPAL_PARAMETER_ACTIVE, true);
    }

    /**
    * Checks if the given Username exists.
    * @deprecated 
    * @param    String  the name that you want to test about its existence
    * @return   boolean true if Username allready exists, else false
    */
    function checkIfUsernameExists($name)
    {
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        $p = $PRINCIPALS->lookup($name);
        return ($p != null);
    }
    

}

?>