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
 * For further information visit {@link http://www.bigace.de www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst
 * @package bigace.administration
 * @subpackage menu
 */

/**
 * Script for News management.
 */

check_admin_login();
admin_header();

import('classes.fright.FrightService');
import('classes.util.formular.CategorySelect');
import('classes.category.ItemCategoryEnumeration');
import('classes.category.CategoryAdminService');
import('classes.news.News');
import('classes.image.Image');
import('classes.news.NewsAdminService');

define('NEWS_DATE_PHP', getTranslation('news_dateformat_php'));
define('NEWS_DATE_SMARTY', getTranslation('news_dateformat_smarty'));
define('NEWS_DATE_JS', getTranslation('news_dateformat_js'));
define('NEWS_TIME_FORMAT', getTranslation('news_timeformat_js'));

$frightService = new FrightService();
$canEdit 		= $frightService->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'news.edit');
$canCreate 		= $frightService->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'news.create');
$canDelete 		= $frightService->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'news.delete');
$canCategories 	= $frightService->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'news.categories');
$canConfig		= $frightService->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'admin_configurations');

$mode = extractVar('mode', 'listing');

// -----------------------------------------------------------------
// CHECK FOR ALL CONFIGURATIONS - IF ONE IS MISSING SHOW NOTHING
// BUT AN ERROR MESSGE INCLUDING LINK TO THE CONFIGURATIO PANEL

$rootID 		= ConfigurationReader::getConfigurationValue("news", "root.id");
$categoryID 	= ConfigurationReader::getConfigurationValue("news", "category.id");
$tplNews 		= ConfigurationReader::getConfigurationValue("news", "template.news");
$newsLang 		= ConfigurationReader::getConfigurationValue("news", "default.language");


$missing = array();

if($rootID == null) 		$missing[] = 'root.id';
if($categoryID == null) 	$missing[] = 'category.id';
if($tplNews == null) 		$missing[] = 'template.news';
if($newsLang == null) 		$missing[] = 'default.language';

// -----------------------------------------------------------------
// Currently need no check, cause they have pre-configured default values:
// archive.min.age.month, archive.min.age.days
//$archiveID 		= ConfigurationReader::getConfigurationValue("news", "archive.id");
//$tplArchMonth 	= ConfigurationReader::getConfigurationValue("news", "template.archive.month");
//$tplArchYear 	= ConfigurationReader::getConfigurationValue("news", "template.archive.year");
//if($archiveID == null) 		$missing[] = 'archive.id';
//if($tplArchMonth == null) 	$missing[] = 'template.archive.month';
//if($tplArchYear == null) 	$missing[] = 'template.archive.year';
// -----------------------------------------------------------------


// ----------------------- CHECK PERMISSIONS -----------------------
$permissionExists = false;
switch($mode) {
	case 'edit':
	case 'update':
		$permissionExists = $canEdit;
		break;
	case 'delete':
	case 'remove':
		$permissionExists = $canDelete;
		break;
	case 'create':
	case 'save':
		$permissionExists = $canCreate;
		break;
	case 'categories':
		$permissionExists = $canCategories;
		break;
	case 'listing':
		$permissionExists = true;
	break;
		default:
		$permissionExists = false;
}
// -----------------------------------------------------------------


