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
 * @subpackage file
 */
 
import('classes.item.Item');

/**
 * Class used for handling BIGACE "File" Items.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage file
 */
class File extends Item
{
    /**
     * This intializes the Object with the given File ID
     * If you pass null as ID the Object will not be initialized but only
     * instantiated.
     *
     * @param int id the File ID or null
     * @param mixed treetype the Treetype to use for loading
     * @param String language the Language to load
     */
    function File($id=null, $treetype=ITEM_LOAD_FULL, $language='')
    {
    	if(func_num_args() > 0)
    		$this->init(_BIGACE_ITEM_FILE, $id, $treetype, $language);
    }

    
}

?>