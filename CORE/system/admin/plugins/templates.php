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

import('classes.smarty.SmartyTemplate');
import('classes.smarty.SmartyService');
import('classes.util.formular.TemplateSelect');
require_once(_BIGACE_DIR_LIBS.'sanitize.inc.php');

$mode = isset($_GET['mode']) ? $_GET['mode'] : (isset($_POST['mode']) ? $_POST['mode'] : '');
$tid = isset($_GET['template']) ? $_GET['template'] : (isset($_POST['template']) ? urldecode($_POST['template']) : '');

$service = new SmartyService();

if($mode == 'delete')
{
    if($tid != "" && check_csrf_token()) 
    { 
        $tpl = new SmartyTemplate($tid);
        if($tpl->isSystem()) {
            displayError('System templates cannot be deleted.');
        } else if($service->countTemplateUsage($tpl->getName()) > 0) {
            displayError('Cannot be deleted, template in use.');
        } else {
            $service->deleteTemplate($tpl->getName());
        }
    }
    else {
        displayError( getTranslation('msg_missing_values') );
    }
    $mode = '';
}

if($mode == 'save')
{
    if(isset($_POST['templatename']) && isset($_POST['description']) && 
        isset($_POST['content']) && isset($_POST['inwork']) && check_csrf_token()) 
    {
        $desc = sanitize_plain_text($_POST['description']);
        //$name = sanitize_plain_text($_POST['templatename']);
        $name = urldecode($_POST['templatename']);
        
        $include = (isset($_POST['isinclude']) ? true : false);
        $inwork = (isset($_POST['inwork']) &&  $_POST['inwork'] == '1' ? true : false);
        if($service->updateTemplate($name, $desc, stripslashes($_POST['content']), $inwork, $include)) {
            displayMessage( getTranslation('msg_saved') );
        } else {
            displayError( getTranslation('msg_saving_failed') );
        }
        $tid = $name;
        $mode = 'edit';
    }
    else {
        displayError( getTranslation('msg_missing_values') );
        $mode = '';
    }
}

if($mode == 'new')
{
    if(isset($_POST['templatename']) && trim($_POST['templatename']) != '' && 
        isset($_POST['description']) && isset($_POST['isinclude']) && check_csrf_token()) 
    {
        $desc = sanitize_plain_text($_POST['description']);
        $name = sanitize_plain_text($_POST['templatename']);
    	
        $include = ($_POST['isinclude'] == '1' ? true : false);
        $tid = $service->createTemplate($name, $desc, '', $include);
        $tid = $name;
        $mode = 'edit';
    }
    else {
        displayError( getTranslation('msg_missing_values') );
        $mode = '';
    }
}

if($mode == 'copy')
{
    $mode = '';
	
    if(isset($_POST['templatename']) && trim($_POST['templatename']) != '' && 
        isset($_POST['description']) && isset($_POST['copyname']) && check_csrf_token()) 
    {
    	$newName = sanitize_plain_text($_POST['templatename']);
        
        $copyTemplate = new SmartyTemplate($_POST['copyname']);
        $newDesc = $copyTemplate->getDescription();
        
        if (strlen(trim($_POST['description'])) > 0) {
        	$newDesc = sanitize_plain_text($_POST['description']);
        }
        
        if($copyTemplate->getName() == $_POST['copyname']) {
            $include = $copyTemplate->isInclude();
            $inwork = $copyTemplate->isInWork();
            $content = $copyTemplate->getContent();
            $tid = $service->createTemplate($newName,$newDesc,$content,$include,$inwork);
            $tid = $newName;
            $mode = 'edit';
        }
    }
    else {
        displayError( getTranslation('msg_missing_values') );
    }
}

if($mode == 'edit')
{
    if($tid != '' && check_csrf_token()) 
    {
        echo createStyledBackLink( createAdminLink($MENU->getID()) );
        $template = new SmartyTemplate($tid);
        $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("TemplateEditor.tpl.htm", false, true);
        $tpl->setVariable("NAME", $template->getName());
        $tpl->setVariable("TEMPLATE_NAME", urlencode($template->getName()));
        $tpl->setVariable("TEMPLATE_DESCRIPTION", $template->getDescription());
        $tpl->setVariable("MARKITUP_DIR", _BIGACE_DIR_ADDON_WEB.'markitup/');
        
        if($template->isInWork()) {
            $tpl->setVariable("INWORK_FALSE_CHECKED", '');
            $tpl->setVariable("INWORK_TRUE_CHECKED", 'checked="checked"');
        }
        else {
            $tpl->setVariable("INWORK_FALSE_CHECKED", 'checked="checked"');
            $tpl->setVariable("INWORK_TRUE_CHECKED", '');
        }

        if($template->isInclude()) {
            $tpl->setVariable("IS_INCLUDE_CHECKED", 'checked="checked"');
        }
        else {
            $tpl->setVariable("IS_INCLUDE_CHECKED", '');
        }

        $tpl->setVariable("FILENAME", $template->getFilename());
        $tpl->setVariable("USER", $template->getChangedBy());
        $tpl->setVariable("LAST_EDITED", date("d.m.Y",$template->getTimestamp()));
        $tpl->setVariable('CSRF_TOKEN', get_csrf_token());
        $tpl->setVariable("TEMPLATE_CONTENT", htmlspecialchars($template->getContent()));
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
    $entrys = $service->getAllTemplates(true);
    
    $all = array();
    $system = array();
    foreach($entrys AS $tpl)
    {
        $temp = array(
            'name'          => $tpl->getName(),
            'filename'      => $tpl->getFilename(),
            'description'   => $tpl->getDescription(),
            'inWork'        => $tpl->isInWork(),
            'include'       => $tpl->isInclude(),
            'system'        => $tpl->isSystem(),
            'usage'         => $service->countTemplateUsage($tpl->getName())
        );
        if($tpl->isSystem())
            $system[] = $temp;
        else
            $all[] = $temp;
    }

    $selector = new TemplateSelect();
    $selector->setShowIncludes(true);
    $selector->setShowDeactivated(true);
    $selector->setShowSystemTemplates(true);
    $selector->setName('copyname');

    $smarty = getAdminSmarty();
    $smarty->assign('system', $system);
    $smarty->assign('templates', $all);
    $smarty->assign('tplcopy', $selector->getHtml());
    $smarty->display('Templates.tpl');
}

admin_footer();
