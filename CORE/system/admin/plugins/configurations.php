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

define('PARAM_PACKAGE', 'entryPackage');
define('PARAM_NAME',    'entryName');
define('PARAM_VALUE',   'entryValue');
define('PARAM_TYPE',    'entryType');
define('PARAM_METHOD',  'method');
define('METHOD_SAVE',   'saveEntry');
define('METHOD_NEW',    'createEntry');

import('classes.util.formular.DesignSelect');
import('classes.util.formular.TemplateSelect');
import('classes.util.formular.CategorySelect');
import('classes.util.html.FormularHelper');
import('classes.util.html.EmptyOption');
import('classes.configuration.ConfigurationReader');
import('classes.configuration.ConfigurationAdmin');
import('classes.util.LinkHelper');
import('classes.util.links.MenuChooserLink');
import('classes.util.html.Option');
import('classes.util.html.Select');
import('classes.util.formular.LanguageSelect');
import('classes.util.formular.GroupSelect');
import('classes.util.formular.EditorSelect');
import('classes.menu.MenuService');
require_once(_BIGACE_DIR_LIBS.'sanitize.inc.php');

$link = new MenuChooserLink();
$link->setItemID(_BIGACE_TOP_LEVEL);
$link->setJavascriptCallback('"+javascriptFunction');

$smarty = getAdminSmarty();
$smarty->assign('PARAM_PACKAGE', PARAM_PACKAGE);
$smarty->assign('MENU_CHOOSER_JS', 'javascriptFunction');
$smarty->assign('MENU_CHOOSER_LINK', '"' . LinkHelper::getUrlFromCMSLink($link));
$smarty->assign('CHOOSE_ID_JS', 'chooseMenuID');

$message = null;
$error = null;

// save entry
$mode = extractVar(PARAM_METHOD);
if ($mode == METHOD_SAVE)
{
	if(isset($_POST[PARAM_PACKAGE]) && isset($_POST[PARAM_NAME])) 
	{
	    $package = $_POST[PARAM_PACKAGE];
	    $values  = $_POST[PARAM_NAME];

	    if(is_array($values)) 
	    {
		    foreach($values AS $name => $value) {
			    if ($name != '') {
                    $value = sanitize_plain_text($value);
			    	ConfigurationAdmin::updateEntry($package, $name, $value);
			    }
			    else {
			        $error = getTranslation('error_config_update');
			    }
		    }
	    }
	    else {
	        $error = getTranslation('error_config_update');
	    }
	}
    else {
        $error = getTranslation('error_config_update');
    }
}

// Create a new configuration entry
if($mode == METHOD_NEW)
{
    $package = extractVar(PARAM_PACKAGE);
    $name    = extractVar(PARAM_NAME);
    $value   = extractVar(PARAM_VALUE);
    $type    = extractVar(PARAM_TYPE);

    if ($package != '' && $name != '' && $type != '') {
        $package = sanitize_plain_text($package);
        $value = sanitize_plain_text($value);
        $name = sanitize_plain_text($name);
        $type = sanitize_plain_text($type);
    	
        ConfigurationAdmin::createEntry($package, $name, $type, $value);
    }
    else {
	    $error = getTranslation('error_config_create');
    }
    
}

$smarty->assign('MESSAGE', $message);
$smarty->assign('ERROR', $error);

$reader = new ConfigurationReader();

$chooserID = 0; // temp variable to increase for easier form handling
$ENUM = $reader->getAll();
$mService = new MenuService();

$packages = array();	// all config entrys will be kept here

