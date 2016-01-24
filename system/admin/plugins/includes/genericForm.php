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
 * For further information visit {@link http://www.kevinpapst.de www.kevinpapst.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 */

import('classes.util.GenericForm');

/* 

Required Form 
===============

$FORM = new GenericForm();

Required translation keys
==========================
 - generic_entry
 - generic_name
 - generic_value

#########################################################################################

If you need no special translations, you can use

loadLanguageFile('genericForm');

TODO 
=================
- Allow to change section?!
- Make column names for name/value dynamic
- add logging (see code)
- use GENERIC_PARAM_NAME/GENERIC_PARAM_VALUE/GENERIC_PARAM_SECTION

Modes
=================
 updateSection 	= change the name of a category
 editSection 	= display all entries of a faq category
 createEntry	= adds a new faq entry
 updateEntry 	= change the name of a category
 removeEntry	= deletes a faq entry
 createSection	= add a new category
 deleteSection	= delete a category ONLY if it has no entries
*/

if(!isset($FORM)) {
	displayError('No FORM configuration could be found.');
	return;
}
else {
	if(!is_class_of($FORM, 'GenericForm')) {
		displayError('FORM configuration is not a GenericForm class subtype.');
		return;
	}

}

import('classes.fright.FrightService');

define('TABLE_SECTIONS', $FORM->get_table_sections());
define('TABLE_MAPPINGS', $FORM->get_table_mappings());
define('TABLE_ENTRIES', $FORM->get_table_entries());
define('PERM_EDIT', $FORM->get_permission_edit());
define('PERM_CREATE', $FORM->get_permission_create());
define('COLUMN_NAME', 'name');
define('COLUMN_VALUE', 'value');

$mode = extractVar('mode', '');
$data = extractVar('data', array());
$additional = array();

$frightService 	= new FrightService();
$canEdit 		= $frightService->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), PERM_EDIT);
$canCreate 		= $frightService->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), PERM_CREATE);

if($mode == 'createEntry' || $mode == 'createSection') {
	if(!$canCreate) {
		// TODO log error
		displayError('Uuups');
		$mode = "";
	}
}
else if($mode == "updateEntry" || $mode == "removeEntry" || $mode == "deleteSection") {
	if(!$canEdit) {
		// TODO log error
		displayError('Uuups');
		$mode = "";
	}
}

