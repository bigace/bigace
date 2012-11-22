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
 * @author Ralf Paprotny
 * @version $Id$
 * @package bigace.smarty
 * @subpackage function
 */
 
import('classes.util.SmartyLink');
import('classes.util.LinkHelper');
import('classes.menu.MenuService');

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.navigation.php
 * Type:     function
 * Name:     navigation
 * Purpose:  Prints a configurable Navigation
 * -------------------------------------------------------------
 */
function smarty_function_navitree($params, &$smarty)
{
	$id = (isset($params['id']) ? $params['id'] : $GLOBALS['_BIGACE']['PARSER']->getItemID());	
	$lang = (isset($params['language']) ? $params['language'] : $GLOBALS['_BIGACE']['PARSER']->getLanguage());	
	$css = (isset($params['css']) ? ' class="'.$params['css'].'"' : '');	
	$selected = (isset($params['selected']) ? ' class="'.$params['selected'].'"' : '');
	$pre = (isset($params['prefix']) ? $params['prefix'] : '');	
	$after = (isset($params['suffix']) ? $params['suffix'] : '');	
	$html = (isset($params['start']) ? $params['start'] : '');	
	$subpre = (isset($params['subprefix']) ? $params['subprefix'] : '');	
	$subafter = (isset($params['subsuffix']) ? $params['subsuffix'] : '');	
	
	$ms = new MenuService();
	$menu_info = $ms->getLightTreeForLanguage($id, $lang);

	if(isset($params['counter']))
		$smarty->assign($params['counter'], $menu_info->count());

    for ($i=0; $i < $menu_info->count(); $i++) 
    {
  		$class = $css;
  		$temp_menu = $menu_info->next();
  		if ($temp_menu->getID() == $GLOBALS['MENU']->getID() || $ms->isChildOf($temp_menu->getID(), $GLOBALS['MENU']->getID())) {
  			$class = $selected;
  		}
  		$link = LinkHelper::getCMSLinkFromItem($temp_menu);
      /*$link = new SmartyLink();
  		$link->setItemID($temp_menu->getID());
  		$link->setLanguageID($temp_menu->getLanguageID());*/
  		$html .= $pre . "<a href=\"".LinkHelper::getUrlFromCMSLink($link)."\"".$class.">".$temp_menu->getName()."</a>";
      $html .= $after."\n";   
  		if ($temp_menu->getID() == $GLOBALS['MENU']->getID() || $ms->isChildOf($temp_menu->getID(), $GLOBALS['MENU']->getID())) {
  		  $menu_info2 = $ms->getLightTreeForLanguage($temp_menu->getID(), $lang);
  		  if ($menu_info2->count()>0) $html .= $subpre."\n";
        for ($ii=0; $ii < $menu_info2->count(); $ii++)
        {
      		$class = $css;
      		$temp_menu2 = $menu_info2->next();
      		if ($temp_menu2->getID() == $GLOBALS['MENU']->getID() || $ms->isChildOf($temp_menu2->getID(), $GLOBALS['MENU']->getID())) {
      			$class = $selected;
      		}
      		$link = LinkHelper::getCMSLinkFromItem($temp_menu2);
      		$html .= "<li class='level1' style='padding-left: 6px;'><a href=\"".LinkHelper::getUrlFromCMSLink($link)."\"".$class.">".$temp_menu2->getName()."</a>".$after."\n";
      		if ($temp_menu2->getID() == $GLOBALS['MENU']->getID() || $ms->isChildOf($temp_menu2->getID(), $GLOBALS['MENU']->getID())) {
      		  $menu_info3 = $ms->getLightTreeForLanguage($temp_menu2->getID(), $lang);
            if ($menu_info3->count()>0) $html .= $subpre."\n";
            for ($iii=0; $iii < $menu_info3->count(); $iii++)
            {
          		$class = $css;
          		$temp_menu3 = $menu_info3->next();
          		if ($temp_menu3->getID() == $GLOBALS['MENU']->getID() || $ms->isChildOf($temp_menu3->getID(), $GLOBALS['MENU']->getID())) {
          			$class = $selected;
          		}
          		$link = LinkHelper::getCMSLinkFromItem($temp_menu3);
          		$html .= '<li class="level2" style="padding-left:10px;"><a href="'.LinkHelper::getUrlFromCMSLink($link).'"'.$class.'>'.$temp_menu3->getName().'</a>'.$after."\n";
            }
      		  if ($menu_info3->count()>0) $html .= $subafter."\n";
      		}
        }
   		  if ($menu_info2->count()>0) $html .= $subafter."\n";
  		}
    }
    return $html . (isset($params['end']) ? $params['end'] : '');
}

