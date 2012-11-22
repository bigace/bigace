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
 * @package bigace.administration
 */

/**
 * Script for reading Statistics
 */

check_admin_login();

loadLanguageFile('search', ADMIN_LANGUAGE);

admin_header();

// TODO check permissions !!!


import('classes.item.ItemRequest');
import('classes.item.ItemRequests');
import('classes.search.ItemSearch');
import('classes.item.Itemtype');
import('classes.util.LinkHelper');
import('classes.util.CMSLink');


function sanitizeSearchTerm($str) {
    $str = stripslashes(str_replace('"', '', $str));
    $str = str_replace("'", '', $str);
    return addslashes($str);
}

function enum_to_array($itemtype, $enum)
{
	$res = array();
	for($i = 0; $i < $enum->count(); $i++) {
		$temp = $enum->next();
		$tmpArray['name'] = $temp->getName();
		$tmpArray['unique_name'] = $temp->getUniqueName();
		$tmpArray['language'] = $temp->getLanguageID();
		$tmpArray['id'] = $temp->getID();
		$tmpArray['filename'] = $temp->getOriginalName();
        $tmpArray['catchwords'] = $temp->getCatchwords();
        $tmpArray['description'] = $temp->getDescription();
        $tmpArray['content'] = $temp->getItemText('5');

		$res[] = prepare_result($itemtype, $tmpArray);
	}
	return $res;
}

function prepare_result($itemtype, $resTemp)
{
	$adminMenu = 'itemMenu';
	if($itemtype == _BIGACE_ITEM_IMAGE)
		$adminMenu = _ADMIN_ID_IMAGE_MAIN;
	if($itemtype == _BIGACE_ITEM_FILE)
		$adminMenu = _ADMIN_ID_FILE_MAIN;

	$adminLink = createAdminLink(
	   $adminMenu,
	   array(
			'data[id]' => $resTemp['id'],
			'data[langid]' => $resTemp['language'],
			'mode' => 'changeattrib',
	   )
	);

    $realItemtype = new Itemtype($itemtype);

    $resultLink = new CMSLink();
    $resultLink->setCommand($realItemtype->getCommand());
    $resultLink->setItemID($resTemp['id']);
    $resultLink->setLanguageID($resTemp['language']);
    $resultLink->setUniqueName($resTemp['unique_name']);
    if($itemtype != _BIGACE_ITEM_MENU)
    	$resultLink->setFilename($resTemp['filename']);

    $resTemp['preview']		= LinkHelper::getUrlFromCMSLink($resultLink);
    $resTemp['url'] 		= $adminLink;
    $resTemp['mimetype'] 	= getFileExtension($resTemp['filename']);
	return $resTemp;
}

function searchItem($itemtype, $langid, $search, $limit)
{
	$results = array();

    $columns = array(
    	'unique_name'	=> 'unique_name',
    	'content'		=> 'text_5',
    	'filename'		=> 'text_1'
    );

    if ($search != '')
    {
        $searcher = new ItemSearch($itemtype, $langid);
        $searcher->resetIgnoreFlags();

		//we probably want to find even deleted items
		//$searcher->addIgnoreFlag(FLAG_TRASH);

        if($limit > 0)
        	$searcher->setLimit( $limit );

        foreach($columns AS $key => $value)
        	$searcher->addResultColumn($value);

        $searchResult = $searcher->search( $search );

        $columns['catchwords'] = 'catchwords';
        $columns['description'] = 'description';
        $columns['name'] = 'name';
        $columns['id'] = 'id';
        $columns['language'] = 'language';
        $columns['content'] = 'text_5';

        if (count($searchResult) > 0)
        {
            for ($i = 0; $i < count($searchResult); $i++)
            {
            	$resTemp = array();

            	foreach($columns AS $key => $value) {
            		$resTemp[$key] = $searchResult[$i]->getResultColumn($value);
            	}

				$resTemp = prepare_result($itemtype, $resTemp);
                $results[] = $resTemp;
            }
        }

        $searcher->finalize();
    }

    return $results;
}

// ------------------------------------------------------------

$allowMenu = (has_permission(_BIGACE_FRIGHT_ADMIN_MENUS) || has_permission('edit_menus'));
$allowImage = (has_permission(_BIGACE_FRIGHT_ADMIN_ITEMS) || has_permission('edit_items'));
$allowFile = (has_permission(_BIGACE_FRIGHT_ADMIN_ITEMS) || has_permission('edit_items'));
$allowUser = false;// not implemented! (has_permission('edit_own_profile') || has_permission('admin_users'));

$limitStart = 0;
$limitEnd = 10;

$searchTerm = trim(isset($_POST['query']) ? $_POST['query'] : '');
$searchTerm = sanitizeSearchTerm($searchTerm);

$resultsMenu = array();
$resultsImage = array();
$resultsFile = array();
// currently not supported
$resultsUser = array();
$searchPerformed = false;

if(strcmp(intval($searchTerm),$searchTerm) == 0)
{
	$searchID = intval($searchTerm);

	if($allowMenu) {
		$ir = new ItemRequest(_BIGACE_ITEM_MENU, $searchTerm);
		$ir->setLimit($limitStart,$limitEnd);
		$res = bigace_find_by_id($ir);
		$resultsMenu = enum_to_array(_BIGACE_ITEM_MENU, $res);
	}

	if($allowImage) {
		$ir = new ItemRequest(_BIGACE_ITEM_IMAGE, $searchTerm);
		$ir->setLimit($limitStart,$limitEnd);
		$res = bigace_find_by_id($ir);
		$resultsImage = enum_to_array(_BIGACE_ITEM_IMAGE, $res);
	}

	if($allowFile) {
		$ir = new ItemRequest(_BIGACE_ITEM_FILE, $searchTerm);
		$ir->setLimit($limitStart,$limitEnd);
		$res = bigace_find_by_id($ir);
		$resultsFile = enum_to_array(_BIGACE_ITEM_FILE, $res);
	}

	$searchPerformed = true;
}
else if(strlen($searchTerm) > 3)
{
	if($allowMenu)
		$resultsMenu 	= searchItem(_BIGACE_ITEM_MENU, null, $searchTerm, $limitEnd);
	if($allowImage)
		$resultsImage 	= searchItem(_BIGACE_ITEM_IMAGE, null, $searchTerm, $limitEnd);
	if($allowFile)
		$resultsFile 	= searchItem(_BIGACE_ITEM_FILE, null, $searchTerm, $limitEnd);

	$searchPerformed = true;

}

if($searchPerformed)
{
	$smarty = getAdminSmarty();

	if($allowMenu)
		$smarty->assign('RESULT_MENU', $resultsMenu);
	if($allowImage)
		$smarty->assign('RESULT_IMAGE', $resultsImage);
	if($allowFile)
		$smarty->assign('RESULT_FILE', $resultsFile);
	if($allowUser)
		$smarty->assign('RESULT_USER', $resultsUser);

	$smarty->display('SearchResults.tpl');
}
else
{
	displayError( getTranslation('msg_empty_term') );
	echo '
		<form action="'.createAdminLink('search').'" method="post">
		<input type="text" value="'.$searchTerm.'" name="query" /> <button type="submit">'.getTranslation('search').'</button>
		</form>
	';
}

admin_footer();

?>
