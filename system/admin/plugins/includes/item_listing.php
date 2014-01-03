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
 * BIGACE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation, 
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.administration.item
 */

import('classes.item.ItemRequest');

define('LIMIT_START', 0);
define('LIMIT_END', 50);

/**
 * Default listing can be used for all itemtypes.
 */
function createFileListing($data)
{
    $languageID = _ULC_;
	//$languageID = (isset($data['langid']) ? $data['langid'] : $GLOBALS['_BIGACE']['SESSION']->getLanguageID());

    // Item for Menu browsing
	$item = $GLOBALS['_SERVICE']->getItem(_BIGACE_TOP_LEVEL);    
	
	$req = new ItemRequest($GLOBALS['_SERVICE']->getItemType(), null); //$item->getID());

	//$item = $GLOBALS['_SERVICE']->getItem($data['id']);    
	//$item = $GLOBALS['_SERVICE']->getItem($data['id'], ITEM_LOAD_FULL, $languageID);

    // get childs for menu    
    //$items = $GLOBALS['_SERVICE']->getTree($item->getID());
	//$items = $GLOBALS['_SERVICE']->getTreeForLanguage($item->getID(), $languageID);
	
	$orderBy = isset($data['orderBy']) ? $data['orderBy'] : "name"; //ORDER_COLUMN_POSITION;
	switch($orderBy) {
		case 'name': 
			$orderBy = "name";
			break;
		default:  
			$orderBy = ORDER_COLUMN_POSITION;
			break;
	}
	$data['orderBy'] = $orderBy;
	
	$order = isset($data['order']) ? $data['order'] : $req->_ORDER_ASC;
	switch($order) {
		case 'desc': 
			$order = $req->_ORDER_DESC;
			break;
		default:  
			$order = $req->_ORDER_ASC;
			break;
	}
	$data['order'] = $order;

	$start =  isset($data['limitFrom']) ? $data['limitFrom'] : LIMIT_START;
    $data['limitFrom'] = $start;

	$end = isset($data['limitTo']) ? $data['limitTo'] : LIMIT_END; 
    $data['limitTo'] = $end;
	
	if ( isset($data['langid']) ) {
		$req->setLanguageID($data['langid']);
	} 
	
	$req->setLimit($data['limitFrom'], $data['limitTo']);

	$req->setTreetype(ITEM_LOAD_FULL);
	$req->setOrderBy($data['orderBy']);
	$req->setOrder($data['order']);
    $req->setFlagToExclude($req->FLAG_ALL_EXCEPT_TRASH);
	
	$items = new SimpleItemTreeWalker($req);
		
	showFileListing($items, $data);
}


