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
 * @subpackage menu
 */

import('classes.item.Item');

/**
 * Class used for handling News.
 *
 * For currently used Text/Num/Date fields, see Item.php.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage menu
 */
class News extends Item
{

    /**
     * Instantiates a MenuService representing the given Menu ID.
     * If you pass null as ID the Object will not be initialized but only
     * instantiated.
     *
     * @param int the Menu ID or null
     * @param String the treetype
     * @param String the Language ID
     */
    function News($id=null, $languageID=null)
    {
    	if(func_num_args() == 2 && $id != null && $languageID != null)
	        $this->init(_BIGACE_ITEM_MENU, $id, ITEM_LOAD_FULL, $languageID);
    }

    /**
     * Returns the News Teaser.
     *
     * @return String the teaser
     */
    function getTeaser() {
    	return $this->getDescription();
    }

    /**
     * Returns the News title
     *
     * @return String the title
     */
    function getTitle() {
    	return $this->getName();
    }

    /**
     * Returns the ID of the linked Image
     *
     * @return int the Image ID
     */
    function getImageID() {
    	return $this->getItemNum('1');
    }

    /**
     * Returns the News Date.
     * If not set, this returns the creation date.
     */
    function getDate() {
    	$d = $this->getItemDate('2');
    	if($d == null || $d == 0)
    		return $this->getCreateDate();
    	return $d;
    }

}