if(!$permissionExists)
{
	displayError( getTranslation('error_permission') );
}
else if(count($missing) > 0)
{
	// TODO News + News Archiv anlegen, Kategorie erstellen und Configs speichern!
	// eventuell eigenen Screen mit Templateauswahl dafür anbieten?!

	// WRONG CONFIGURED
	$msg = getTranslation('error_unconfigured');
	foreach($missing AS $mm)
		$msg .= '<br> - ' . $mm;
	$msg .= '<br><a href="'.createAdminLink('configurations', array(), 'index.html#news', 'content').'">'.getTranslation('news_header_config').'</a>';
	displayError( $msg );
}
else
{
	newsHeader();

	if ($mode == "edit")
	{
		// open formular to edit existing entry

		if(!isset($_POST['newsID']) || !isset($_POST['newsLangID'])) {
			displayError( getTranslation('error_id') );
		}
		else {
			// TODO vorschau des ausgesuchten bildes mit thumbnails vorschau
			// TODO configs fuer thumbnail ja/nein und thumbnail hoehe breite
			$vals = prepareNewsEdit('update', $_POST['newsID'], $_POST['newsLangID']);
			newsEdit($vals);
		}
	}
	else if ($mode == "create")
	{
		// open formular to create new entry

		$vals = prepareEmptyNewsEdit('save', $newsLang);
		newsEdit($vals);
	}
	else if ($mode == "delete")
	{
		// open formular to delete existing entry

		if(!isset($_POST['newsID']) || !isset($_POST['newsLangID'])) {
			displayError( getTranslation('error_id') );
		}
		else {
    		$smarty = getAdminSmarty();
			$smarty->assign('NEWS_ID', $_POST['newsID']);
			$smarty->assign('NEWS_LANGUAGE', $_POST['newsLangID']);
			$smarty->assign('DATE_FORMAT', NEWS_DATE_SMARTY);
			$smarty->display('NewsDelete.tpl');
		}
	}
	else if ($mode == "update")
	{
		// save/update existing entry

		if(!isset($_POST['newsID'])) {
			displayError( getTranslation('error_id') );
		}
		else if(!isset($_POST['newsLangID'])) {
			displayError( getTranslation('error_language') );
		}
		else if(!isset($_POST['title'])) {
			displayError( getTranslation('error_title') );
		}
		else {
			$newCats = (isset($_POST['categories']) ? $_POST['categories'] : array());
			$newCats[] = $categoryID; // always assign the news root id
			// create requested categories too
			if($canCategories && isset($_POST['newCategories']) && trim($_POST['newCategories']) != "") {
				$newCats = array_merge($newCats, createNewCategories($_POST['newCategories']));
			}

			$nas = new NewsAdminService();
			$rr = $nas->updateNews(	$_POST['newsID'],
									$_POST['newsLangID'],
									stripslashes($_POST['title']),
									stripslashes($_POST['teaser']),
									'',
									stripslashes($_POST['content']),
									$newCats,
									strtotime($_POST['newsDate']),
									$_POST['imgID'],
									(isset($_POST['publish']) && $_POST['publish'] == '1') );
			if($rr === FALSE) {
				displayError( getTranslation('error_updating') );
			}
			else {
				$news = new News($_POST['newsID'], $_POST['newsLangID']);
				$link = LinkHelper::getCMSLinkFromItem($news);
				displayMessage( getTranslation('news_saved') . '<br><a href="'.LinkHelper::getUrlFromCMSLink($link).'">'.$news->getTitle().'</a>' );
			}
			//$vals = prepareNewsEdit('update', $_POST['newsID'], $_POST['newsLangID']);
			//newsEdit($vals);
			newsListing();
		}
	}
	else if ($mode == "save")
	{
		// create/save new entry

		if(!isset($_POST['newsLangID'])) {
			displayError( getTranslation('error_language') );
		}
		else if(!isset($_POST['title'])) {
			displayError( getTranslation('error_title') );
		}
		else {
			$newCats = (isset($_POST['categories']) ? $_POST['categories'] : array());
			$newCats[] = $categoryID; // always assign the news root id
			// create requested categories too
			if($canCategories && isset($_POST['newCategories']) && trim($_POST['newCategories']) != "") {
				$newCats = array_merge($newCats, createNewCategories($_POST['newCategories']));
			}
			$imgID = ((isset($_POST['imgID']) && strlen(trim($_POST['imgID'])) > 0) ? $_POST['imgID'] : null);

			$nas = new NewsAdminService();
			$id = $nas->createNews(	$_POST['newsLangID'],
									stripslashes($_POST['title']),
									stripslashes($_POST['teaser']),
									'',
									stripslashes($_POST['content']),
									$newCats,
									strtotime($_POST['newsDate']),
									$imgID,
									(isset($_POST['publish']) && $_POST['publish'] == '1') );
			if($id === FALSE) {
				displayError( getTranslation('error_creating') );
			}
			else {
				$news = new News($id, $_POST['newsLangID']);
				$link = LinkHelper::getCMSLinkFromItem($news);
				displayMessage( getTranslation('news_created') . '<br><a href="'.LinkHelper::getUrlFromCMSLink($link).'">'.$news->getTitle().'</a>' );
			}
			//$vals = prepareNewsEdit('update', $id, $_POST['newsLangID']);
			//newsEdit($vals);
			newsListing();
		}
	}
	else if ($mode == "remove")
	{
		// delete existing entry

		if(!isset($_POST['newsID'])) {
			displayError( getTranslation('error_id') );
		}
		else if(!isset($_POST['newsLangID'])) {
			displayError( getTranslation('error_language') );
		}
		else {
			$nas = new NewsAdminService();

			if( $nas->deleteNews($_POST['newsID'], $_POST['newsLangID']) ) {
				displayMessage( getTranslation('news_deleted') );
			}
			else {
				displayMessage( getTranslation('error_deleting') );
			}
			newsListing();
		}
	}
	else if ($mode == "categories")
	{
		displayError('Not implemented yet!');
		newsListing();
	}
	else if ($mode == "listing")
	{
		// show existing news entrys

		newsListing();
	}
	else
	{
	    displayError( getTranslation('error_mode') );
	}

	unset ( $mode );
	unset ( $frightService );
}

