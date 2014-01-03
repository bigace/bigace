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

import('classes.util.links.EditorLink');
import('classes.util.LinkHelper');

/**
 * Creates an URL to the given or default Editor.
 * 
 * Parameter:
 * - id
 * - language
 * - editor
 */
function smarty_function_link_editor($params, &$smarty)
{
	if(!isset($params['id'])) {
		$smarty->trigger_error("link_editor: missing 'id' attribute");
		return;
	}	

	if(!isset($params['language'])) {
		$smarty->trigger_error("link_editor: missing 'language' attribute");
		return;
	}	

    $link = new EditorLink($params['id'], $params['language']);
    
    if(isset($params['editor']))
    	$link->setEditor($params['editor']);
    	
    return LinkHelper::getUrlFromCMSLink($link);
}