if ($mode == "updateEntry")
{
	if(isset($_POST['section']) && isset($_POST['entry']) && isset($_POST['name'])
		 && strlen(trim($_POST['name'])) > 0 && isset($_POST['value']) && strlen(trim($_POST['value'])) > 0 ) {
		$replacer = array(
			'SECTION' => stripslashes($_POST['section']), 
			'ID' => stripslashes($_POST['entry']),
			'NAME' => stripslashes($_POST['name']),
			'VALUE' => stripslashes($_POST['value']),
			'TIMESTAMP' => time(),
		);
		$sqlString = "UPDATE {DB_PREFIX}".TABLE_ENTRIES." SET ".COLUMN_NAME." = {NAME}, ".COLUMN_VALUE." = {VALUE}, 
						timestamp = {TIMESTAMP} WHERE cid = {CID} AND id = {ID}"; 
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $replacer, true);
		$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
		if($res->isError()) {
			displayError(getTranslation("error_update_entry"));
		}
	}
	else {
		displayError(getTranslation("missing_values"));	
	}
} 
else if ($mode == "removeEntry")
{
	if(isset($_POST['section']) && isset($_POST['entry'])) {
		$replacer = array(
			'SECTION' => stripslashes($_POST['section']), 
			'ENTRY' => stripslashes($_POST['entry']),
		);
		$sqlString = "DELETE FROM {DB_PREFIX}".TABLE_ENTRIES.", {DB_PREFIX}".TABLE_MAPPINGS." 
						USING {DB_PREFIX}".TABLE_ENTRIES." RIGHT JOIN {DB_PREFIX}".TABLE_MAPPINGS." 
						ON {DB_PREFIX}".TABLE_ENTRIES.".id = {DB_PREFIX}".TABLE_MAPPINGS.".entry_id
						AND {DB_PREFIX}".TABLE_ENTRIES.".cid = {DB_PREFIX}".TABLE_MAPPINGS.".cid
						WHERE {DB_PREFIX}".TABLE_MAPPINGS.".cid = {CID} 
						AND {DB_PREFIX}".TABLE_MAPPINGS.".section_id = {SECTION} 
						AND {DB_PREFIX}".TABLE_MAPPINGS.".entry_id = {ENTRY}";
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $replacer, true);
		$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
		if($res->isError()) {
			displayError(getTranslation("error_delete_entry"));
		}
	}
	else {
		displayError(getTranslation("missing_values"));	
	}
} 
else if ($mode == "createEntry")
{
	$additional['name'] = isset($_POST['name']) ? $_POST['name'] : "";
	$additional['value'] = isset($_POST['value']) ? $_POST['value'] : "";

	if(isset($_POST['name']) && strlen(trim($_POST['name'])) > 0 && 
		isset($_POST['value']) && strlen(trim($_POST['value'])) > 0 && isset($_POST['section'])) {
			$sqlString = "INSERT INTO {DB_PREFIX}".TABLE_ENTRIES." (cid, ".COLUMN_NAME.", ".COLUMN_VALUE.", timestamp) 
							VALUES ({CID}, {NAME}, {VALUE}, {TIMESTAMP})";
			$replacer = array(
				'NAME' => stripslashes($_POST['name']),
				'VALUE'	=> stripslashes($_POST['value']),
				'TIMESTAMP' => time(),
			);
			$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $replacer, true);
			$id = $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sqlString);
			if($id === false) {
				displayError('(1) ' . getTranslation("error_create_entry"));
			}
			else {
				$replacer = array(
					'SECTION' 	=> stripslashes($_POST['section']),
					'ENTRY' 	=> stripslashes($id)
				);
				$sqlString = "INSERT INTO {DB_PREFIX}".TABLE_MAPPINGS." (cid, section_id, entry_id) 
								VALUES ({CID}, {SECTION}, {ENTRY})";
				$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $replacer, true);
				$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
				if($res->isError()) {
					displayError('(2) ' . getTranslation("error_create_entry"));
				}
				else {
					$mode = "editSection";
					$additional['name'] = "";
					$additional['value'] = "";
				}
			}
	}
	else {
		displayError(getTranslation("missing_values"));	
	}
} 
else if ($mode == "createSection")
{
	if(isset($_POST['section']) && strlen(trim($_POST['section'])) > 0) {
	    $sqlString = "INSERT INTO {DB_PREFIX}".TABLE_SECTIONS." (cid, type, name) VALUES ({CID}, {TYPE}, {NAME})";
	    $replacer = array('NAME' => stripslashes($_POST['section']), 'TYPE' => $FORM->getIdentifier());
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $replacer, true);
	    $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	    if($res->isError())
	    	displayError(getTranslation("error_create_section"));
	}
	else {
		displayError(getTranslation("missing_values"));	
	}
} 
else if ($mode == "deleteSection")
{
	if(isset($_POST['section']) && strlen(trim($_POST['section'])) > 0) {
		$replacer = array(
					'SECTION' => stripslashes($_POST['section']), 
		);
		
		if($FORM->count_generic_entries($_POST['section']) !== false) {
			$sqlString = "DELETE FROM {DB_PREFIX}".TABLE_ENTRIES.", {DB_PREFIX}".TABLE_MAPPINGS." 
							USING {DB_PREFIX}".TABLE_ENTRIES." RIGHT JOIN {DB_PREFIX}".TABLE_MAPPINGS." 
							ON {DB_PREFIX}".TABLE_ENTRIES.".id = {DB_PREFIX}".TABLE_MAPPINGS.".entry_id
							AND {DB_PREFIX}".TABLE_ENTRIES.".cid = {DB_PREFIX}".TABLE_MAPPINGS.".cid
							WHERE {DB_PREFIX}".TABLE_MAPPINGS.".cid = {CID} AND {DB_PREFIX}".TABLE_MAPPINGS.".section_id = {SECTION}";
			$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $replacer, true);
			$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
			if($res->isError()) {
				displayError('(1) ' . getTranslation("error_delete_section"));
			}
			else {
				$sqlString = "DELETE FROM {DB_PREFIX}".TABLE_SECTIONS." WHERE cid = {CID} AND id = {SECTION}";
				$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $replacer, true);
				$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
				if($res->isError()) displayError('(2) ' . getTranslation("error_delete_section"));
			}
		}
		else {
			displayError('(0) ' . getTranslation("error_delete_section"));
		}
	}
	else {
		displayError(getTranslation("missing_values"));	
	}
} 

$smarty = getAdminSmarty();
$smarty->assign('ACTION_URL', createAdminLink($MENU->getID()));
$smarty->assign('MODE', $mode);
$smarty->assign('PERM_EDIT', $canEdit);
$smarty->assign('PERM_CREATE', $canCreate);
$smarty->assign('INPUT_SECTION', $FORM->style_input_section());
$smarty->assign('INPUT_ENTRY', $FORM->style_input_entry());
$smarty->assign('FORM', $FORM);

if(isset($_POST['section']) && ($mode == 'updateEntry' || $mode == 'editSection' || $mode == 'createEntry' || $mode == 'removeEntry')) {
	$section_ID = $_POST['section'];
	$smarty->assign('SECTION', $FORM->get_generic_section($section_ID));
	$smarty->assign('ENTRIES', $FORM->get_generic_entries($section_ID));
}

$smarty->assign("SECTIONS", $FORM->get_generic_sections());
	
foreach($additional AS $key => $value)
	$smarty->assign($key, $value);

$smarty->display('GenericListing.tpl');

unset($smarty);
unset($mode);
unset($data);

admin_footer();
