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
 * @subpackage tipoftheday
 */

loadClass('tipoftheday', 'TipOfTheDay');

/**
 * Can be used to receive all or a a limited list of Entrys.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage tipoftheday
 */
class TipOfTheDayEnumeration
{
    /**
     * @access private
     */
	var $all;
	
	function TipOfTheDayEnumeration($from = '0', $limit = '-1')
	{
	    $LIM = '';
	    if ($limit != '-1') {
	        $LIM = " LIMIT ".$from.",".$limit;
	    }
	    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->loadAndPrepareStatement('tipoftheday_enum', array('CID' => _CID_, 'LIMIT' => $LIM));
		$this->all = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
		unset ($sql);
	}

	function next()
	{
		$temp = $this->all->next();
		return new TipOfTheDay($temp["id"]);
	}

	function count()
	{
	    if ($this->all) {
    		return $this->all->count();
	    } else {
	        return 0;
	    }
	}
	
}

?>