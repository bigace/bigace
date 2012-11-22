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
 * Create a new page with this script.
 */

check_admin_login();

// load translations, some of them are used in the header already
// do not move this line below the admin_header()
loadLanguageFile('itemMenuCreate', ADMIN_LANGUAGE);

import('classes.util.html.FormularHelper');
import('classes.item.ItemTreeWalker');
import('classes.item.ItemAdminService');
import('classes.layout.LayoutService');
import('classes.menu.Menu');
import('classes.menu.MenuService');
import('classes.menu.MenuAdminService');
import('classes.modul.ModulService');
import('classes.right.RightAdminService');
import('classes.language.Language');
import('classes.language.LanguageEnumeration');
import('classes.administration.MenuAdminMask');
import('classes.util.html.JavascriptHelper');
import('classes.util.LinkHelper');
import('classes.util.links.MenuChooserLink');
import('classes.right.RightService');
import('classes.util.formular.ModulSelect');
import('classes.seo.UniqueNameService');
import('classes.seo.UniqueNameAdminService');
import('classes.util.ApplicationLinks');

$mode = extractVar('mode', '1');
$data = extractVar('data', array());

$nextAdmin = 'menuTree';
if(isset($data['nextAdmin'])) {
    $nextAdmin = $data['nextAdmin'];
}

$MENU_SERVICE   = new MenuService();

$title = getTranslation('new_page');
$error = '';

if ($mode== 'createNewMenu')
{
	if (is_array($data) && count($data) > 2 && isset($data['langid']) && isset($data['name']) && isset($data['parentid']))
	{
		if (strlen(trim($data['name'])) > 0)
		{
			$item = $MENU_SERVICE->getClass($data['parentid']);
			if($item->exists())
			{
				$R_SERVICE = new RightService();
				$NEWPARENTRIGHT = $R_SERVICE->getMenuRight($GLOBALS['_BIGACE']['SESSION']->getUserID(), $data['parentid']);
				if($NEWPARENTRIGHT->canWrite()) {

		            $admin = new MenuAdminService();

		            if(!isset($data['unique_name']))
		                $data['unique_name'] = '';

		            // if not was set, calculate from name and build safe url
		            if( BIGACE_URL_REWRITE == 'true' && ( !isset($data['unique_name']) || strlen(trim($data['unique_name'])) == 0) ) {
						$data['unique_name'] = $admin->buildUniqueNameSafe($data['name'], '');
		            }
		            // otherwise, if url was given, we see if this is useable
		            else {
			            // check if unique name exists, if so: create a different one
			            // really pass an empty string as second parameter???
			        	$curUniqueName = bigace_unique_name_raw($data['unique_name']);
			           	if($curUniqueName != null) {
							$data['unique_name'] = $admin->buildUniqueNameSafe($data['unique_name'], '');
			           	}
		            }

		        	$new_mid = $admin->createMenu($data);
		        	if($new_mid === false) {
		        	    $error = "Failed creating menu."; // TODO translate
					}
					else {
						header('Location: '.createAdminLink($GLOBALS['nextAdmin'],
																array(
																	'data[id]'     => $new_mid,
																	'data[langid]' => $data['langid'],
																	'mode'         => _MODE_EDIT_ITEM
																)
											)
						);
						exit();
					}
					// no answer box, instead redirect to the menu itself
					/*

		            include_once(_ADMIN_INCLUDE_DIRECTORY.'answer_box.php');

		            $hidden = array(
		                            'data[id]'     => $new_mid,
		                            'data[langid]' => $data['langid'],
		                            'mode'         => _MODE_EDIT_ITEM
		            );

		            $msg = array(
		                            getTranslation('name')  => $data['name'],
		                            'ID'                    => $new_mid
		            );
		            displayAnswer(getTranslation('create_new_menu'), $msg, createAdminLink( $GLOBALS['nextAdmin'], array('preloadID' => $new_mid, 'preloadLang' => $data['langid']) ), $hidden, getTranslation('show'), 'item_1_new.png');
		            unset($msg);
		            unset($hidden);
					*/
		            unset($admin);
				}
				else {
					loadLanguageFile('access_rights');
				    $error = getTranslation('missing.write.rights');
		        	$title = getTranslation('new_page');
				}
			}
			else {
				$error = getTranslation('parent.does.not.exist');
		    	$title = getTranslation('new_page');
			}
		}
		else
		{
		    $title = getTranslation('insert_name');
		}
	}
}

