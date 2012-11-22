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

import('classes.smarty.SmartyDesign');
import('classes.smarty.SmartyTemplate');
import('classes.smarty.SmartyStylesheet');
import('classes.smarty.SmartyService');
import('classes.util.formular.TemplateSelect');
import('classes.util.formular.StylesheetSelect');

require_once(_BIGACE_DIR_LIBS.'sanitize.inc.php');

define('PARAM_DESIGN_NAME', 'designName');
define('PARAM_STYLE_MODE', 'mode');

$mode = isset($_GET[PARAM_STYLE_MODE]) ? $_GET[PARAM_STYLE_MODE] : (isset($_POST[PARAM_STYLE_MODE]) ? $_POST[PARAM_STYLE_MODE] : '');
$tid = isset($_GET[PARAM_DESIGN_NAME]) ? $_GET[PARAM_DESIGN_NAME] : (isset($_POST[PARAM_DESIGN_NAME]) ? $_POST[PARAM_DESIGN_NAME] : '');

$service = new SmartyService();

// ------------------------------------- DELETE DESIGN ---------------------------------------
if($mode == 'delete')
{
    if(isset($_POST[PARAM_DESIGN_NAME]) && check_csrf_token()) { 
        if($service->countDesignUsage(urldecode($_POST[PARAM_DESIGN_NAME])) == 0) {
            if($service->deleteDesign(urldecode($_POST[PARAM_DESIGN_NAME]))) {
                displayMessage( getTranslation('msg_deleted') );
            }
        } else {
            displayError( getTranslation('delete_in_use') );
        }
    }
    else {
        displayError( getTranslation('msg_missing_values') );
    }
	$mode = '';
}
// -------------------------------------------------------------------------------------------


// --------------------------------------- SAVE DESIGN ---------------------------------------
if($mode == 'save' || $mode == 'update')
{
    if(isset($_POST[PARAM_DESIGN_NAME]) && isset($_POST['description']) && 
       isset($_POST['stylesheet']) && isset($_POST['template']) && check_csrf_token()) 
    {
        $_POST['description'] = sanitize_plain_text($_POST['description']);

        $design = new SmartyDesign($_POST[PARAM_DESIGN_NAME]);
        if($design->getName() == $_POST[PARAM_DESIGN_NAME]) {
            $portletSupport = false;
            if(isset($_POST['portletSupport']) && $_POST['portletSupport'] == 1)
                $portletSupport = true;


            if($service->updateDesign($_POST[PARAM_DESIGN_NAME],$_POST['description'],$_POST['template'],$_POST['stylesheet'],$portletSupport)) {
                displayMessage( getTranslation('msg_saved') );

                $portlets = array();
                $temp = explode(",", trim($_POST['portletColumns']));
                foreach($temp AS $entry) {
                	$entry = trim($entry);
                	if(strlen($entry) > 0 && !isset($portlets[$entry]))
                		$portlets[] = trim($entry);
                }
                $service->setPortlets($_POST[PARAM_DESIGN_NAME],$portlets);

                $contents = array();
                $temp = explode(",", trim($_POST['contents']));
                foreach($temp AS $entry) {
                	$entry = trim($entry);
                	if(strlen($entry) > 0 && !isset($contents[$entry])) {
						$entName = trim($entry);
						$search = array("Ä","Ö","Ü","ä","ö","ü","ß","\t","\r","\n"," ");
						$replace = array("AE","OE","UE","ae","oe","ue","ss","-","-","-","-");
						$entName = str_replace($search, $replace, $entName);
						// FIXME - what is delim????
						$entName = preg_replace("/($delim)+/", $delim, preg_replace("/[^a-zA-Z0-9\/_-\\s]/", $delim, $entName));
                		$contents[] = $entName;
                	}
                }
                $service->setContents($_POST[PARAM_DESIGN_NAME],$contents);
                
				if($mode == 'update')
					$mode = 'edit';
				else if($mode == 'save')
					$mode = '';
            } 
			else {
                displayError( getTranslation('msg_saving_failed') );
				$mode = '';
            }
         }
         else {
                displayError( getTranslation('msg_saving_failed') . ' - wrong Design submitted!');
				$mode = '';
         }
    }
    else {
        displayError( getTranslation('msg_missing_values') );
		$mode = '';
    }
}
// -------------------------------------------------------------------------------------------


