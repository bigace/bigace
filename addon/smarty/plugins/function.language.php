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

import('classes.language.Language');

/**
 * Returns the Language for the given ID.
 *
 * Parameter:
 * - id     = the language to fetch
 * - assign = name of the template variable to assign the language to
 */
function smarty_function_language($params, &$smarty)
{
    if (!isset($params['id'])) {
        $smarty->trigger_error("language: missing 'id' attribute");
        return null;
    }

    import('classes.language.Language');
    $lang = new Language($params['id']);

    if (!$lang->isValid()) {
        return null;
    }

    if(isset($params['assign'])) {
        $smarty->assign($params['assign'], $lang);
        return;
    }
    return $lang;
}

