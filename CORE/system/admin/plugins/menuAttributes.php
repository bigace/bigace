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
 */

/**
 * Plugin used to edit one Website page.
 */


check_admin_login();
admin_header();

import('classes.menu.Menu');
import('classes.administration.MenuAdminMask');
import('classes.menu.MenuAdminService');
import('classes.util.links.ItemInfoLink');

if(!isset($_GET['mode']) && !isset($_POST['mode']))
    $mode = _MODE_EDIT_ITEM;

$_ITEMTYPE     = _BIGACE_ITEM_MENU;
$_ADMIN        = new MenuAdminService();

include_once(_ADMIN_INCLUDE_DIRECTORY.'item_main.php');

propagateAdminMode();

admin_footer();

// ----------------------------------------------------------
// NEEDED FOR item_main.php
// ----------------------------------------------------------

function createAdminMask($itemtype) {
    $mask = new MenuAdminMask();
    $mask->setLargeMode(false);
    return $mask;
}

function createEditDataMaskForm($data, $item)
{
	$mask = createAdminMask(_BIGACE_ITEM_MENU);
	if($mask->checkWriteRight($item->getID())) 
	{
        $mask->getHeader($item);
		$mask->getToolbar($item);
	    showMenuAttributes( $mask, new Menu($item->getID(),ITEM_LOAD_FULL,$item->getLanguageID()) );
	}
}


function showMenuAttributes($mask, $item)
{
    // the modul select box
    import('classes.util.formular.ModulSelect');
    $modSelect = new ModulSelect();
    $modSelect->setModulLanguage(ADMIN_LANGUAGE);
    $modSelect->setPreSelectedID($item->getModulID());
    $modSelect->setName('data['._BIGACE_COLUMN_MODUL_ID.']');
    $modSelect->setShowPreselectedIfDeactivated(true);
    
    $prj = new ItemProjectService(_BIGACE_ITEM_MENU);
    
	$allMeta = Hooks::apply_filters('edit_item_meta', array(), $item);
    
    $layout = $mask->createLayoutSelectBox(_BIGACE_COLUMN_LAYOUT_ID, $item->getLayoutName());

    $tempLanguage = new Language($item->getLanguageID());

    $hiddenOrShown = $mask->getHiddenOrShown($item->isHidden());
    /*
    $hiddenOrShown = createRadioButton('num_3', FLAG_HIDDEN, $item->isHidden(), 'hiddenMenuOn') .
    ' <label for="hiddenMenuOn" style="cursor:pointer">'.getTranslation('hidden_menu') . '</label> &nbsp;&nbsp; ' .
    createRadioButton('num_3', FLAG_NORMAL, !$item->isHidden(), 'hiddenMenuOff') .
    ' <label for="hiddenMenuOff" style="cursor:pointer">' . getTranslation('display_menu') . '</label>';
    */
        
	$displayInfo = ' <img onmouseover="tooltip(\''.getTranslation('hidden_description').'\')" onMouseOut="nd();" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'info.png" border="0">';

	$iili = new ItemInfoLink();
	$iili->setInfoItemtype(_BIGACE_ITEM_MENU);
	$iili->setInfoItemLanguage($item->getLanguageID());
	$iili->setInfoItemID($item->getID());

	// assign values to template
	$smarty = getAdminSmarty();
	$smarty->assign('MODUL_SELECT', $modSelect->getHtml());
	$smarty->assign('LAYOUT_SELECT', $layout);
	$smarty->assign('WORKFLOW_SELECT', $mask->createWorkflowSelectBox('data[workflow]', $item->getWorkflowName()));
	$smarty->assign('tempLanguage', $tempLanguage);
	$smarty->assign('item', $item);
	$smarty->assign('hiddenOrShown', $hiddenOrShown);
	$smarty->assign('supportUniqueName', (BIGACE_URL_REWRITE == 'true'));
	
	$smarty->assign('FORM_ACTION', createAdminLink($GLOBALS['MENU']->getID()));
	$smarty->assign('FORM_MODE', _MODE_SAVE_ITEM);
	$smarty->assign('METADATA_URL', LinkHelper::getUrlFromCMSLink($iili));
	$smarty->assign('META_VALUES', $allMeta);
	
	$smarty->display('MenuAttributes.tpl');
	unset($smarty);
}