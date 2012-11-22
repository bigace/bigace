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
 * Returns the BIGACE Version ID
 *    
 * Parameter: 
 * "full"   displays the full Version String including Application Name
 * "build"  return the BUILD ID also
 * "link"   returns a complete HTML Link to the official BIGACE Homepage
 * 
 * All parameter only needs to be set, the values will be ignored. 
 */
function smarty_function_bigace_version($params, &$smarty)
{
	$html = _BIGACE_ID;

	if(isset($params['full']))
		$html = 'BIGACE ' . $html;
	if(isset($params['build']))
		$html .= ' ' . _BIGACE_BUILD_ID;		
	if(isset($params['link']))
		$html = '<a href="http://www.bigace.de/" title="BIGACE Web CMS - Free PHP Content Management System" target="_blank">'.$html.'</a>';
		 
	return $html;
}
