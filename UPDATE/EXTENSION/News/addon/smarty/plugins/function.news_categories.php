<?php

import('classes.category.CategoryTreeWalker');

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.news_categories.php
 * Type:     function
 * Name:     news_categories
 * Purpose:  Returns all available News categories   
 * -------------------------------------------------------------
 * Parameter:
 * - assign (optional)
 */
function smarty_function_news_categories($params, &$smarty)
{
	$entries = array();
	
	$catWalk = new CategoryTreeWalker( ConfigurationReader::getConfigurationValue("news", "category.id") );
	
	for($i=0; $i < $catWalk->count(); $i++)
		$entries[] = $catWalk->next();
	
	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $entries);
		return;
	}
	
	// otherwise we create the required html
	$html = (isset($params['start']) ? $params['start'] : '');	
	$css = (isset($params['css']) ? ' class="'.$params['css'].'"' : '');
	$prefix = (isset($params['prefix']) ? $params['prefix'] : '');
	// html to render behind each link
	$after = (isset($params['suffix']) ? $params['suffix'] : ', ');
	// last is empty, becaue we redner a comma separated list by default
	$last = (isset($params['last']) ? $params['last'] : ''); 
	$amount = count($entries);

	for($i = 0; $i < $amount; $i++)
	{
		$tempCat = $entries[$i];
//		$html .= $prefix . '<a href=""'.$css.' title="'.$tempCat->getName().'">'.$tempCat->getName().'</a>'
		$html .= $prefix . $tempCat->getName()
		       . (($i == ($amount-1)) ? $last : $after) . "\n";
	}
	       
	return $html;
}

