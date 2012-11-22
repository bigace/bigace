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
 * @version $Id$
 * @package bigace.smarty
 * @subpackage function
 */

import('classes.util.SmartyLink');
import('classes.util.LinkHelper');
import('classes.menu.MenuService');

/**
 * Prints a configurable Navigation with unlimited level
 *
 * Parameter:
 * - prefix     = prefix HTML for each link
 * - suffix     = suffix HTML for each link
 * - subprefix  = prefix before a sub-menu
 * - subsuffix  = suffix after a sub-menu
 * - item       = the start menu (required!)
 *
 * @since 2.7.6
 */
function smarty_function_navitree2($params, &$smarty)
{
	if(!isset($params['item'])) {
		$smarty->trigger_error("navitree2: missing 'item' attribute");
		return;
	}
	$pre = (isset($params['prefix']) ? $params['prefix'] : '');
	$after = (isset($params['suffix']) ? $params['suffix'] : '');
	$subpre = (isset($params['subprefix']) ? $params['subprefix'] : '');
	$subafter = (isset($params['subsuffix']) ? $params['subsuffix'] : '');
	$startMenu = $params['item'];

	$menuService = new MenuService();
	$menu_info = $menuService->getLightTreeForLanguage($startMenu->getID(), $startMenu->getLanguageID());

	$html = '';
    for ($i=0; $i < $menu_info->count(); $i++)
    {
  		$temp_menu = $menu_info->next();
  		$link = LinkHelper::getCMSLinkFromItem($temp_menu);
  		$html .= $pre . "\n" . '<a href="'.LinkHelper::getUrlFromCMSLink($link).'">'.$temp_menu->getName().'</a>'. "\n";
  		$html .= helper_navitree2_recursive($menuService, $temp_menu, $pre, $after, $subpre, $subafter). "\n";
  		$html .= $after. "\n";
    }
    return $html;
}

function helper_navitree2_recursive($menuService, $startMenu, $prefix, $suffix, $subpre, $subafter) {
    $html = '';

    $menu_info2 = $menuService->getLightTreeForLanguage($startMenu->getID(), $startMenu->getLanguageID());
    if ($menu_info2->count() > 0)
    {
        $html .= $subpre."\n";
        for ($ii=0; $ii < $menu_info2->count(); $ii++)
        {
            $temp_menu = $menu_info2->next();
            $link = LinkHelper::getCMSLinkFromItem($temp_menu);
      		$html .= $prefix . "\n" . '<a href="'.LinkHelper::getUrlFromCMSLink($link).'">'.$temp_menu->getName().'</a>' . "\n";
      		$html .= helper_navitree2_recursive($menuService, $temp_menu, $prefix, $suffix, $subpre, $subafter). "\n";
      		$html .= $suffix. "\n";
        }
        $html .= $subafter."\n";
    }

	return $html;
}