function showFileListing($items, $data)
{
    if(!isset($data['id'])) $data['id'] = _BIGACE_TOP_LEVEL;

	$limitFrom = (isset($data['limitFrom'])) ? $data['limitFrom'] : LIMIT_START;
	$limitTo = (isset($data['limitTo'])) ? $data['limitTo'] : LIMIT_END;

	$totalItems = $GLOBALS['_SERVICE']->countAllItems();

	// when there is at least one item - display items
    if ($totalItems > 0)
    {
        echo '<div style="margin-bottom:5px">';
		$langEnum = new LanguageEnumeration();
		for($i=0; $i < $langEnum->count(); $i++) 
		{
			$tempLanguage = $langEnum->next();
			echo '<a href="'.createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => _BIGACE_TOP_LEVEL, 'adminCharset' => $tempLanguage->getCharset(), 'data[langid]' => $tempLanguage->getID(), 'mode' => _MODE_BROWSE_MENU)).'"><img alt="'.$tempLanguage->getName().'" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'languages/'.$tempLanguage->getLocale() . '.gif" style="border-width:0px"></a>&nbsp;';
			unset($tempLanguage);
		}
		unset($langEnum);
	
        // ------------------------------------- [START] ----------------------------------------
	    echo '<form id="nextPage" action="'.createAdminLink($GLOBALS['MENU']->getID()).'" method="POST">';
	    echo '<input type="hidden" id="itemFrom" name="data[limitFrom]" value="'.$limitFrom.'">';
	    echo getTranslation('entrys_total').': <b>'.$totalItems.'</b>';
	    echo ' | ';
	    echo getTranslation('entrys_page').': <select id="itemTo" name="data[limitTo]">';
	    echo '<option value=""></option>';
	    $ii = 5;
	    while($ii < 51) {
	    	echo '<option value="'.$ii.'"';
	    	if($limitTo != '' && $limitTo == $ii)
		    	echo ' selected';
			echo '>'.$ii.'</option>';
			$ii += 5;
	    }
	    
	    $ii = 60;
	    while($ii < 101) {
	    	echo '<option value="'.$ii.'"';
	    	if($limitTo != '' && $limitTo == $ii)
		    	echo ' selected';
			echo '>'.$ii.'</option>';
			$ii += 10;
	    }
	    $ii = 150;
	    while($ii < 501) {
	    	echo '<option value="'.$ii.'"';
	    	if($limitTo != '' && $limitTo == $ii)
		    	echo ' selected';
			echo '>'.$ii.'</option>';
			$ii += 50;
	    }
	    echo '</select> ';
	    	    
	    echo getTranslation('sort_orderby').': <select name="data[orderBy]">';
	    echo '<option value="name"'.(strtolower($data['orderBy']) == "name" ? " selected" : "").'>'.getTranslation('name').'</option>';
	    echo '<option value="position"'.($data['orderBy'] == ORDER_COLUMN_POSITION ? " selected" : "").'>'.getTranslation('position').'</option>';
	    echo '</select> ';

	    echo getTranslation('sort_order').': <select name="data[order]">';
	    echo '<option value="asc"'.(strtolower($data['order']) == "asc" ? " selected" : "").'>'.getTranslation('sort_asc').'</option>';
	    echo '<option value="desc"'.(strtolower($data['order']) == "desc" ? " selected" : "").'>'.getTranslation('sort_desc').'</option>';
	    echo '</select> ';
	    
	    echo '<button type="submit">'.getTranslation('show').'</button>';
		echo '</form>';
        if(has_permission('item_default_permission')) {
            echo '<form action="'.createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => _BIGACE_TOP_LEVEL, 'mode' => 'defaultPermissions')).'" method="post" style="float:right">';
	        echo '<button type="submit" title="'.getTranslation('defaultPermissions_'.$GLOBALS['_ITEMTYPE']).'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'rights.png" alt="'.getTranslation('defaultPermissions_'.$GLOBALS['_ITEMTYPE']).'" title="'.getTranslation('defaultPermissions_'.$GLOBALS['_ITEMTYPE']).'"></button>';
	        echo '</form>';
        }
        
        echo '</div>';
        // -------------------------------------- [END] -----------------------------------------

		if ($totalItems > $items->count()) 
		{
			// calculate amount of pages
			$pages = 1;
            if($limitTo != '')
            {
                $pages = (int)($totalItems / $limitTo);
    			if (($totalItems % $limitTo) > 0)
    				$pages++;
            }
	
			// if there is at least one page
			if($pages > 1)
			{
                echo '<div id="pages" style="margin-bottom:5px">'.getTranslation('page') . ' ';
				for($i=0; $i < $pages; $i++)
				{
					if ($limitFrom != $i*$limitTo) {
						//echo '<a href="'.createAdminLink($GLOBALS['MENU']->getID(), array('data[limitTo]' => $limitTo, 'data[limitFrom]' => $i*$limitTo)).'">';
						echo '<a href="#" onClick="changePage(\''.$limitTo.'\', \''.($i*$limitTo).'\')">';
					} else {
						echo '<b>';
					}
					echo ($i+1);
					if ($limitFrom != $i*$limitTo) {
						echo '</a>';
					} else {
						echo '</b>';
					}
					echo ' ';
				}
                echo '</div>';
			}
		}
		
	    //$item = $GLOBALS['_SERVICE']->getItem($data['id']);
	    
	/*  ######################
	    ## CREATE FILE LIST ##
	    ###################### */
	    
	    // Title for the Items Box
	    $tools = array();

        $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("ItemListing.tpl.htm", true, true);
        $tpl->setVariable('TABLE_WIDTH', '100%');
        $tpl->setVariable('JS_MODE_ZIP', _MODE_DOWNLOAD_AS_ZIP);
        $tpl->setVariable('JS_DEFAULT_ACTION', createAdminLink($GLOBALS['MENU']->getID()));
        $tpl->setVariable('JS_DOWNLOAD_ACTION', createCommandLink('download', '0', array('itemtype' => $GLOBALS['_ITEMTYPE']), 'files.zip'));
        $tpl->setVariable('FORM_ACTION', createAdminLink($GLOBALS['MENU']->getID()));
        $tpl->setVariable('ITEM_ID', $data['id']);

	    $cssClass = "row1";
	
	    $a = $items->count();
	
        $tpl->setCurrentBlock('row');

	    for ($i=0; $i < $a; $i++)
	    {
	        $item = $items->next();
	        $tools = getToolLinksForItem($item);
            $tpl->setVariable('CSS', $cssClass);
            $tpl->setVariable('HISTORY_CHECKBOX', (isset($tools['history']) ? $tools['history'] : ''));
            if(isset($tools['admin_link']))            
                $tpl->setVariable('ITEM_NAME', '<a href="'.$tools['admin_link'].'">' . $tools['name'] . '</a>');
            else 
                $tpl->setVariable('ITEM_NAME', $tools['name']);
            $tpl->setVariable('ITEM_URL', $item->getUniqueName());
            $tpl->setVariable('ITEM_MIMETYPE', ((isset($tools['mimetype']) && $tools['mimetype'] != '') ? '<img src="'.$tools['mimetype'].'" title="'.strtoupper(getFileExtension($item->getOriginalName())).'" width="16" height="16" border="0">' : '<span style="padding-right:16px"></span>'));
            $tpl->setVariable('ITEM_ADMIN', (isset($tools['admin']) ? $tools['admin'] : ''));
            $tpl->setVariable('ITEM_PREVIEW', (isset($tools['preview']) ? $tools['preview'] : ''));
            $tpl->setVariable('ITEM_DOWNLOAD', (isset($tools['download']) ? $tools['download'] : ''));
            $tpl->setVariable('ITEM_RIGHTS', (isset($tools['rights']) ? $tools['rights'] : ''));
            $tpl->setVariable('ITEM_DELETE', (isset($tools['delete']) ? $tools['delete'] : ''));
            $tpl->setVariable('ITEM_UP', ($i > 0 && isset($tools['up']) ? $tools['up'] : ''));
            $tpl->setVariable('ITEM_DOWN', ($i+1 < $a && isset($tools['down']) ? $tools['down'] : ''));
	
			$cssClass = ($cssClass == "row1") ? "row2" : "row1";
            $tpl->parseCurrentBlock();
	    }
	
	    $modeList = array(
	        ''                                  => '',
	        getTranslation('delete')            => _MODE_DELETE_MULTIPLE,
	        getTranslation('updatemode_3')      => _MODE_DELETE_HISTORY_MENU,
	        getTranslation('mode_download')     => _MODE_DOWNLOAD_AS_ZIP,
            getTranslation('update_multiple')   => _MODE_UPDATE_MULTIPLE,
	    );

        $tpl->setVariable('OPTION_MODE_SELECT', createNamedSelectBox('mode', $modeList));
        $tpl->setVariable('LAST_CSS', $cssClass);
        $tpl->show();

	    unset ($modeList);

    }
    else
    {
    	echo '<b>'.getTranslation('error_no_items').'</b>';
    }
}

?>

