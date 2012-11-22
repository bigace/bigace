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
 * @subpackage fright
 */

/**
 * The FrightAdminService provides methods for administrating
 * of Functional rights.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage fright
 */
class FrightAdminService
{

    function deleteAllGroupFrights($id)
    {
	    $values = array( 'GROUP_ID'     => $id );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('fright_delete_all_group_fright');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logInfo('Deleting all Frights for Group: ' . $id);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    function deleteGroupFright($groupid, $fright)
    {
	    $values = array( 'GROUP_ID'     => $groupid,
	                     'FRIGHT_ID'    => $fright );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('fright_delete_group_fright');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    function deleteFright($id)
    {
	    // delete all mappings for the fright
	    $values = array( 'FRIGHT_ID'    => $id );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('fright_delete_fright_mappings');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logSQL('Deleting all Mappings for Fright (ID: '.$id.'): ' . $sqlString);
        $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

        // now delete the fright itself
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('fright_delete_fright');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logSQL('Delete Fright (ID: '.$id.'): ' . $sqlString);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

    }

    /**
     * Create a mapping for a Group to a Functional right.
     */
    function createGroupFright($groupid, $fright, $val = 'Y')
    {
        $values = array( 'GROUP_ID'  => $groupid,
                         'FRIGHT'    => $fright,
                         'VALUE'     => $this->parseFrightValue($val) );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('fright_create_group_fright');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logSQL('Create Fright: ' . $sqlString);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sqlString);
    }

    function changeFright($name, $description, $value = 'Y')
    {
        $val = $this->parseFrightValue($value);
        $values = array( 'DESCRIPTION'  => $description,
                         'FRIGHT_NAME'    => $name,
                         'VALUE'        => $val );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('fright_change_fright');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $GLOBALS['LOGGER']->logSQL('Change Fright: ' . $sqlString);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
     * Create an Functional Right with the given Name.
     * The name must have at least a length of 4 character!
     */
    function createFright($name,$description, $val = 'Y')
    {
        if (strlen(trim($name)) > 3)
        {
            $values = array( 'DESCRIPTION'  => $description,
                             'NAME'         => $name,
                             'DEFAULT'      => 'N',
                             'VALUE'        => $this->parseFrightValue($val) );
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('fright_create_fright');
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
            return $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sqlString);
        }
        return false;
    }

    /**
     * Parses the given Value and returns 'Y' (true) if the given value
     * matches 'Y', in all other cases 'N' (false).
     */
    function parseFrightValue($value)
    {
        if ($value == 'Y')
            return 'Y';
        return 'N';
    }

}

?>