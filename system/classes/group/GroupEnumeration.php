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

import('classes.group.Group');

/**
 * Receive a List of all available User Groups.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage group
 */
class GroupEnumeration
{
	private $enum;
	
	/**
	* Gets a enumeration of all Groups.
	*
	* @param int cid the Consumer ID to fetch Groups for
	*/
	function GroupEnumeration($cid = _CID_)
	{
	    
	    $values = array( 'CID' => $cid );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('group_enumeration');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $this->enum = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	}

	function count()
	{
		return $this->enum->count();
	}

    /**
    * Gets the next Group.
    * @return Group the next Group
    */
	function next()
	{
		$g = new Group();
		$g->init($this->enum->next());
		return $g;
	}
	
}

?>