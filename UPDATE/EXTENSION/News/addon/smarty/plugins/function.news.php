<?php

import('classes.item.ItemRequest');
import('classes.item.SimpleItemTreeWalker');
import('classes.news.News');

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.news.php
 * Type:     function
 * Name:     news
 * Purpose:  Fetches an array of plain News items
 * -------------------------------------------------------------
 * - orderby
 * - order
 * - assign
 * - counter
 * - start / end
 * - category
 * - limit (null or 0 will be skipped)
 * - language (optional) 
 *  */
function smarty_function_news($params, &$smarty)
{	
	if(!isset($params['assign'])) {
		$smarty->trigger_error("news: missing 'assign' attribute");
		return;
	}
	
	$itemtype = _BIGACE_ITEM_MENU;
	$id = ConfigurationReader::getConfigurationValue("news", "root.id");
	$lang = (isset($params['language']) ? $params['language'] : ConfigurationReader::getConfigurationValue("news", "default.language"));
	$order = (isset($params['order']) ? $params['order'] : 'DESC');
	$orderby = (isset($params['orderby']) ? $params['orderby'] : "date_2");
	
	$from = (isset($params['start']) ? $params['start'] : 0);
	$to = (isset($params['end']) ? $params['end'] : (isset($params['limit']) ? $params['limit'] : null));
	
	if($id == null) {
		$smarty->trigger_error("news: Configuration news/root.id not set");
		return;
	}

	$ir = new ItemRequest($itemtype);
	$ir->setID($id);
	$ir->setLanguageID($lang);
	$ir->setReturnType("News");
	$ir->setOrderBy($orderby);
	$ir->setOrder($order);
	if(isset($params['hidden']) && $params['hidden'] === true)
		$ir->setFlagToExclude($ir->FLAG_ALL_EXCEPT_TRASH);
	
	if($to != null && $to != 0)
		$ir->setLimit($from, $to);
	
	if(isset($params['category']) && $params['category'] != '') {
		if(strpos($params['category'], ",") === FALSE) {
			$ir->setCategory($params['category']); 		
		}
		else {
			$tmp = explode(",", $params['category']);
			foreach($tmp AS $x)	{
				$ir->setCategory($x);
			} 		
		}
	}
	
	$menu_info = new SimpleItemTreeWalker($ir);

	$items = array();

	if(isset($params['counter']))
		$smarty->assign($params['counter'], $menu_info->count());

    for ($i=0; $i < $menu_info->count(); $i++) {
		$items[] = $menu_info->next();
    }
    
	$smarty->assign($params['assign'], $items);
}
