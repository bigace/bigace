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

import('classes.menu.MenuAdminService');
import('classes.category.CategoryAdminService');

/**
 * Class used for administrating "News" entrys.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage news
 */
class NewsAdminService extends MenuAdminService
{

    /**
     * Instantiates a NewsAdminService.
     */
    function __construct() {
         parent::__construct();
    }
    
    /**
     * @access private
     */
    function prepareNewsDataArray($language, $title, $teaser, $metatags, $content, $newsDate, $imageID = null, $published = false) {
    	$vals =  array(
    		'langid'		=> $language,
    		'template'		=> ConfigurationReader::getConfigurationValue("news", "template.news"),
    		'name'			=> $title,
    		'description'	=> $teaser,
    		'catchwords'	=> $metatags,
    		'content'		=> $content,
    		'date_2'		=> $newsDate,
    		'num_1'			=> $imageID,
    		'num_3'			=> (($published === true) ? FLAG_NORMAL: FLAG_HIDDEN),
    		'parentid'		=> ConfigurationReader::getConfigurationValue("news", "root.id")
    	);

		return $vals;
    }

    /**
     * Deletes a News entry with the given ID and Language.
     *
     * @param int the News ID
     * @param String the Language locale
     */
    function deleteNews($id, $language) 
    {
    	return $this->deleteItemLanguage($id, $language);
    }
    
    /**
     * Updates a News entry with the given values.
     *
     * @return true on success, false on error
     */
    function updateNews($id, $language, $title, $teaser, $metatags, $content, $categories = array(), $newsDate, $imageID = null, $published = false)
    {
    	$data = $this->prepareNewsDataArray($language, $title, $teaser, $metatags, $content, $newsDate, $imageID, $published);

		// create all category links new 
    	$this->removeAllCategoryLinks($id);
    	
		// create all category links new 
        if(count($categories) > 0) 
    	{
	    	$cs = new CategoryAdminService();
	    	foreach($categories AS $catID) {
	    		$cs->createCategoryLink(_BIGACE_ITEM_MENU, $id, $catID);
	    	}
    	}
    	
    	return $this->updateItemContent($id, $language, $content, $data);
    }
    
	/**
     * Creates a News entry with the given values.
     *
     * @return the new News ID or FALSE
     */
    function createNews($language, $title, $teaser, $metatags, $content, $categories = array(), $newsDate, $imageID = null, $published = false)
    {
    	$data = $this->prepareNewsDataArray($language, $title, $teaser, $metatags, $content, $newsDate, $imageID, $published);
    	$data['modul'] = 'displayContent'; // is this really required?
    	
    	$pattern = ConfigurationReader::getConfigurationValue('news', 'unique.name.pattern', '');
    	$extension = ConfigurationReader::getConfigurationValue('news', 'unique.name.extension', '');
    	$catRoot = ConfigurationReader::getConfigurationValue("news", "category.id");
    	/*
    	foreach($categories AS $catID) {
    		if($catID != $catRoot) {
    			// TODO load category and use its name ... do we get a return value?
    			$pattern = str_replace("{category}", "{category}", $pattern);
    		}
    	}
		*/
    	$pattern = strftime($pattern);
    		
    	$data['unique_name'] = parent::buildUniqueNameSafe($pattern . $data['name'] . $extension,$extension);
    	
    	$id = $this->createMenu($data);
    	
    	if($id === FALSE) {
    		return false;
    	}
    	
    	if(count($categories) > 0) 
    	{
	    	$cs = new CategoryAdminService();
	    	foreach($categories AS $catID) {
	    		$cs->createCategoryLink(_BIGACE_ITEM_MENU, $id, $catID);
	    	}
    	}
    	return $id; 
    }

	function publishNews($id, $language) {
		return $this->setItemFlag($id, $language, FLAG_NORMAL);
	}

	function hideNews($id, $language) {
		return $this->setItemFlag($id, $language, FLAG_HIDDEN);
	}
	
}
