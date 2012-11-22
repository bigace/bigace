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

import('classes.util.links.ThumbnailLink');

/**
 * Creates the URL for an image thumbnail.
 * 
 * Parameter:
 * - id
 * - item
 * - height
 * - width
 * - language
 * - crop
 * - assign 
 */
function smarty_function_thumbnail($params, &$smarty)
{
	
	if(!isset($params['id']) && !isset($params['item'])) {
		$smarty->trigger_error("thumbnail: neither 'id' nor 'item' attribute is set");
		return;
	}
	
	$height = (isset($params['height']) ? $params['height'] : null);
	$width = (isset($params['width']) ? $params['width'] : null);

	$link = new ThumbnailLink();
	if(isset($params['item'])) {
		$link->setItemID($params['item']->getID());
		$link->setLanguageID($params['item']->getLanguageID());
		$link->setUniqueName($params['item']->getUniqueName());
	} else {
		$link->setItemID($params['id']);
		if(isset($params['language']))
			$link->setLanguageID(isset($params['language']));
	}
	if(isset($params['crop']))
        $link->setCropping((bool)$params['crop']);	
	if($height != null)
		$link->setHeight($height);
	if($width != null)
		$link->setWidth($width);
	
	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], LinkHelper::getUrlFromCMSLink($link));
		return;
	}
	
	return LinkHelper::getUrlFromCMSLink($link);
}
