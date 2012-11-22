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
 

loadClass('fright', 'DBFright');

/**
 * This should be used to receive all exisiting Fright-Strings.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage fright
 */
class FrightStringsEnumeration
{
	/**
	 * @access private
	 */
	var $items;
	/**
	 * @access private
	 */
	var $tab;

	function FrightStringsEnumeration($orderby = 'name', $order = 'ASC')
	{
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('fright_strings');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array( 'CID' => _CID_, 'ORDER_BY' => $orderby, 'ORDER' => $order ));
        $this->items = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	}
	
	function count()
	{
	    if ($this->items) {
		    return $this->items->count();
		} else {
		    return 0;
		}
	}
	
    /**
     * @return Fright the next Functional Right
     */
    function next() 
    {
		return new DBFright($this->items->next());
    }

}

?>