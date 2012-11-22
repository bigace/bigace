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
 * Script for Comment management.
 */

check_admin_login();
admin_header();

import('classes.comments.CommentAdminService');

$frightService = new FrightService();
$canEdit 		= $GLOBALS['FRIGHT_SERVICE']->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'comments.edit');
$canActivate	= $GLOBALS['FRIGHT_SERVICE']->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'comments.activate');
$canDelete 		= $GLOBALS['FRIGHT_SERVICE']->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'comments.delete');
$canConfig		= $GLOBALS['FRIGHT_SERVICE']->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'admin_configurations');

$mode = extractVar('mode', 'listing');


// ----------------------- CHECK PERMISSIONS -----------------------
$permissionExists = false;
switch($mode) {
	case 'edit':
	case 'update':
		$permissionExists = $canEdit;
		break;
	case 'activate':
		$permissionExists = $canActivate;
		break;
	case 'spam':
	case 'remove':
		$permissionExists = $canDelete;
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
else
{
	commentHeader($mode);
	
	if ($mode == "edit")
	{
		// open formular to edit existing entry
		
		if(!isset($_POST['commentID'])) {
			displayError( getTranslation('error_id') );
		} 
		else {
			commentEdit($_POST['commentID']);
		}
	}
	else if ($mode == "activate")
	{
		// activate existing entry
		
		if(!isset($_POST['commentID'])) {
			displayError( getTranslation('error_id') );
		} 
		else {
			$cas = new CommentAdminService();
			$rr = $cas->activate($_POST['commentID']);
			if($rr === FALSE) {
				displayError( getTranslation('error_activation') );
			}
			else {
				displayMessage( getTranslation('comment_activated') );
			}
			commentListPending();
		}
	} 
	else if ($mode == "update")
	{
		// save/update existing entry
		
		if(!isset($_POST['commentID'])) {
			displayError( getTranslation('error_id') );
		} 
		else {
			$cas = new CommentAdminService();
			$rr = $cas->update(	$_POST['commentID'],
								stripslashes($_POST['name']), 
								stripslashes($_POST['comment']), 
								stripslashes($_POST['email']), 
								stripslashes($_POST['homepage']));
			if($rr === FALSE) {
				displayError( getTranslation('error_updating') );
			}
			else {
				displayMessage( getTranslation('comment_saved') );
			}
			commentListPending();
		}
	} 
	else if ($mode == "remove" || $mode == "spam")
	{
		// delete existing entry
		if(!isset($_POST['commentID'])) {
			displayError( getTranslation('error_id') );
		} 
		else {
			$cas = new CommentAdminService();

			if( $mode == "remove" ){
				if ($cas->delete($_POST['commentID']) ) {
					displayMessage( getTranslation('comment_deleted') );
				} 
				else {
					displayMessage( getTranslation('error_deleting') );
				}
			} 
			else if( $mode == "spam" ){
				if ($cas->deleteSpam($_POST['commentID']) ) {
					displayMessage( getTranslation('spam_deleted') );
				} 
				else {
					displayMessage( getTranslation('error_deleting') );
				}
			} 
			commentListPending();
		}
	} 
	else if ($mode == "listing")
	{
		// show pending comments
		commentListPending();
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

function commentEdit($id) 
{

	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('comment_select');
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array('ID' => $id), true);
    $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    $comment = $res->next(); 
	
	$smarty = getAdminSmarty();
	$smarty->assign('SAVE_URL', createAdminLink($GLOBALS['MENU']->getID(), array('mode' => 'save')));
	$smarty->assign('COMMENT', $comment);
	$smarty->display('CommentsEdit.tpl');
}

function commentHeader($mode) 
{
	$smarty = getAdminSmarty();
	
	$smarty->assign('LISTING_URL', createAdminLink($GLOBALS['MENU']->getID()));
	$smarty->assign('SEARCH_URL', createAdminLink($GLOBALS['MENU']->getID(), array('mode' => 'search')));
	$smarty->assign('MODE', $mode);
	$smarty->assign('CONFIG_URL', createAdminLink('configurations', array(), 'index.html#comments', 'content'));
	
	$smarty->assign('PERM_ACTIVATE', $GLOBALS['canActivate']);
	$smarty->assign('PERM_DELETE', $GLOBALS['canDelete']);
	$smarty->assign('PERM_EDIT', $GLOBALS['canEdit']);
	$smarty->assign('PERM_CONFIG', $GLOBALS['canConfig']);
	
	$smarty->display('CommentsHeader.tpl');
}

function commentListPending() 
{
	$smarty = getAdminSmarty();
	
	$start = 0;
	$limit = 300;

	$values = array('START' => $start, 'END' => $limit);
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('comment_select_pending');
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
    $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	
    $comments = array();

	$smarty->assign('PERM_ACTIVATE', $GLOBALS['canActivate']);
    
    // if available show pending comments
    if ($res->count() == 0) 
    {
    	$smarty->assign('PERM_ACTIVATE', false);
	    // otherwise load existing comnments and display listing
    	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('comment_select_all');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
	    $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    for($i=0; $i < $res->count(); $i++)
    	$comments[] = $res->next();

    $smarty->assign('COMMENTS', $comments);
	$smarty->assign('PERM_DELETE', $GLOBALS['canDelete']);
	$smarty->assign('PERM_EDIT', $GLOBALS['canEdit']);
	$smarty->assign('PERM_CONFIG', $GLOBALS['canConfig']);
	
	$smarty->display('CommentsPendingList.tpl');
}

admin_footer();

?>