// ---------------------------------------------------------
//     FUNCTIONS FOLLOW
// ---------------------------------------------------------

/**
 * Creates new Categories from the given String.
 * The String may contain multiple Category names, divided by a comma.
 */
function createNewCategories($categories)
{
	$catIDs = array();
	$cats = explode(",", $categories);
	if(count($cats) > 0) {
		$cas = new CategoryAdminService();
		foreach($cats AS $cc) {
			$data = array( 'name' 			=> trim($cc),
						   'description' 	=> getTranslation('news_category_default_description'),
						   'parentid'		=> $GLOBALS['categoryID']
			);
			$temp = $cas->createCategory($data);
			if($temp === false) {
				// TODO: loggen
			} else {
				$catIDs[] = $temp;
			}
		}
	}
	return $catIDs;
}

function prepareEmptyNewsEdit($mode, $language)
{
	return array(
		'hiddenValues'	=> array('mode' => $mode, 'newsLangID' => $language),
		'imageURL'		=> '',
		'imageID'		=> '',
		'imageName'		=> '',
		'newsDate'		=> date(NEWS_DATE_PHP, time()),
		'dateFormat'	=> NEWS_DATE_JS,
		'timeFormat'	=> NEWS_TIME_FORMAT,
		'content'		=> '',
		'teaser'		=> '',
		'title'			=> '',
		'published'		=> false,
		'mode'			=> $mode,
		'categories'	=> array()
	);
}

function prepareNewsEdit($mode, $itemID, $itemLangID)
{
	$news = new News($itemID,$itemLangID);

	$categories = array();

	$icenum = new ItemCategoryEnumeration(_BIGACE_ITEM_MENU, $itemID);
	while($icenum->hasNext()) {
		$temp = $icenum->next();
		$categories[] = $temp->getID();
	}

	$vals = array(
		'hiddenValues'	=> array('mode' => $mode, 'newsID' => $itemID, 'newsLangID' => $itemLangID),
		'imageURL'		=> '',
		'imageID'		=> '',
		'imageName'		=> '',
		'newsDate'		=> date(NEWS_DATE_PHP, $news->getDate()),
		'dateFormat'	=> NEWS_DATE_JS,
		'timeFormat'	=> NEWS_TIME_FORMAT,
		'content'		=> $news->getContent(),
		'teaser'		=> $news->getTeaser(),
		'title'			=> $news->getTitle(),
		'published'		=> (!$news->isHidden()),
		'mode'			=> $mode,
		'categories'	=> $categories
	);

	if(!is_null($news->getImageID()))
	{
	    $image = new Image($news->getImageID());
	    $link = LinkHelper::getCMSLinkFromItem($image);

        $vals['imageURL']	= LinkHelper::getUrlFromCMSLink($link);
        $vals['imageID']    = $image->getID();
        $vals['imageName']  = $image->getName();
    }

    return $vals;
}

