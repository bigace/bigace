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
 * @subpackage configuration
 */


/**
 * This class provides methods for the Administration of DB Configurations. 
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage configuration
 */
class ConfigurationAdmin
{

    /**
     * Update an Configuration Entry.
     */
    static function updateEntry($package, $name, $value)
    {
        $values = array('NAME' =>$name, 'VALUE' => $value, 'PACKAGE' => $package);
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('configuration_update');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $GLOBALS['LOGGER']->logSQL('Updating Configuration Entry: ' . $sqlString);
        
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);        
    }

    /**
     * Creates an Configuration Entry.
     */
    static function createEntry($package, $name, $type, $value)
    {
        $values = array('TYPE' => $type, 'NAME' => $name, 'VALUE' => $value, 'PACKAGE' => $package);
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('configuration_create');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $GLOBALS['LOGGER']->logSQL('Creating Configuration Entry: ' . $sqlString);
        
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);        
    }    
}
