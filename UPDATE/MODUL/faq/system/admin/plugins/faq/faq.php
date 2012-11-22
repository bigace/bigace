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

/*
 updateSection 	= change the name of a category
 editSection 	= display all entries of a faq category
 createEntry	= adds a new faq entry
 updateEntry 	= change the name of a category
 removeEntry	= deletes a faq entry
 createSection	= add a new category
 deleteSection	= delete a category ONLY if it has no entries
*/

check_admin_login();
admin_header();

import('classes.util.FAQForm');
$FORM = new FAQForm();
include_once(_ADMIN_INCLUDE_DIRECTORY.'genericForm.php');

/*
import('classes.fright.FrightService');

$mode = extractVar('mode', '');
$data = extractVar('data', array());
$additional = array();

$frightService 	= new FrightService();
$canEdit 		= $frightService->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'faq.edit.entry');
$canCreate 		= $frightService->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'faq.add.entry');

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
	if(isset($_POST['section']) && isset($_POST['entry']) && isset($_POST['question'])
		 && strlen(trim($_POST['question'])) > 0 && isset($_POST['answer']) && strlen(trim($_POST['answer'])) > 0 ) {
		$replacer = array(
			'SECTION' => stripslashes($_POST['section']), 
			'ID' => stripslashes($_POST['entry']),
			'QUESTION' => stripslashes($_POST['question']),
			'ANSWER' => stripslashes($_POST['answer']),
			'TIMESTAMP' => time()
		);
		$sqlString = "UPDATE {DB_PREFIX}faq_entries SET question = {QUESTION}, answer = {ANSWER}, timestamp = {TIMESTAMP} 
						WHERE cid = {CID} AND id = {ID}"; 
		// TODO allow to change section?!
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
			'ENTRY' => stripslashes($_POST['entry'])
		);
		$sqlString = "DELETE FROM {DB_PREFIX}faq_entries, {DB_PREFIX}faq_mappings 
						USING {DB_PREFIX}faq_entries RIGHT JOIN {DB_PREFIX}faq_mappings 
						ON {DB_PREFIX}faq_entries.id = {DB_PREFIX}faq_mappings.entry_id
						AND {DB_PREFIX}faq_entries.cid = {DB_PREFIX}faq_mappings.cid
						WHERE {DB_PREFIX}faq_mappings.cid = {CID} 
						AND {DB_PREFIX}faq_mappings.section_id = {SECTION} 
						AND {DB_PREFIX}faq_mappings.entry_id = {ENTRY}";
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
	$additional['question'] = isset($_POST['question']) ? $_POST['question'] : "";
	$additional['answer'] = isset($_POST['answer']) ? $_POST['answer'] : "";

	if(isset($_POST['question']) && strlen(trim($_POST['question'])) > 0 && 
		isset($_POST['answer']) && strlen(trim($_POST['answer'])) > 0 && isset($_POST['section'])) {
			$sqlString = "INSERT INTO {DB_PREFIX}faq_entries (cid, question, answer, timestamp) 
							VALUES ({CID}, {QUESTION}, {ANSWER}, {TIMESTAMP})";
			$replacer = array(
				'QUESTION' 	=> stripslashes($_POST['question']),
				'ANSWER' 	=> stripslashes($_POST['answer']),
				'TIMESTAMP' => time()
			);
			$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $replacer, true);
			$id = $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sqlString);
			if($id === false) {
				displayError('(1) ' . getTranslation("error_create_entry"));
			}
			else {
				$replacer = array(
					'SECTION' 	=> stripslashes($_POST['section']),
					'ENTRY' 	=> stripslashes($id),
				);
				$sqlString = "INSERT INTO {DB_PREFIX}faq_mappings (cid, section_id, entry_id) 
								VALUES ({CID}, {SECTION}, {ENTRY})";
				$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $replacer, true);
				$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
				if($res->isError()) {
					displayError('(2) ' . getTranslation("error_create_entry"));
				}
				else {
					$mode = "editSection";
					$additional['question'] = "";
					$additional['answer'] = "";
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
	    $sqlString = "INSERT INTO {DB_PREFIX}faq_sections (cid, name) VALUES ({CID}, {NAME})";
	    $replacer = array('NAME' => stripslashes($_POST['section']));
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
		$replacer = array('SECTION' => stripslashes($_POST['section']));
		
		$sqlString = "SELECT count(mappings.entry_id) as amount FROM {DB_PREFIX}faq_mappings mappings WHERE cid = {CID} AND section_id = {SECTION}";
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $replacer, true);
		$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
		if(!$res->isError()) {
			$res = $res->next();
			if($res['amount'] == 0) {
				$sqlString = "DELETE FROM {DB_PREFIX}faq_entries, {DB_PREFIX}faq_mappings 
								USING {DB_PREFIX}faq_entries RIGHT JOIN {DB_PREFIX}faq_mappings 
								ON {DB_PREFIX}faq_entries.id = {DB_PREFIX}faq_mappings.entry_id
								AND {DB_PREFIX}faq_entries.cid = {DB_PREFIX}faq_mappings.cid
								WHERE {DB_PREFIX}faq_mappings.cid = {CID} AND {DB_PREFIX}faq_mappings.section_id = {SECTION}";
				$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $replacer, true);
				$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
				if($res->isError()) {
					displayError('(1) ' . getTranslation("error_delete_section"));
				}
				else {
					$sqlString = "DELETE FROM {DB_PREFIX}faq_sections WHERE cid = {CID} AND id = {SECTION}";
					$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $replacer, true);
					$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
					if($res->isError()) displayError('(2) ' . getTranslation("error_delete_section"));
				}
			} 
			else {
				displayError(getTranslation("delete_entries_exist"));	
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

if(isset($_POST['section']) && ($mode == 'updateEntry' || $mode == 'editSection' || $mode == 'createEntry' || $mode == 'removeEntry')) {
	$smarty->assign('SECTION', $_POST['section']);
}

foreach($additional AS $key => $value)
	$smarty->assign($key, $value);

$smarty->display('FAQ-Listing.tpl');
unset($smarty);



unset ( $mode );
unset ( $data );

admin_footer();

*/


?>