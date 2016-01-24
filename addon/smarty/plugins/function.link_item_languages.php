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

import('classes.util.LinkHelper');
import('classes.language.ItemLanguageEnumeration');
import('classes.item.ItemService');

/**
 * Returns a list of links to switch the language of the current page.
 * 
 * Parameter:
 * - item       = REQUIRED the item to display links for
 * - assign     = name of the template variable
 * - spacer		= the spacer between the languages
 * - delimiter	= the delimiter between the passed locales
 * - css        = CSS Class to use for every <a> link 
 * - cssLocale = if set to true, each <a> link will have the languages locale set as css class  
 * - hideActive = do not show active language link (default false)
 * - images		= if set to true images will displayed (default false)
 * - names		= if set to false, language names will not be displayed (default true) if images is false, this will always be true!
 * - locale		= the locale to display the language names
 * - directory 	= where the images are taken from (optional)
 */
function smarty_function_link_item_languages($params, &$smarty)
{
	if(!isset($params['item'])) {
		$smarty->trigger_error("link_item_languages: missing 'item' attribute");
		return;
	}
	
	$spacer		= (isset($params['spacer']) ? $params['spacer'] : ' ');
	$delimiter	= (isset($params['delimiter']) ? $params['delimiter'] : ',');
	$css 		= (isset($params['css']) ? $params['css'] : '');
	$cssLocale	= (isset($params['cssLocale']) && ((bool)$params['cssLocale']) === true) ? true : false;
	$hideActive = (isset($params['hideActive']) && ((bool)$params['hideActive']) === true) ? true : false;
	$images 	= (isset($params['images']) && ((bool)$params['images']) === true) ? true : false;
	$names 		= (isset($params['names']) && ((bool)$params['names']) === false) ? false: true;
	$locForName = (isset($params['locale']) ? $params['locale'] : _ULC_);
	$dir 		= (isset($params['directory']) ? $params['directory'] : _BIGACE_DIR_PUBLIC_WEB.'system/images/' );
	$item 		= $params['item'];
	
	if(strlen($css) > 0 && $cssLocale)
		$css .= ' '; 
	
	if($images === false && $names == false)
		$names = true;
	
	$ile = new ItemLanguageEnumeration($item->getItemtypeID(), $item->getID());
	$is = new ItemService($item->getItemtypeID());
	
	$html = '';
	for($i=0; $i < $ile->count(); $i++) 
	{
		$lang = $ile->next();
		if(!$hideActive || $lang->getLocale() != $item->getLanguageID()) 
		{
			$linkItem = $is->getClass($item->getID(), ITEM_LOAD_LIGHT, $lang->getLocale());
			$link = LinkHelper::getCMSLinkFromItem($linkItem);
			$curCss = $css;
			
			if($cssLocale)
				$curCss .=  $lang->getLocale();
			
			$html .= '<a' . ($curCss != '' ? ' class="'.$curCss.'"' : '').' href="'.LinkHelper::getUrlFromCMSLink($link).'" title="'.htmlspecialchars($linkItem->getName()).'">';
			if($images)
				$html .= '<img src="'.$dir.$lang->getLocale().'.gif" alt="'.$lang->getLanguageName($locForName).'" border="0"/>';
			if($names)
				$html .= $lang->getLanguageName($locForName);
			$html .= '</a>';
			if($i+1 < $ile->count())
				$html .= $spacer;
		}
	}
	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $html);
		return;
	}
	return $html;
}
