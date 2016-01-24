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

import('classes.util.links.SessionLanguageLink');
import('classes.util.LinkHelper');
import('classes.language.Language');

/**
 * Returns the configured Languages as Flag images with a link to switch 
 * the session language
 * 
 * Parameter:
 * - languages 	= comma separated list of locales (required!)
 * - directory 	= where the images are taken from (optional)
 * - hideActive = do not show active language link (default false)
 * - images		= if set to false the language names will be displayed (default true)
 * - locale		= the locale to display the language names
 * - delimiter	= the delimiter between the passed locales
 * - spacer		= the spacer between the languages
 * - alt		= the alt attribute for the images (one for all)
 * - altexts	= delimiter separated list of alt texts for the images (order of text must be order as given in languages parameter)
 * - title		= title attributes for the links
 * - id			= menu ID if you don't want to link to the current page
 * - css		= css class to be used
 */
function smarty_function_switch_language($params, &$smarty)
{
	if(!isset($params['languages'])) {
		$smarty->trigger_error("switch_language: missing 'languages' attribute");
		return;
	}
	
	$title		= (isset($params['title']) ? $params['title'] : '');
	$alt		= (isset($params['alt']) ? $params['alt'] : '');
	$spacer		= (isset($params['spacer']) ? $params['spacer'] : ' ');
	$delimiter	= (isset($params['delimiter']) ? $params['delimiter'] : ',');
	$alttexts	= (isset($params['alttexts']) ? explode($delimiter, $params['alttexts']) : array());
	$css 		= (isset($params['css']) ? ' class="'.$params['css'].'"' : '');
	$hideActive = (isset($params['hideActive']) && $params['hideActive'] == 'true') ? true : false;
	$images 	= (isset($params['images']) && strtolower($params['images']) == 'false') ? false : true;
	$languages 	= explode($delimiter, $params['languages']);
	$locForName = (isset($params['locale']) ? $params['locale'] : _ULC_);
	$dir 		= (isset($params['directory']) ? $params['directory'] : _BIGACE_DIR_PUBLIC_WEB.'system/images/' );
	
	$counter = count($languages);
	if($hideActive && in_array(_ULC_,$languages))
		$counter--;
	$counter--;
		
	$html = '';
	for( $i = 0 ; $i < count($languages) ; $i++ ) 
	{
		$locale = $languages[$i];
		if(!$hideActive || $locale != _ULC_) 
		{
			$link = new SessionLanguageLink($locale);
			if(isset($params['id']))
				$link->setItemID($params['id']);
			//$link->setReturnToCommand(_BIGACE_CMD_SMARTY);
			$html .= '<a'.$css.' href="'.LinkHelper::getUrlFromCMSLink($link).'" title="'.$title.'">';
			if($images) {
				$altText = $alt;
				if($i < count($alttexts) )
					$altText = $alttexts[$i];
				$html .= '<img src="'.$dir.$locale.'.gif" alt="'.$altText.'" border="0"/>';
			} else {
				$l = new Language($locale);
				$html .= $l->getLanguageName($locForName);
			}
			$html .= '</a>';
			if($counter-- > 0)
				$html .= $spacer;
		}
	}		
	return $html;
}