function newsEdit($newsEditArray) {

	require_once(_BIGACE_DIR_EDITOR.'editor_properties.php');
	$fallback = ConfigurationReader::getConfigurationValue("news", "root.id"); // null???
    $fallbackLang = ConfigurationReader::getConfigurationValue("news", "default.language"); // null???
	$passID = ($newsEditArray['mode'] == 'update' ? $newsEditArray['hiddenValues']['newsID'] : $fallback);
	$passLang = ($newsEditArray['mode'] == 'update' ? $newsEditArray['hiddenValues']['newsLangID'] : $fallbackLang);
	$imageURL = get_image_dialog_settings($passID,$passLang);

	$editor = '';
	if (file_exists(_BIGACE_DIR_ADDON.'FCKeditor/fckeditor.php'))
	{
		$extend = bigace_session_name() . "=" . bigace_session_id();

		if(!is_null($passID)) {
			$extend .= '&parent=' . $passID;
		}
        if(!is_null($passLang)) {
            $extend .= '&language=' . $passLang;
        }


		require_once(_BIGACE_DIR_ADDON.'FCKeditor/fckeditor.php');
        $oFCKeditor = new FCKeditor( 'content' );
        $oFCKeditor->ToolbarSet         = 'News';
        $oFCKeditor->Width              = '100%';
        $oFCKeditor->Height             = '300px';
		$oFCKeditor->BasePath           = _BIGACE_DIR_ADDON_WEB . 'FCKeditor/';
        $oFCKeditor->Config             = array('CustomConfigurationsPath' => BIGACE_URL_PLUGINS.'news/fckeditor.php?'.$extend);
        $oFCKeditor->Value 				= $newsEditArray['content'];
        $editor = $oFCKeditor->CreateHtml();
	}

	$categories = new CategorySelect();
	$categories->setName('categories[]');
	$categories->setStartID($GLOBALS['categoryID']);
	$categories->setIsMultiple();
	$categories->setSize(4);

	foreach($newsEditArray['categories'] AS $cat) {
		$categories->setPreSelectedID($cat);
	}

	$smarty = getAdminSmarty();

	$smarty->assign('CONTENT_EDITOR', $editor);
	$smarty->assign('SAVE_URL', createAdminLink($GLOBALS['MENU']->getID()));
	/*
	import('classes.util.links.ImageChooserLink');
	$icl = new ImageChooserLink();
	$icl->setJSNameForURL("SetUrl");
	$icl->setJSNameForInfos("SetInfos");
	$smarty->assign('IMAGE_CHOOSER_URL', LinkHelper::getUrlFromCMSLink($icl));
	*/
	$smarty->assign('IMAGE_CHOOSER_URL', $imageURL['url']);

	$smarty->assign('CATEGORY_CHOOSER', $categories->getHtml());
	$smarty->assign('EDIT_CONFIG', $newsEditArray);
	$smarty->assign('LANGUAGE', ADMIN_LANGUAGE);

	$smarty->assign('PERM_CATEGORIES', $GLOBALS['canCategories']);
	$smarty->assign('PERM_CREATE', $GLOBALS['canCreate']);
	$smarty->assign('PERM_DELETE', $GLOBALS['canDelete']);
	$smarty->assign('PERM_EDIT', $GLOBALS['canEdit']);
	$smarty->assign('PERM_CONFIG', $GLOBALS['canConfig']);

	$smarty->display('NewsEdit.tpl');
}

function newsHeader()
{
	$smarty = getAdminSmarty();

	$smarty->assign('LISTING_URL', createAdminLink($GLOBALS['MENU']->getID()));
	$smarty->assign('CREATE_URL', createAdminLink($GLOBALS['MENU']->getID(), array('mode' => 'create')));
	$smarty->assign('CATEGORIES_URL', createAdminLink($GLOBALS['MENU']->getID(), array('mode' => 'categories')));
	$smarty->assign('MODE', $GLOBALS['mode']);
	$smarty->assign('CONFIG_URL', createAdminLink('configurations', array(), 'index.html#news', 'content'));

	$smarty->assign('PERM_CATEGORIES', $GLOBALS['canCategories']);
	$smarty->assign('PERM_CREATE', $GLOBALS['canCreate']);
	$smarty->assign('PERM_DELETE', $GLOBALS['canDelete']);
	$smarty->assign('PERM_EDIT', $GLOBALS['canEdit']);
	$smarty->assign('PERM_CONFIG', $GLOBALS['canConfig']);

	$smarty->display('NewsHeader.tpl');
}

function newsListing()
{
	$smarty = getAdminSmarty();

	$smarty->assign('CATEGORIES', '');
	$smarty->assign('LIMIT', 0);
	$smarty->assign('DATE_FORMAT', NEWS_DATE_SMARTY);

	$smarty->assign('PERM_CATEGORIES', $GLOBALS['canCategories']);
	$smarty->assign('PERM_CREATE', $GLOBALS['canCreate']);
	$smarty->assign('PERM_DELETE', $GLOBALS['canDelete']);
	$smarty->assign('PERM_EDIT', $GLOBALS['canEdit']);
	$smarty->assign('PERM_CONFIG', $GLOBALS['canConfig']);

	$smarty->display('NewsListing.tpl');
}

admin_footer();
