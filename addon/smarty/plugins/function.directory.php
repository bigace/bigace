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
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.smarty
 * @subpackage function
 */

/**
 * Returns the Name of a configured BIGACE Directory (given by the Parameter 'name') or the public 
 * directory to link web resources like images, css, javascrpit files ...
 * 
 * Parameter:
 * - name	= the name of the directory to fetch
 * - assign = 
 */
function smarty_function_directory($params, &$smarty)
{
	$dir = BIGACE_URL_PUBLIC.'cid'._CID_.'/';
	
	if(isset($params['name']))
	{ 	
		if(defined('BIGACE_URL_'.strtoupper($params['name'])))
			$dir = constant('BIGACE_URL_'.strtoupper($params['name']));
		else if(defined('_BIGACE_DIR_'.$params['name']))
			$dir = constant('_BIGACE_DIR_'.$params['name']);
		else if(defined('BIGACE_'.$params['name']))
			$dir = constant('BIGACE_'.$params['name']);
		else if(defined('_BIGACE_DIR_'.strtoupper($params['name'])))
			$dir = constant('_BIGACE_DIR_'.strtoupper($params['name']));
		else if(defined('BIGACE_'.strtoupper($params['name'])))
			$dir = constant('BIGACE_'.strtoupper($params['name']));
		else if(isset($GLOBALS['_BIGACE']['DIR'][$params['name']]))	
		    $dir = $GLOBALS['_BIGACE']['DIR'][$params['name']];
	}
	
	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $dir);
		return;
	}
	   
	return $dir;
}
