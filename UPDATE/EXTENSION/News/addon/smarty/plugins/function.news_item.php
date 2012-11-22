<?php

import('classes.news.News');

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.news_item.php
 * Type:     function
 * Name:     load_item
 * Purpose:  Load one News item and assign it to a Smarty variable
 * -------------------------------------------------------------
 * - id
 * - assign
 * - language (optional)
 */
function smarty_function_news_item($params, &$smarty)
{
	if(!isset($params['id'])) {
		$smarty->trigger_error("news_item: missing 'id' attribute");
		return;
	}	
	if(!isset($params['assign'])) {
		$smarty->trigger_error("news_item: missing 'assign' attribute");
		return;
	}
	$lang = (isset($params['language']) ? $params['language'] : ConfigurationReader::getConfigurationValue("news", "default.language"));	
	
	$smarty->assign($params['assign'], new News($params['id'], $lang));
	
	return;
}