// --------------------------------------- EDIT DESIGN ---------------------------------------
if($mode == 'edit')
{
    if($tid != '' && check_csrf_token()) 
    {
        echo createStyledBackLink( createAdminLink($MENU->getID()) );

        $smarty = getAdminSmarty();

        $temp = new SmartyDesign($tid);
        $template = $temp->getTemplate();
        $stylesheet = $temp->getStylesheet();
        
        $selector = new TemplateSelect();
        $selector->setPreselected($template->getName());
        $selector->setShowIncludes(false);
        $selector->setShowDeactivated(false);
        $selector->setShowPreselectedIfDeactivated(true);
        $selector->setName('template');
        $smarty->assign("TEMPLATE", $selector->getHtml());

        $selector2 = new StylesheetSelect();
        $selector2->setPreselected($stylesheet->getName());
        $selector2->setName('stylesheet');
        $smarty->assign("STYLESHEET", $selector2->getHtml());

        if($temp->hasPortletSupport()) {
            $smarty->assign("PORTLET_SUPPORT", ' checked="checked"');
        } else {
            $smarty->assign("PORTLET_SUPPORT", '');
        }
        $temp3 = $temp->getPortletColumns();
        $pts = '';
        foreach($temp3 AS $name)
			$pts .= ($pts == '' ? '' : ',') . $name;         	

        $temp3 = $temp->getContentNames();
        $ptn = '';
        foreach($temp3 AS $name)
			$ptn .= ($ptn == '' ? '' : ',') . $name;         	
			
        $smarty->assign("PORTLETS", $pts);
        $smarty->assign("CONTENTS", $ptn);
        $smarty->assign("NAME", $temp->getName());
        $smarty->assign("DESCRIPTION", $temp->getDescription());
        $smarty->assign("EDIT_URL", createAdminLink($MENU->getID(), get_csrf_token(array(PARAM_DESIGN_NAME => urlencode($temp->getName()), PARAM_STYLE_MODE => 'edit')))) ;
        $smarty->assign("SAVE_URL", createAdminLink($MENU->getID(), get_csrf_token(array(PARAM_STYLE_MODE => 'update')))) ;

        $smarty->display('DesignEditor.tpl');
    } 
    else 
    {
        displayError( getTranslation('msg_missing_values') );
        $mode = '';
    }
}
// -------------------------------------------------------------------------------------------


// ------------------------------------ CREATE NEW DESIGN ------------------------------------
if($mode == 'new')
{
    if(isset($_POST[PARAM_DESIGN_NAME]) && trim($_POST[PARAM_DESIGN_NAME]) != '' && isset($_POST['description'])
       && isset($_POST['template']) && isset($_POST['stylesheet']) && check_csrf_token()) 
    {
        $_POST[PARAM_DESIGN_NAME] = sanitize_plain_text($_POST[PARAM_DESIGN_NAME]);
        $_POST['description'] = sanitize_plain_text($_POST['description']);

        $design = new SmartyDesign($_POST[PARAM_DESIGN_NAME]);
        if($design->getName() == $_POST[PARAM_DESIGN_NAME]) {
            displayError( getTranslation('name_exists') );
        } else {
    		$template = new SmartyTemplate($_POST['template']);
    		$stylesheet = new SmartyStylesheet($_POST['stylesheet']);
    		if($template->getName() == $_POST['template'] && $stylesheet->getName() == $_POST['stylesheet'])
    		{
                $portletSupport = false;
                if(isset($_POST['portletSupport']) && $_POST['portletSupport'] == 1)
                    $portletSupport = true;

                if($service->parseName($_POST[PARAM_DESIGN_NAME]) != $_POST[PARAM_DESIGN_NAME]) {
                    displayError( getTranslation('disallowed_character') );
                } else {
                    $service->createDesign($_POST[PARAM_DESIGN_NAME],$_POST['description'],$_POST['template'],$_POST['stylesheet'],$portletSupport);
                }
            }
            else {
                displayError( 'Template or Stylesheet does not exist ...');
            }
        }
    }
    else {
        displayError( getTranslation('msg_missing_values') );
    }
	$mode = '';
}
// -------------------------------------------------------------------------------------------


