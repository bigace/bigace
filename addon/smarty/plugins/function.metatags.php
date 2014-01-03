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
 * Metatags dumps all the tags you get from the menu-page administration.
 *
 * Parameter:
 * item 		= MENU    - required, the item to show the metatags for
 * assign 		= STRING  - name of tpl variable to assign the javascript code to
 * prefix		=
 * author		= defines author meta tag if set, otherwise will be skipped
 */
function smarty_function_metatags($params, &$smarty)
{
	if(!isset($params['item'])) {
		$smarty->trigger_error("metatags: missing 'item' attribute");
    	return;
	}

	$item = $params['item'];
    $prefix = isset($params['prefix']) ? $params['prefix'] : '   ';
    $author = isset($params['author']) ? $params['author'] : '';

    $values = array(
        'description'   => $item->getDescription(),
        'generator'     => 'BIGACE '._BIGACE_ID,
        'author'        => $author,
        'robots'        => 'index,follow',
        'title'         => $item->getName()
    );

    $values = Hooks::apply_filters('metatags', $values, $item);

	$badge  = $prefix . "<title>".htmlspecialchars($values['title'], ENT_NOQUOTES)."</title>\n";
    $badge .= $prefix . '<meta name="description" content="'.htmlspecialchars($values['description']).'" />'."\n";
    $badge .= $prefix . '<meta name="robots" content="'.htmlspecialchars($values['robots']).'" />'."\n";
    $badge .= $prefix . '<meta name="generator" content="'.htmlspecialchars($values['generator']).'" />'."\n";
    $badge .= $prefix . '<link rel="canonical" href="'.LinkHelper::itemUrl($params['item']).'"/>'."\n";

    if(isset($values['author']) && trim(strlen($values['author'])) > 0)
        $badge .= $prefix . '<meta name="author" content="'.htmlspecialchars($values['author']).'" />'."\n";

    $additional = Hooks::apply_filters('metatags_more', array(), $item);

    foreach($additional as $entry) {
        $badge .= $prefix . $entry . "\n";
    }

    if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $badge);
		return;
	}

    return $badge;
}
