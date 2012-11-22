<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.comment_spam_counter.php
 * Type:     function
 * Name:     comment_spam_counter
 * Purpose:  Counts the amount of Spam for the Community
 * -------------------------------------------------------------
 * - assign
 */
function smarty_function_comment_spam_counter($params, &$smarty)
{	
	$values = array(
	);
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('comment_spam_count');
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