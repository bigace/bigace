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
 * Returns the URL to a random image from one folder.
 *
 * Parameter:
 * - name		= directory to look for files
 * - extensions	= (optional) comma separated list of allowed file extensions to use. 
 * 				  Default: "gif,png,jpg,jpeg"
 * - assign		= (optional) name of the template variable to assign result to
 */
function smarty_function_readdir($params, &$smarty)
{
	if(!isset($params['name'])) {
		$smarty->trigger_error("readdir: missing 'name' parameter");
		return;
	}
	
	$extensions = isset($params['extensions']) ? $params['extensions'] : "gif,png,jpg,jpeg";
	$image = "";
	$extensions = strtolower($extensions);
	
	$allFiles = array();

	if(is_dir($params['name'])) 
	{
		$handle = opendir($params['name']);
		while (false !== ($file = readdir($handle))) 
		{
			if($file != "." && $file != "..") {
				foreach(explode(",",$extensions) AS $ext) {
					if(strripos(strtolower($file), $ext) !== false)
						$allFiles[$file] = $file;
				}
			}
		}
	}

	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $allFiles);
		return;
	}
	
	return $allFiles;
}
