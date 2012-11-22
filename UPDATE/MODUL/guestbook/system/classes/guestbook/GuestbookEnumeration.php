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
 * @subpackage guestbook
 */
 
import('classes.guestbook.Guestbook');

/**
 * Can be used to receive all or a list of Guestbook Entrys.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage guestbook
 */
class GuestbookEnumeration
{
	/**
	 * @access private
	 */
	var $all;
	
	function GuestbookEnumeration($from = 0, $limit = 1000)
	{
		$values = array('FROM' => intval($from), 'TO' => intval($limit));
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('guestbook_enum');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, false);
		$this->all = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
		unset ($sqlString);
	}

	function getAllEntrys()
	{
		return $this->all;
	}

	function getNextEntry()
	{
		$temp = $this->all->next();
		return new Guestbook($temp["id"]);
	}

	function countEntrys()
	{
	    if (is_object($this->all)) {
    		return $this->all->count();
	    } else {
	        return 0;
	    }
	}
	
	function countAllEntrys()
	{
	    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->loadAndPrepareStatement('guestbook_count_all', array('CID' => _CID_));
		$entrys = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
		return $entrys->next();
	}
	
	function count() {
		return $this->countEntrys();
	}

	function next() {
		return $this->getNextEntry();
	}
}
