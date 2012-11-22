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

/**
 * Returns the rendered content for a menu, identified by its name.
 *
 * @param int $id the menu id  
 * @param String $language the language locale
 * @param String $name the content entry identifier
 * @return mixed the content or null
 */
function get_content($id, $language, $name, $target = 'html') {

	$res = get_content_raw($id, $language, $name);
	if($res != null) {
		return render_content($res, $target);
	}
	
	return null;
}

/**
 * Returns the rendered content for a menu, identified by its name.
 *
 * @param int $id the menu id  
 * @param String $language the language locale
 * @param String $name the content entry identifier
 * @return mixed the complete db entry or null
 */
function get_content_raw($id, $language, $name) {
	// prepare sql to find the item reference by its unique name
	$values = array ( 'ID' => $id, 'LANGUAGE' => $language, 'NAME' => $name );
	$sql = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('content_get');
	$sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
	$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);

	// found entry - fetch values from result
	if($res->count() != 0) {
		return $res->next();
	}

	return null;
}

/**
 * Returns the rendered content. It trys to find a specialized renderer function, called:
 * <code>render_content_{TYPE}_2_{TARGET}($source)</code> where {TYPE} is the content 
 * entries type (html, bbcode, ...) and target is yor requested target code (html, pdf, ...).
 *  
 * If none could be found, it returns the $source itself
 *
 * @param unknown_type $content_entry
 * @param unknown_type $target
 * @return unknown
 */
function render_content($content_entry, $target = 'html') {
	$renderer = "render_content_" . $content_entry['cnt_type'] . "_2_" . $target;
	if(function_exists($renderer))
		return $renderer($content_entry);

	return $content_entry['content'];
}

/**
 * Renders the HTML by treating it as a Smarty Resource. 
 */
function render_content_html_2_smarty($content_entry) {
	// TODO - this MUST be changed to use the database!
	$name = $content_entry['id'] . '_' . $content_entry['language']; 
	import('classes.core.FileCache');
	FileCache::createCacheFile($name, $content_entry['content']);
	$smarty = BigaceSmarty::getSmarty();
	return $smarty->fetch("file://" . FileCache::get_cache_url($name));
}

?>