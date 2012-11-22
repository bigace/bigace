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
 * @subpackage group
 */

/**
 * ID of the Anonymous User Group.
 */
define('GROUP_ANONYMOUS', 0);

/**
 * This represents a User Group.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage group
 */
class Group
{

	private $group = null;

    /**
     * This intializes the Object with the given Group ID
     *
     * @param id the Group ID to be loaded
     */
    function Group($id = null)
    {
        if($id != null)
    	   $this->_initFromDatabase($id);
    }
    
    /**
     * Initializes the Object with a Result from Database
     * @param id the Group ID to be loaded
     * @access private
     */
    private function _initFromDatabase($id) 
    {
	    $values = array( 'GROUP_ID' => $id,
	                     'CID'      => _CID_ );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('group_select');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $this->group = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $this->init( $this->group->next() );
    }

    /**
     * Use this method for instantiation of Groups through prefetched DB results.
     * @param $groupData an array with the group data
     */
    function init($groupData) {
        $this->group = $groupData;
    }
    
    /**
     * Gets the Group ID.
     *
     * @return int the ID
     */
    function getID() {
        return $this->_getValue("group_id");
    }

    /**
     * Gets the Group Name
     *
     * @return int the ID
     */
    function getName() {
        return $this->_getValue("group_name");
    }
    
    private function _getValue($key) {
        if($this->group != null && isset($this->group[$key]))
            return $this->group[$key];
        return null;
    }

}
