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
 * Smarty plugin to redirect to a special URL.
 * This TAG works ONLY if nothing else has echo'ed before, becuase it sends a HTTP header.
 * 
 * Parameter:
 * - url the URL to redirect to
 */
function smarty_function_redirect($params, &$smarty)
{
    if(!isset($params['url'])) {
		$smarty->trigger_error("redirect: missing 'url' attribute");
		return;
	}

    header('Location: ' . $params['url']);
}
