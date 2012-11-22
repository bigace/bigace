<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.comment_counter.php
 * Type:     function
 * Name:     news
 * Purpose:  Counts the amount of Comments for one Item
 * -------------------------------------------------------------
 * - assign
 * - order (ASC/DESC)
 */
function smarty_function_comment_counter($params, &$smarty)
{	
	if(isset($params['item']))
	{
		$itemtype = $params['item']->getItemTypeID(); 
		$id = $params['item']->getID();
		$language = $params['item']->getLanguageID();
	}
	else
	{
		if(!isset($params['id']) || !isset($params['language']) ) {
			$smarty->trigger_error("comment_counter: missing 'id' or 'language' attribute");
			return;
		}
		$itemtype = (isset($params['itemtype']) ? $params['itemtype'] : _BIGACE_ITEM_MENU); 
		$id = $params['id'];
		$language = $params['language'];
	}
	
	$values = array(
		'ITEMID' 	=> $id,
		'LANGUAGE'	=> $language,
		'ITEMTYPE'	=> $itemtype
	);
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('comment_count_language');
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
    $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    $res = $res->next();
    
    if(isset($params['assign'])) {
    	$smarty->assign($params['assign'], $res['counter']);
    	return;
    }
    return $res['counter'];
}

?>