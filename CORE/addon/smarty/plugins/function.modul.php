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

import('classes.modul.Modul');

/**
 * Load the Modul with the given name.
 *
 * Parameter:
 *
 * - language 	(optional)
 * - name 		(optional)
 * - menu
 */
function smarty_function_modul($params, &$smarty)
{
	$lang = (isset($params['language']) ? $params['language'] : $GLOBALS['_BIGACE']['PARSER']->getLanguage());

    // load the explicit given modul if passed
    $name = isset($params['name']) ? $params['name'] : null;

    // otherwise use menus configured modul if menu was passed, last fallback use current menus one
    if(is_null($name)) {
    	if(isset($params['menu']))
    		$name = $params['menu']->getModulID();
    	else if (isset($GLOBALS['MENU']))
    		$name = $GLOBALS['MENU']->getModulID();
    }

    $mod = new Modul($name);

    if($mod != null && is_object($mod)) {
        if ($mod->isTranslated()) {
            $mod->loadTranslation($lang);
        }

        if(file_exists($mod->getFullURL())) {
        	// this variable is required in many modul
        	$MENU = &$GLOBALS['MENU'];
            include($mod->getFullURL());
            return;
        }
    }

	$smarty->trigger_error("modul: could not load modul '".$name."'.");
	return;
}