// ----------------------------------- LIST ALL DESIGN ---------------------------------------
if($mode == '')
{

    $_POST[PARAM_DESIGN_NAME] = sanitize_plain_text($_POST[PARAM_DESIGN_NAME]);
    $_POST['description'] = sanitize_plain_text($_POST['description']);

    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("AdminDesigns.tpl.htm", false, true);
    $entrys = $service->getAllDesigns();
    $cssClass = 'row1';

    foreach($entrys AS $temp)
    {
        if($service->countDesignUsage($temp->getName()) == 0) {
            $delete = '<form method="post" onsubmit="return confirm(\''.getTranslation('ask_delete').'\')" action="'.createAdminLink($MENU->getID() ).'">
                <input type="hidden" name="mode" value="delete" /><input type="hidden" name="'.PARAM_DESIGN_NAME.'" value="'.urlencode($temp->getName()).'" />
                '.get_csrf_token().'
                <button type="submit" class="delete">'.getTranslation('delete').'</button></form>';
        } else {
            $delete = '<img border="0" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'sign_no.png" onMouseOver="tooltip(\''.getTranslation('delete_in_use').'\')" onMouseOut="nd();">';
        }

        $template = $temp->getTemplate();
        $selector = new TemplateSelect();
        $selector->setTagAttribute('style','width:100%');
        $selector->setPreselected($template->getName());
        $selector->setShowIncludes(false);
        $selector->setShowDeactivated(false);
        $selector->setTagAttribute('disabled','disabled');
        $selector->setShowPreselectedIfDeactivated(true);
        $selector->setName('template');
        $tpl->setVariable("TEMPLATE", $selector->getHtml());

        $stylesheet = $temp->getStylesheet();
        $selector2 = new StylesheetSelect();
        $selector2->setTagAttribute('style','width:100%');
        $selector2->setPreselected($stylesheet->getName());
        $selector2->setName('stylesheet');
        $selector2->setTagAttribute('disabled','disabled');
        $tpl->setVariable("STYLESHEET", $selector2->getHtml());

        $prt = '<img src="'.$GLOBALS['_BIGACE']['style']['DIR'].($temp->hasPortletSupport() ? 'active.png' : 'inactive.png').'" border="0" />';     
        $tpl->setVariable("PORTLET", $prt);
        if($temp->hasPortletSupport()) {
        	$tpl->setVariable("PORTLET_SUPPORT", ' checked="checked"');
        } else {
            $tpl->setVariable("PORTLET_SUPPORT", '');
        }
        $tpl->setCurrentBlock("row");
        $tpl->setVariable("CSS", $cssClass);
        $tpl->setVariable("NAME", $temp->getName());
        $tpl->setVariable("DESCRIPTION", $temp->getDescription());
        $tpl->setVariable("EDIT_URL", createAdminLink($MENU->getID(), get_csrf_token(array(PARAM_DESIGN_NAME => urlencode($temp->getName()), PARAM_STYLE_MODE => 'edit')))) ;
        $tpl->setVariable("SAVE_URL", createAdminLink($MENU->getID(), get_csrf_token(array(PARAM_STYLE_MODE => 'save')))) ;
        $tpl->setVariable("DELETE", $delete);

        $tpl->parseCurrentBlock("row") ;

        $cssClass = ($cssClass == 'row1') ? 'row2' : 'row1';
    }

    $tpl->setVariable("CREATE_URL", createAdminLink($MENU->getID(), get_csrf_token(array(PARAM_STYLE_MODE => 'new'))));

    $tpl->setVariable("NEW_NAME", (isset($_POST[PARAM_DESIGN_NAME]) && $mode != 'new' && $mode != 'save' ? $_POST[PARAM_DESIGN_NAME]: ''));
    $tpl->setVariable("NEW_DESCRIPTION", (isset($_POST['description']) && $mode != 'new' && $mode != 'save' ? $_POST['description']: ''));

    $selector = new TemplateSelect();
    $selector->setTagAttribute('style','width:100%');
    $selector->setShowIncludes(false);
    $selector->setShowDeactivated(false);
    $selector->setName('template');
    $tpl->setVariable("TEMPLATE", $selector->getHtml());

    $selector2 = new StylesheetSelect();
    $selector2->setTagAttribute('style','width:100%');
    $selector2->setName('stylesheet');
    $tpl->setVariable("STYLESHEET", $selector2->getHtml());

    $tpl->show();
}
// -------------------------------------------------------------------------------------------


admin_footer();


