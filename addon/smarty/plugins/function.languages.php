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

import('classes.language.LanguageEnumeration');
import('classes.util.LinkHelper');

/**
 * Returns an array with all available languages.
 *
 * The array holds the locales as keys and as values the language object itself 
 * OR the translated language name (depends on the parameter).
 * 
 * Parameter:
 * - object = whether only the language name is returned (false = default) or the Language object itself (true)
 * - locale = the locale to display language names with (default _ULC_)
 */
function smarty_function_languages($params, &$smarty)
{
    $languages = array();
    
    $langObjects = (isset($params['object']) && $params['object'] === true) ? true : false;
	$locForName = (isset($params['locale']) ? $params['locale'] : _ULC_);
	
	$enum = new LanguageEnumeration();
	for($i=0; $i < $enum->count(); $i++)
	{
	    $l = $enum->next();
	    $temp = ($langObjects ? $l : $l->getLanguageName($locForName));
	    $languages[$l->getLocale()] = $temp;
	}
	
	if(isset($params['assign'])) {
	    $smarty->assign($params['assign'], $languages);
	    return;
	}
	
	return $languages;
}

