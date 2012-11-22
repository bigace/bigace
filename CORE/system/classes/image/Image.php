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
 * @subpackage image
 */

import('classes.item.Item');

/**
 * This represents an Image Item.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage image
 */
class Image extends Item
{
    
    /**
     * This intializes the Object with the given Image ID
     * If you pass null as ID the Object will not be initialized but only
     * instantiated.
     *
     * @param int id the Image ID or null
     * @param mixed treetype the TreeType
     * @param String language the Language
     */
    function Image($id=null, $treetype = ITEM_LOAD_FULL, $language = '')
    {
    	if(func_num_args() > 0)
    		$this->init(_BIGACE_ITEM_IMAGE, $id, $treetype, $language);
    }


}

?>