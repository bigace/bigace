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
 * @subpackage comments
 */

import('classes.item.ItemProjectService');

/**
 * Some standard functions for handling trackbacks and comments
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage comments
 */
class CommentService
{
	/**
	 * Checks if there is a ping or trackback from that adress, ignoring the content of the comment.
	 */
	public static function checkRemoteCommentDuplicate($itemtype, $id, $language, $url)
	{
		$values = array('ITEMTYPE' => $itemtype, 'ID' => $id, 'LANGUAGE' => $language, 'URL' => $url);
		$res = $GLOBALS['_BIGACE']['SQL_HELPER']->sql('comment_find_duplicate_trackback', $values, true);
		if($res->count() > 0)
			return true;
		return false;
	}

	/**
	 * Checks if there is a comment from that email adress with username and the comment for the item.
	 */
	public static function checkCommentDuplicate($itemtype, $id, $language, $name, $content)
	{
		$values = array('ITEMTYPE' => $itemtype, 'ID' => $id, 'LANGUAGE' => $language, 'NAME' => $name, 'COMMENT' => $content);
		$res = $GLOBALS['_BIGACE']['SQL_HELPER']->sql('comment_find_duplicate', $values, true);
		if($res->count() > 0)
			return true;
		return false;
	}
	
	/**
	 * Check if trackbacks or pings are allowed for this item.
	 */
	public static function activeRemoteComments($itemtype, $id, $language) 
	{
		$ips = new ItemProjectService($itemtype);
		return $ips->getBool($id, $language, 'allow_trackbacks', true);
	}
	
	/**
	 * Check if comments are allowed for this item.
	 */
	public static function activeComments($itemtype, $id, $language) 
	{
		$ips = new ItemProjectService($itemtype);
		return $ips->getBool($id, $language, 'allow_comments', true);
	}
}
