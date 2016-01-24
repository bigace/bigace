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

import('classes.item.ItemRequest');
import('classes.item.ItemRequests');

/**
 * Fetch or display the latest created items.
 * 
 * Parameter:
 * - itemtype
 * - id
 * - language
 * - from
 * - to
 * - amount
 * - order
 * - assign
 * - pre
 * - post
 * - preDate
 * - postDate
 * - description
 * - date
 * - hidden
 */
function smarty_function_last_created_items($params, &$smarty)
{
	$itemtype = (isset($params['itemtype']) ? $params['itemtype'] : _BIGACE_ITEM_MENU);	
	$id = (isset($params['id']) ? $params['id'] : null);	
	$language = (isset($params['language']) ? $params['language'] : $GLOBALS['_BIGACE']['PARSER']->getLanguage());	
	$from = (isset($params['from']) ? $params['from'] : 0);	
	$to = (isset($params['to']) ? $params['to'] : (isset($params['amount']) ? $params['amount'] : 5));	
	$showHidden = (isset($params['hidden']) ? (bool)$params['hidden'] : false); // since 2.7
	
	$ir = new ItemRequest($itemtype);
	$ir->setID($id);
	$ir->setLimit($from, $to);
	$ir->setLanguageID($language);
	if(isset($params['order']))
		$ir->setOrder($params['order']);
	
	if($showHidden)
		$ir->setFlagToExclude(FLAG_TRASH);
		
	$temp = bigace_last_created_items($ir);
	
	$items = array();
	for($i=0; $i < $temp->count(); $i++)
		$items[] = $temp->next();
	
	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $items);
		return;
	}
		
	$pre = (isset($params['pre']) ? $params['pre'] : '<li>');	
	$post = (isset($params['post']) ? $params['post'] : '</li>');
	
	$preDate = (isset($params['preDate']) ? $params['preDate'] : '<i>');	
	$postDate = (isset($params['postDate']) ? $params['postDate'] : '</i>');
	
	// -1 = hide description, 0 = full length, every other value = allowed length of description	
	$desc = (isset($params['description']) ? $params['description'] : -1);	
	$date = (isset($params['date']) ? $params['date'] : null);	
	
	$html = '';
        
	foreach($items AS $lastCreated)
    {
            $html .= $pre . '<a href="' . createMenuLink( $lastCreated->getID() ) . '">' . $lastCreated->getName() . '</a><br/>';
            if ($desc != -1 && strlen($lastCreated->getDescription()) > 0) {
            	if($desc == 0) {
                	$html .= $lastCreated->getDescription();
            	}
                else {
	            	$html .= substr ( $lastCreated->getDescription(), 0, $desc);
	                if (strlen($lastCreated->getDescription()) > ($desc+3))
	                    $html .= '...';
                }
                $html .= '<br />';
            }
            if($date != null)
            	$html .= $preDate . date($date, $lastCreated->getLastDate()) . $postDate;
            $html .= $post;
    }
	return $html;
}
