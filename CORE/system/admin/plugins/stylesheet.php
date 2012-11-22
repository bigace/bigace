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

check_admin_login();
admin_header();

import('classes.smarty.SmartyStylesheet');
import('classes.smarty.SmartyService');
import('classes.util.formular.StylesheetSelect');
require_once(_BIGACE_DIR_LIBS.'sanitize.inc.php');

$mode = isset($_GET['mode']) ? $_GET['mode'] : (isset($_POST['mode']) ? $_POST['mode'] : '');
$sid = isset($_GET['stylesheet']) ? $_GET['stylesheet'] : (isset($_POST['stylesheet']) ? urldecode($_POST['stylesheet']) : '');

$service = new SmartyService();

// ------------------------------------ DELETE STYLESHEET ------------------------------------
if($mode == 'delete')
{
    if(isset($_POST['stylesheet']) && check_csrf_token()) { 
        if($service->countStylesheetUsage(urldecode($_POST['stylesheet'])) > 0) {
            displayError('Cannot be deleted, Stylesheet in use.');
        } else {
            $service->deleteStylesheet(urldecode($_POST['stylesheet']));
        }
    }
    else {
        displayError( getTranslation('msg_missing_values') );
    }
    $mode = '';
}


// ------------------------------------ SAVE STYLESHEET ------------------------------------
if($mode == 'save')
{
    if (isset($_POST['stylesheet']) && isset($_POST['description']) && 
        isset($_POST['content']) && isset($_POST['editorcss']) && check_csrf_token()) 
    {
        $desc = sanitize_plain_text($_POST['description']);
        $name = urldecode($_POST['stylesheet']);
        
        $css = $_POST['editorcss']; //sanitize_plain_text($_POST['editorcss']);
        $content = $_POST['content'];
        
        if($service->updateStylesheet($name, $desc, $content, $css)) {
            displayMessage( getTranslation('msg_saved') );
        } else {
            displayError( getTranslation('msg_saving_failed') );
        }
        $sid = $name;
        $mode = 'edit';
    }
    else {
        displayError( getTranslation('msg_missing_values') );
        $mode = '';
    }
}

// ---------------------------------- CREATE STYLESHEET ----------------------------------
if($mode == 'new')
{
    if(isset($_POST['stylesheet']) && trim($_POST['stylesheet']) != '' && 
        isset($_POST['description']) && isset($_POST['editorcss']) && check_csrf_token()) 
    {
	    $desc = sanitize_plain_text($_POST['description']);
	    $name = sanitize_plain_text($_POST['stylesheet']);
        $css = sanitize_plain_text($_POST['editorcss']);
	    
        $sid = $service->createStylesheet($name, $desc,'',$css);
        $sid = $name;
        $mode = 'edit';
    }
    else {
        displayError( getTranslation('msg_missing_values') );
        $mode = '';
    }
}

// ---------------------------------- EDIT STYLESHEET ----------------------------------
if($mode == 'edit')
{
    if($sid != '' && check_csrf_token()) 
    {
        echo createStyledBackLink( createAdminLink($MENU->getID()) );

        $stylsheet = new SmartyStylesheet($sid);
        $editorStylesheet = $stylsheet->getEditorStylesheet();;

        $selector2 = new StylesheetSelect();
        $selector2->setPreselected($editorStylesheet->getName());
        $selector2->setName('editorcss');

        $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("StylesheetEditor.tpl.htm", false, true);
        $tpl->setVariable("MARKITUP_DIR", _BIGACE_DIR_ADDON_WEB.'markitup/');
        $tpl->setVariable("EDITOR_CHOOSER", $selector2->getHtml());
        $tpl->setVariable("STYLESHEET_ID", $stylsheet->getName());
        $tpl->setVariable("STYLESHEET_NAME", $stylsheet->getName());
        $tpl->setVariable('CSRF_TOKEN', get_csrf_token());
        $tpl->setVariable("STYLESHEET_DESCRIPTION", $stylsheet->getDescription());
        $tpl->setVariable("STYLESHEET_CONTENT", file_get_contents($stylsheet->getFullFilename()));
        $tpl->setVariable("SAVE_URL", createAdminLink($MENU->getID(), array('mode' => 'save', 'a=a#editor'))) ;
        $tpl->setVariable("CANCEL_URL", createAdminLink($MENU->getID())) ;
        $tpl->show();
    } 
    else {
        displayError( getTranslation('msg_missing_values') );
        $mode = '';
    }
}


if($mode == '')
{
    $selector2 = new StylesheetSelect();
    $selector2->setName('editorcss');

    $entrys = $service->getAllStylesheets();
    
    $all = array();
    
    foreach($entrys AS $temp)
    {
        $temp = array(
            'name'          => $temp->getName(),
            'filename'      => urlencode($temp->getName()),
            'description'   => $temp->getDescription(),
            'usage'         => $service->countStylesheetUsage($temp->getName())
        );
        $all[] = $temp;
    }

    $smarty = getAdminSmarty();
    $smarty->assign('stylesheets', $all);
    $smarty->assign('MENU', $MENU);
    $smarty->assign('editorcss', $selector2->getHtml());
    $smarty->display('Stylesheets.tpl');
}

admin_footer();
