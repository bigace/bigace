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

import('classes.item.ItemRequest');
import('classes.item.SimpleItemTreeWalker');

/**
 * The ItemTreeWalker provides methods for receiving childs of a special Item.
 * This class only fetches Items the current User has read rights for!
 * 
 * Use a SimpleItemTreeWalker for more complex Item Requests. 
 * 
 * @see SimpleItemTreeWalker
 *  
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage item
 */
class ItemTreeWalker extends SimpleItemTreeWalker
{

    /**
    * Gets all Children of the given Itemtype and Item ID.
    */
    function ItemTreeWalker($itemtype, $parent, $orderby = ORDER_COLUMN_POSITION, $treetype = ITEM_LOAD_FULL, $languageID = '')
    {
    	$req = new ItemRequest($itemtype, $parent);
    	$req->setLanguageID($languageID);
    	$req->setTreetype($treetype);
    	$req->setOrderBy($orderby);
    	$req->setOrder($req->_ORDER_ASC);
    	$this->setRequest($req);
    }

}

?>