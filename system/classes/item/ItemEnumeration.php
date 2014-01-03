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
 * @subpackage item
 */

import('classes.item.DBItem');
import('classes.item.Itemtype');

/**
 * The ItemEnumeration helps reading a all list of Items.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage item
 */
class ItemEnumeration
{
    /**
     * @access private
     */
    var $res;
    /**
     * @access private
     */
    var $itemtype;
    
    /**
     * Gets all Children of the given Item ID
     * @param array the Array with the DB Results of Items.
     * @access private
     */
    function ItemEnumeration($dbResult, $itemtype)
    {
        $this->res = $dbResult;
        $this->itemtype = $itemtype;
    }
    
    /**
     * Count how many Items can be returned by this Enumeration.
     * @return int the amount of Items within this Enumeration
     */
    function count() {
        return $this->res->count();
    }

    /**
     * Returns the next Item in this Enumeration.
     * @return Item the next Item
     */
    function next() {
    	$d = new DBItem( $this->res->next() );
    	$d->initItemtype($this->itemtype);
        return $d;
    }

}

?>