for($i=0; $i < count($ENUM); $i++)
{
    $temp = $ENUM[$i];
    
    if(!isset($packages[$temp->getPackage()])) {
    	$packages[$temp->getPackage()] = array(
    		'name'		=> $temp->getPackage(),
	    	'action'	=> createAdminLink($MENU->getID(), array(PARAM_PACKAGE => $temp->getPackage(), PARAM_METHOD => METHOD_SAVE)),
	    	'configs'	=> array(),
    	);
    }
        
    $formValName = PARAM_NAME."[".$temp->getName()."]";

    // temporarly remember the formElement
    $formElement = null;
    
    if ($temp->getType() == CONFIG_TYPE_BOOLEAN) 
    {
        $tmp = array('TRUE' => 'true', 'FALSE' => '0');
        $val = createNamedSelectBox($formValName, $tmp, $temp->getValue(), '',false,$temp->getName());
        $formElement = $val;
    }
    else if ($temp->getType() == CONFIG_TYPE_STRING || $temp->getType() == CONFIG_TYPE_CLASSNAME) 
    {
        $val = htmlspecialchars($temp->getValue());
        $tmp = createNamedTextInputType($formValName, $val, '200', false);
        $formElement = $tmp . ' ' . getTranslation('type_string');
    }
    else if ($temp->getType() == CONFIG_TYPE_INT) 
    {
        $tmp = createNamedTextInputType($formValName, $temp->getValue(), '200', false);
        $formElement = $tmp . ' ' . getTranslation('type_int');
    }
    else if ($temp->getType() == CONFIG_TYPE_LONG)
    {
        $tmp = createNamedTextInputType($formValName, $temp->getValue(), '200', false);
        $formElement = $tmp . ' ' . getTranslation('type_long');
    }
    else if ($temp->getType() == CONFIG_TYPE_ADMIN_STYLE)
    {
        $tmp = createNamedSelectBox($formValName, $STYLE_SERVICE->getAvailableStyles(), $temp->getValue()); 
        $formElement = $tmp;
    }
    else if ($temp->getType() == CONFIG_TYPE_GROUP_ID)
    {
    	$groupSelect = new GroupSelect(); 
    	$groupSelect->setName($formValName);
    	$groupSelect->setPreSelectedID( $temp->getValue());
        $formElement = $groupSelect->getHtml() . ' ' . getTranslation('type_group');
    }
    else if ($temp->getType() == CONFIG_TYPE_EDITOR)
    {
        $editSelect = new EditorSelect();
        $editSelect->setName($formValName);
        $editSelect->setPreSelected($temp->getValue());
        $formElement = $editSelect->getHtml();
    }
    else if ($temp->getType() == CONFIG_TYPE_LANGUAGE)
    {
        $select = new LanguageSelect(ADMIN_LANGUAGE);
        $select->setName($formValName);
        $select->setPreSelected($temp->getValue());
        $formElement = $select->getHtml();
    }
    else if ($temp->getType() == CONFIG_TYPE_TEMPLATE)
    {
        $selector = new TemplateSelect();
        $selector->setPreselected($temp->getValue());
        $selector->setShowIncludes(false);
        $selector->setShowDeactivated(false);
        $selector->setShowPreselectedIfDeactivated(true);
        $selector->setShowSystemTemplates(true);
        $selector->setName($formValName);
        $formElement = $selector->getHtml();
    }
    else if ($temp->getType() == CONFIG_TYPE_TEMPLATE_INCLUDE)
    {
        $selector = new TemplateSelect();
        $selector->setPreselected($temp->getValue());
        $selector->setShowIncludes(true);
        $selector->setShowDeactivated(false);
        $selector->setShowPreselectedIfDeactivated(true);
        $selector->setShowSystemTemplates(true);
        $selector->setName($formValName);
        $formElement = $selector->getHtml();
    }
    else if ($temp->getType() == CONFIG_TYPE_CATEGORY_ID)
    {
        $selector = new CategorySelect();
        $selector->setPreSelectedID($temp->getValue());
        $selector->setName($formValName);
        $formElement = $selector->getHtml();
    }
    else if ($temp->getType() == CONFIG_TYPE_DESIGN)
    {
        $selector = new DesignSelect();
        if($temp->getValue() == null || $temp->getValue() == "") {
        	$o = new EmptyOption();
        	$o->setIsSelected();
        	$selector->addOption( $o );
        } else {
       		$selector->setPreselected($temp->getValue());
       	}
        $selector->setName($formValName);
        $selector->setSortAlphabetical(true);
        $formElement = $selector->getHtml();
    }
    else if ($temp->getType() == CONFIG_TYPE_MENU_ID)
    {
        $menuName = '';
        if($temp->getValue() != '') {
            $ti = $mService->getMenu($temp->getValue());
            $menuName = $ti->getName();
        }
        $html = "\n" . '<script language="javascript">' . "\n";
        $html .= ' <!-- ' . "\n";
        $html .= 'function setMenu'.$chooserID.'(id, language, name) ' . "\n";
        $html .= '{' . "\n";
        $html .= '   document.getElementById("'.$formValName.'_id_'.$chooserID.'").value = id;' . "\n";
        $html .= '   document.getElementById("'.$formValName.'_name_'.$chooserID.'").value = name;' . "\n";
        $html .= '}' . "\n";
        $html .= ' // --> ' . "\n";
        $html .= '</script>' . "\n";
        $html .= '<input style="border:1px solid #000000;width:100px;margin-right:5px" type="text" id="'.$formValName.'_name_'.$chooserID.'" name="'.$formValName.'_name" value="'.$menuName.'" disabled="disabled">' . "\n";
        $html .= '<input style="border:1px solid #000000;width:50px;" type="text" id="'.$formValName.'_id_'.$chooserID.'" name="'.$formValName.'" value="'.$temp->getValue().'">' . "\n";
        $html .= ' <button onclick="chooseMenuID(\'setMenu'.$chooserID.'\', \''.$formValName.'\'); return false;">'.getTranslation('choose').'</button>' . "\n";
        $formElement = $html;
        $chooserID++;
    }
    else if($temp->getType() == CONFIG_TYPE_LOGLEVEL)
    {
        $allLevel = $GLOBALS['LOGGER']->getErrorLevel();
        $levelSelect  = '<select name="'.$formValName.'">';
        foreach($allLevel AS $levelValue => $levelName) 
        {
            $checked = ($levelValue == $temp->getValue() ? 'selected ' : '');
            $levelSelect .= '<option '.$checked.' value="'.$levelValue.'">'.$levelName.'</option>';
        }
        $levelSelect .= '</select>';
        $formElement = $levelSelect;
    }
    else 
    {
        $formElement = $temp->getValue() . ' (Type: '.$temp->getType().')';
    }
    
    $packages[$temp->getPackage()]['configs'][] = array(
    	'package' 	=> $temp->getPackage(),
    	'name'		=> $temp->getName(),
    	'type'		=> $temp->getType(),
    	'value'		=> $temp->getValue(),
    	'formInput'	=> $formElement
    );
}

