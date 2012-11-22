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

import('classes.menu.ContentService');

define('CNT_TYPE_HTML', 'html');
define('CNT_TYPE_PDF', 'pdf');
define('CNT_STATE_RELEASED', 'R');
define('CNT_STATE_DRAFT', 'D');
define('CNT_STATE_ARCHIVE', 'A');

/**
 * This file holds all methods for reading menu contents.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage menu
 */

class ContentSaver
{
	private $id, $language, $name, $content;

	public function ContentSaver($id, $language, $name, $content = '') {
		$this->id = $id;
		$this->language = $language;
		$this->name = $name;
		$this->content = $content;
	}
	
	public function setContent($cnt) {
		$this->content = $cnt; 
	}
	
	public function getContent() {
		return $this->content; 
	}
	
	public function getID() {
		return $this->id; 
	}
	
	public function getLanguage() {
		return $this->language; 
	}
	
	public function getName() {
		return $this->name; 
	}
	
	public function validate() {
		if (is_null($this->id)) return false; 
		if (is_null($this->language)) return false; 
		if (is_null($this->name)) return false; 
		if (is_null($this->content)) return false;
		return true; 
	}
	
	public function getValidFrom() {
		return time() - 1000;
	}
	
	public function getValidTo() {
		// max timestamp: strtotime("19 Jan 2038 03:14:07 GMT")
		return 2147483647;
	}
	
	public function getPosition() {
		return 1;
	}
	public function getType() {
		return CNT_TYPE_HTML;
	}
	public function getStatus() {
		return CNT_STATE_RELEASED;
	}
}

/**
 * Returns the rendered content for a menu, identified by its name.
 *
 * @param ContentSave $contentSave the Object filled with all required values  
 * @return mixed the content or null
 */
function save_content($contentSave) {

	if(!$contentSave->validate()) {
		// TODO log me
		return false;
	}
	
	if(get_content_raw($contentSave->getID(), $contentSave->getLanguage(), $contentSave->getName()) == null) {
		$sql = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('content_create');
	}
	else {
		$sql = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('content_update');
	}
	
	$values = array ( 'ID' 			=> $contentSave->getID(), 
					  'LANGUAGE' 	=> $contentSave->getLanguage(), 
					  'NAME' 		=> $contentSave->getName(),
					  'CONTENT' 	=> $contentSave->getContent(),
					  'STATE' 		=> $contentSave->getStatus(), 
					  'TYPE' 		=> $contentSave->getType(),
					  'POSITION' 	=> $contentSave->getPosition(),
					  'VALID_FROM' 	=> $contentSave->getValidFrom(),
					  'VALID_TO' 	=> $contentSave->getValidTo()
	);
	$sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
	$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
	
	return true;
}

?>