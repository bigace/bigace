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

import('classes.item.MasterItemType');

/**
 * This Base class is represents an Itemtype within the CMS
 * and holds methods to receive several information about Items of this Type.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage item
 */
class Itemtype extends MasterItemType
{
    /**
     * @access private
     */
    var $itemtype;

    function Itemtype( $type )
    {
        $this->initItemtype($type);
    }
    
    function initItemtype($type) 
    {
        $this->itemtype = $type;
    }
    
    function getItemtypeID() {
        return $this->itemtype;
    }
    
    function getCommand() 
    {
       return $this->getCommandForItemType ($this->getItemtypeID());
    }

    function getClassName() 
    {
       return $this->getClassNameForItemType ($this->getItemtypeID());
    }
    
    function getDirectory()
    {
       return $this->getDirectoryForItemType ($this->getItemtypeID());
    }
    
    function getClass($id, $treetype = ITEM_LOAD_FULL, $languageID='')
    {
        return $this->getClassForItemType($this->getItemtypeID(), $id, $treetype, $languageID);
    }
    
}

?>