$smarty->assign('CONFIGURATIONS', $packages);


$types = array(
	CONFIG_TYPE_EDITOR 		=> CONFIG_TYPE_EDITOR, 
    CONFIG_TYPE_STRING 		=> CONFIG_TYPE_STRING,
    CONFIG_TYPE_ADMIN_STYLE => CONFIG_TYPE_ADMIN_STYLE,
    CONFIG_TYPE_INT 		=> CONFIG_TYPE_INT,
    CONFIG_TYPE_LONG 		=> CONFIG_TYPE_LONG,
	CONFIG_TYPE_BOOLEAN 	=> CONFIG_TYPE_BOOLEAN,
	CONFIG_TYPE_TIMESTAMP 	=> CONFIG_TYPE_TIMESTAMP,
	CONFIG_TYPE_MENU_ID 	=> CONFIG_TYPE_MENU_ID,
	CONFIG_TYPE_GROUP_ID 	=> CONFIG_TYPE_GROUP_ID,
    CONFIG_TYPE_TEMPLATE 	=> CONFIG_TYPE_TEMPLATE,
    CONFIG_TYPE_TEMPLATE_INCLUDE 	=> CONFIG_TYPE_TEMPLATE_INCLUDE,
    CONFIG_TYPE_LOGLEVEL 	=> CONFIG_TYPE_LOGLEVEL,
    CONFIG_TYPE_LANGUAGE 	=> CONFIG_TYPE_LANGUAGE,
    CONFIG_TYPE_CATEGORY_ID => CONFIG_TYPE_CATEGORY_ID,
    CONFIG_TYPE_DESIGN		=> CONFIG_TYPE_DESIGN,
);

$smarty->assign('NEW_TYPES', $types);
$smarty->assign('NEW_URL', createAdminLink($MENU->getID(), array(PARAM_METHOD => METHOD_NEW)));
$smarty->assign('NEW_PARAM', PARAM_TYPE);

$smarty->display('Configurations.tpl');

admin_footer();

?>