// -----------------------------------------------------------------
// Display the form including errors, header and footer
admin_header();
if($error != '') {
	displayError($error);
}
showNewMenuForm( $title, $data );
admin_footer();
// -----------------------------------------------------------------


function createEditorLink($itemid, $mode = 'empty')
{
    return ApplicationLinks::getEditorTypeURL('fckeditor', $itemid, ADMIN_LANGUAGE, array('mode' => $mode));
}


/**
 * Creates a "Edit values for Page" Mask, filled with the given Page infos from DB
 * by default if not turned off by setting $filldata = true;
 */
function showNewMenuForm($title, $data)
{

	$data_name 	= isset($data['name']) 			? $data['name'] 		: '';
	$data_catch = isset($data['catchwords']) 	? $data['catchwords']	: '';
	$data_desc 	= isset($data['description']) 	? $data['description'] 	: '';
	$data_pid 	= isset($data['id']) 			? $data['id'] 			: (isset($data['parentid']) ? $data['parentid']	: -1);
	$data_wf 	= isset($data['workflow']) 		? $data['workflow'] 	: '';
	$data_lang	= isset($data['langid']) 		? $data['langid'] 		: $GLOBALS['_BIGACE']['SESSION']->getLanguageID();
	$data_modul = isset($data[_BIGACE_COLUMN_MODUL_ID]) ? $data[_BIGACE_COLUMN_MODUL_ID] : 'displayContent';
	$parLayout  = isset($data[_BIGACE_COLUMN_LAYOUT_ID]) ? $data[_BIGACE_COLUMN_LAYOUT_ID] : "";

    $adminMask = new MenuAdminMask();

    if($data_pid == _BIGACE_TOP_PARENT) {
        $data_pid = _BIGACE_TOP_LEVEL;
    }

    $R_SERVICE = new RightService();
	$USERTOPRIGHT = $R_SERVICE->getMenuRight($GLOBALS['_BIGACE']['SESSION']->getUserID(), $data_pid);

    $newType = "hidden";

    if($USERTOPRIGHT->canRead()) {
	    $item = $GLOBALS['MENU_SERVICE']->getClass($data_pid, ITEM_LOAD_FULL, ADMIN_LANGUAGE);
    	$parTempId = $item->getId();
    	$parName = $item->getName();
		if($parLayout == '')
	    	$parLayout = $item->getLayoutName();
    }
	else {
    	// user cannot read top level and therefor is not able to use menu js tree
    	// popup. let him edit the id manually
		if($data_pid == _BIGACE_TOP_LEVEL)
		    $newType = "text";

    	$parTempId = '';
    	$parName = '';
    }

	$allMeta = Hooks::apply_filters('create_item_meta', array(), _BIGACE_ITEM_MENU);

    // ##################### PARENT CHOOSER #####################
    $link = new MenuChooserLink();
    $link->setJavascriptCallback('setMenu');
    echo JavascriptHelper::createJSPopup('parentSelector', 'SelectParent', '400', '350', LinkHelper::getUrlFromCMSLink($link), array(), 'yes');
    unset($link);

    // ##################### Show hidden field and parent selector #####################
    $submenu = '<input '.($newType != 'hidden' ? 'style="width:50px;margin-right:10px"': '').' type="'.$newType.'" name="data[parentid]" value="'.$parTempId.'" id="parentid">' .
    			      '<input type="text" id="parentname" name="parentname" value="'.$parName.'" disabled="disabled">' .
                      '&nbsp;<input type="button" value="'.getTranslation('choose').'" onclick="parentSelector()">';

    // ##################### HIDDEN OR SHOWN #####################
	$hhh = (isset($data['num_3']) && $data['num_3'] == FLAG_HIDDEN) ? true : false;
	$hiddenOrShown = $adminMask->getHiddenOrShown($hhh);

    // ##################### Language Select #####################
    $languages = array();
    $langEnum = new LanguageEnumeration();
    for ($i=0;$i<$langEnum->count();$i++)
    {
        $tempLang = $langEnum->next();
        $languages[$tempLang->getName(ADMIN_LANGUAGE)] = $tempLang->getID();
        unset($tempLang);
    }
    unset($langEnum);

    // ##################### Modul Select #####################
    $modSelect = new ModulSelect();
    $modSelect->setModulLanguage(ADMIN_LANGUAGE);
    $modSelect->setPreSelectedID( $data_modul );
    $modSelect->setName('data['._BIGACE_COLUMN_MODUL_ID.']');
    $modSelect->setShowPreselectedIfDeactivated(false);

	$uniqueName = (isset($data['unique_name']) ? $data['unique_name'] : "");

    // ##################### Content Editor #####################
	$editor = '';
	if (file_exists(_BIGACE_DIR_ADDON.'FCKeditor/fckeditor.php')) {
		require_once(_BIGACE_DIR_ADDON.'FCKeditor/fckeditor.php');
        $oFCKeditor = new FCKeditor( 'data[content]' );
		$oFCKeditor->ToolbarSet = 'Bigace';
        $oFCKeditor->Width      = '100%';
        $oFCKeditor->Height     = '300px';
		$oFCKeditor->BasePath   = _BIGACE_DIR_ADDON_WEB . 'FCKeditor/';
        $oFCKeditor->Value 		= (isset($data['content']) ? $data['content'] : "");
        // FIXME: create own config: set null as parent id for link and image dialog!
	    $oFCKeditor->Config     = array('CustomConfigurationsPath' => createEditorLink($parTempId, 'customConfig'));
        $editor = $oFCKeditor->CreateHtml();
	}
    unset ($parTempId);

	// ------------------------------------------------------------
	$smarty = getAdminSmarty();
	$smarty->assign('LOCALE', ADMIN_LANGUAGE);
	$smarty->assign('FORM_ACTION', createAdminLink($GLOBALS['MENU']->getID()));
	$smarty->assign('FORM_MODE', "createNewMenu");
	$smarty->assign('NEXT_ADMIN', $GLOBALS['nextAdmin']);
	$smarty->assign('supportUniqueName', (BIGACE_URL_REWRITE == 'true'));
	$smarty->assign('META_VALUES', $allMeta);

	$smarty->assign('WORKFLOW_SELECT', $adminMask->createWorkflowSelectBox('data[workflow]', $data_wf));
	$smarty->assign('MODUL_SELECT', $modSelect->getHtml());
	$smarty->assign('LAYOUT_SELECT', $adminMask->createLayoutSelectBox(_BIGACE_COLUMN_LAYOUT_ID, $parLayout, false));

	$smarty->assign('NEW_NAME', $data_name);
	$smarty->assign('NEW_LANGUAGE', createSelectBox('langid', $languages, $data_lang));
	$smarty->assign('NEW_SUBMENU', $submenu);
	$smarty->assign('NEW_CATCHWORDS', $data_catch);
	$smarty->assign('NEW_DESCRIPTION', $data_desc);
	$smarty->assign('NEW_STATE', $hiddenOrShown);
	$smarty->assign('NEW_UNIQUE_NAME', $uniqueName);
	$smarty->assign('NEW_EDITOR', $editor);
	//$smarty->assign('NEW_POSITION', createTextInputType('num_4', '1', 50));
	$smarty->assign('title', $title);
	$smarty->display('MenuCreate.tpl');
}
