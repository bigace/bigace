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
 * Displays a list of all installed modules.
 * You can edit and de-/activate modules.
 * You can also create new modules on the fly.
 */

check_admin_login();
admin_header();

define('MODULE_ID', 'modulID');
define('MODULE_MODE', 'modeModul');
define('MODULE_CHANGE_STATE', 'state');
define('MODULE_EDIT_MODULE', 'edit');
define('MODE_SAVE', 'save');
define('MODE_CREATE', 'create');

import('classes.modul.ModulService');
import('classes.modul.Modul');
import('classes.util.IOHelper');

$SERVICE = new ModulService();
$mode = extractVar(MODULE_MODE, null);
$displayList = true;
$moduleID = extractVar(MODULE_ID, null);

switch($mode)
{
	case MODE_CREATE:
		if(isset($_POST['name']) && check_csrf_token())
		{
			$name = str_replace(" ", "_", $_POST['name']);
			$name = preg_replace("/(_)+/", "_", preg_replace("/[^a-zA-Z0-9_-\\s]/", "_", $name));
			if(!$SERVICE->createModul($name)) {
				displayError( getTranslation('create_failed') );
			}
		}
		break;
	case MODE_SAVE:
		if(!is_null($moduleID) && isset($_POST['content']) && check_csrf_token())
		{
			$module = new Modul($moduleID);
			$content = $_POST['content'];

            if (IOHelper::write_file($module->getFullURL(), $content)) {
				displayMessage( getTranslation('saved_ok') );
            } else {
            	displayError( getTranslation('saved_failed') );
			}
		}
		// no break, we stay in edit mode
	case MODULE_EDIT_MODULE:
		if(!is_null($moduleID))
		{
			echo createStyledBackLink( createAdminLink($MENU->getID()) );
			module_editor($moduleID);
			$displayList = false;
		}
		break;
	case MODULE_CHANGE_STATE:
		if(!is_null($moduleID) && check_csrf_token())
		{
			// try to save the de-/activated modul settings
			$mm = new Modul($moduleID);
			if($mm->isActivated()) {
				$SERVICE->deactivateModul($mm->getID(), _CID_);
			} else {
				$SERVICE->activateModul($mm->getID(), _CID_);
			}
		}
		break;
}


if($displayList)
{
	$allowCreation = true;
	// check if we are able to create new modules
	if (!is_writeable($GLOBALS['_BIGACE']['DIR']['modul'])) {
		displayError( getTranslation('modul_dir_not_writeable') . $GLOBALS['_BIGACE']['DIR']['modul']);
		$allowCreation = false;
	}

	$smarty = getAdminSmarty();
	$ENUM = $SERVICE->getModulEnumeration();
	$allModules = array();
	while($ENUM->hasNext())
	{
		$temp = $ENUM->next();
		if(_ULC_ != ADMIN_LANGUAGE)
		$temp->loadTranslation(ADMIN_LANGUAGE);

		$allModules[] = $temp;
	}
	$smarty->assign('MENU', $MENU);
	$smarty->assign('MODULES', $allModules);
	$smarty->assign('ALLOW_CREATE', $allowCreation);

	$smarty->display('ModuleListing.tpl');
}

function module_editor($moduleID)
{
	$module = new Modul($moduleID);
	$smarty = getAdminSmarty();
	$smarty->assign('MARKITUP_DIR', _BIGACE_DIR_ADDON_WEB.'markitup/');
	$smarty->assign('MODULE_CONTENT', htmlspecialchars(file_get_contents($module->getFullURL())));
	$smarty->assign('CANCEL_URL', createAdminLink($GLOBALS['MENU']->getID()));
	$smarty->assign('MENU', $GLOBALS['MENU']);
	$smarty->assign('module', $module);
	$smarty->display('ModuleEditor.tpl');
}

admin_footer();

