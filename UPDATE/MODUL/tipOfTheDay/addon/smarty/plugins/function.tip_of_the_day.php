<?php

import('classes.tipoftheday.TipOfTheDayService');
    
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.tip_of_the_day.php
 * Type:     function
 * Name:     tip_of_the_day
 * Purpose:  Returns a randomized Tip of the Day   
 * -------------------------------------------------------------
 * - assign
 */
function smarty_function_tip_of_the_day($params, &$smarty)
{
	if(!isset($params['assign'])) {
		$smarty->trigger_error("tip_of_the_day: missing 'assign' attribute");
		return false;			
	}
	
	$totds = new TipOfTheDayService();
	$tip = $totds->getRandomTip();
	$smarty->assign($params['assign'], $tip);
	return;
}
