<?php
/*
 * -------------------------------------------------------------
 * File:     	function.gravatar.php
 * Type:     	function
 * Name:     	gravatar
 * Description: This TAG creates a valid URL to a Gravatar.
 * 
 * See http://en.gravatar.com/ for further information.
 * -------------------------------------------------------------
 * @copyright Copyright (C) 2008 Kevin Papst
 * @see http://www.kevinpapst.de/
 * @license GNU Public License (GPL)
 *
 * This function was originally writen as part of the Comments Extension 
 * for the BIGACE CMS:
 * http://www.bigace.de/
 * http://wiki.bigace.de/bigace:extensions:addon:comments
 * -------------------------------------------------------------
 * Parameter:
 * - email      = the email to fetch the gravatar for (required)
 * - default    = full url to the default image in case of none existing OR 
 *                invalid rating (required, only if "email" is not set)
 * - width      = the images width
 * - rating     = the highest possible rating displayed image [ G | PG | R | X ]
 * - assign     = if you want to assign the URL to a template variable instead 
 *                of returning it directly
 * -------------------------------------------------------------
 * Example usage:
 * 
 * <img src="{gravatar email="example@example.com"}">
 * <img src="{gravatar email="example@example.com" rating="PG" size="40" default="http://www.example.com/gravatar.gif"}">
 *
 * {gravatar email="example@example.com" size="40" assign="gravatarUrl"}
 * <img src="{$gravatarUrl}">
 */
function smarty_function_gravatar($params, &$smarty)
{
	// check for email adress
	if(!isset($params['email']) && !isset($params['default'])) {
		$smarty->trigger_error("gravatar: neither 'email' nor 'default' attribute passed");
		return;
	}
	
	$email = (isset($params['email']) ? trim(strtolower($params['email'])) : '');
	$rating = (isset($params['rating']) ? $params['rating'] : 'R');
	$url = "http://www.gravatar.com/avatar/".md5($email) . "?r=".$rating;
	
	if(isset($params['default']))
		$url .= "&d=".urlencode($params['default']);
	if(isset($params['size']))
		$url .= "&s=".$params['size'];
		
	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $url);
		return;
	}
	
	return $url;
}

?>