<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.comments_latest.php
 * Type:     function
 * Name:     comments_latest
 * Purpose:  Fetches an array of the latest Comments
 * -------------------------------------------------------------
 * - assign
 * - order (default: ASC - possible ASC/DESC)
 * - start (default: 0 - possible int)
 * - end (default: 10 - possible int)
 * 
 * - itemtype
 * - itemid
 * - language
 */
function smarty_function_comments_latest($params, &$smarty)
{	
	if(!isset($params['assign'])) {
		$smarty->trigger_error("comments_latest: missing 'assign' attribute");
		return;
	}
	
	$order = (isset($params['order']) && strtolower($params['order']) == 'asc') ? 'ASC' : 'DESC';
	$start = (isset($params['start']) ? $params['start'] : 0);
	$end = (isset($params['end']) ? $params['end'] : (isset($params['amount']) ? $params['amount'] : 10));
	$extension = "";
	$possible = array('itemtype', 'itemid', 'language');
	
	foreach($possible as $p) {
		if(isset($params[$p]))
			$extension .= " AND `".$p."` = '".$params[$p]."' "; 
	}
	
	$values = array(
		'START'		=> intval($start),
		'END'		=> intval($end),
		'IP'		=> $_SERVER['REMOTE_ADDR'],
		'ORDER_BY'	=> $order,
		'EXTENSION' => $extension
	);
	
	if(strlen($extension) == 0)
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('comment_select_latest');
	else
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('comment_select_latest_ext');

    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
    $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	
    $comments = array();
    for($i=0; $i < $res->count(); $i++)
    	$comments[] = $res->next();
    
    $smarty->assign($params['assign'], $comments);
}
?>