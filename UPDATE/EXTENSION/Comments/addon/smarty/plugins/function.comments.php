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
 * @package addon.smarty
 * @subpackage function
 */

/*
 * Fetches an array of Comments for an Item. 
 * Is also capable to create new Comments.
 * 
 * - assign
 * - preview
 * 
 * - item 
 *   OR 
 * - id
 * - language
 * - itemtype (default: _BIGACE_ITEM_MENU) 
 * 
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package addon.smarty
 * @subpackage function
 */
function smarty_function_comments($params, &$smarty)
{	
	if(!isset($params['assign'])) {
		$smarty->trigger_error("comments: missing 'assign' attribute");
		return;
	}
	if(!isset($params['preview'])) {
		$smarty->trigger_error("comments: missing 'preview' attribute");
		return;
	}
	
	if(isset($params['item']))
	{
		$itemtype = $params['item']->getItemTypeID(); 
		$id = $params['item']->getID();
		$language = $params['item']->getLanguageID();
	}
	else
	{
		if(!isset($params['id']) || !isset($params['language']) ) {
			$smarty->trigger_error("comments: missing 'id' or 'language' attribute");
			return;
		}
		$itemtype = (isset($params['itemtype']) ? $params['itemtype'] : _BIGACE_ITEM_MENU); 
		$id = $params['id'];
		$language = $params['language'];
	}
	
	// empty preview
	$preview = array(
		'name' 		=> '',
		'email'		=> '',
		'homepage'	=> '',
		'comment'	=> ''
	);
	
	$captcha = null;
	if(ConfigurationReader::getConfigurationValue("comments", "use.captcha", false))
	{
		$captcha = ConfigurationReader::getValue("system", "captcha", null);
		if($captcha == null) {
			$smarty->trigger_error("comments: wrong configuration 'system/captcha'");
		}
	}
	
	if(isset($params['post']) && isset($_POST) && count($_POST) > 0)
	{
		if(!isset($params['admin'])) {
			$smarty->trigger_error("comments: tried to post comment, but 'admin' attribute was missing");
			return;
		}
		$result = array('missing' => array());
		if(!isset($_POST['name']) || strlen(trim($_POST['name'])) == 0)
			$result['missing'][] = 'name'; 
		if(ConfigurationReader::getConfigurationValue('comments', 'email.required', false) && (!isset($_POST['email']) || trim(strlen($_POST['email'])) == 0))
			$result['missing'][] = 'email'; 
		if(!isset($_POST['comment']) || strlen(trim($_POST['comment'])) == 0)
			$result['missing'][] = 'comment'; 
		if(!is_null($captcha) && (!isset($_POST['source']) || !isset($_POST['captcha']) || $captcha->validate($_POST['captcha'],$_POST['source']) != 1))
			$result['missing'][] = 'captcha'; 
		
		$allowHtml = ConfigurationReader::getConfigurationValue("comments", "allow.html", false);
		$comment = (isset($_POST['comment']) ? ($allowHtml ? $_POST['comment'] : strip_tags($_POST['comment'])) : '');
		$name = (isset($_POST['name']) ? strip_tags($_POST['name']) : '');
		$email = (isset($_POST['email']) ? strip_tags($_POST['email']) : '');
		$homepage = (isset($_POST['homepage']) ? strip_tags($_POST['homepage']) : '');
		if(strlen(trim($homepage)) > 0 && strpos($homepage, "http://") === false)
			$homepage = "http://" . $homepage; 
		
		if(count($result['missing']) == 0 && (!isset($_POST['mode']) || $_POST['mode'] == 'create'))
		{
			// erstelle neues kommentar
			import('classes.comments.CommentAdminService');
			import('classes.comments.CommentService');
			
			$cas = new CommentAdminService($itemtype);
			$r = $cas->createComment($id,$language,$name,$comment,$email,$homepage);
			if(intval($r) == intval(COMMENT_IS_SPAM))
				$result['spam'] = true;
			else if(intval($r) == intval(COMMENT_DUPLICATE))
				$result['error'] = 'Duplicate! Comment already exists.';
			$result['mode'] = 'create';
		}
		else {
			$preview = array(
				'name' 		=> $name,
				'email'		=> $email,
				'homepage'	=> $homepage,
				'comment'	=> $comment,
				'ip'		=> $_SERVER['REMOTE_ADDR'],
				'timestamp'	=> time(),
				'activated'	=> false,
				'anonymous' => $GLOBALS['_BIGACE']['SESSION']->isAnonymous()
			);
			$result['mode'] = 'preview';
		}
		$smarty->assign($params['admin'], $result);
	}
		
	$smarty->assign($params['preview'], $preview);
	

	if(!is_null($captcha)) {
		$smarty->assign("captcha", $captcha->get());
	}
	
	$values = array(
		'ITEMID' 	=> $id,
		'LANGUAGE'	=> $language,
		'ITEMTYPE'	=> $itemtype,
		'IP'		=> $_SERVER['REMOTE_ADDR']
	);
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('comment_select_language');
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
    $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	
    $comments = array();
    for($i=0; $i < $res->count(); $i++)
    	$comments[] = $res->next();
    
    $smarty->assign($params['assign'], $comments);
}

?>