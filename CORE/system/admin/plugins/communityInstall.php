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
 * @subpackage configurations
 */

check_admin_login();
admin_header();

define('WIDTH_INPUT',        '300px');

define('PARAM_INSTALL_MODE',    's4lkhkj5svr');
define('INSTALL_MODE_EXECUTE',  'gI8n3Els143');

/**
 * Administrate your Consumer settings with this Plugin
 */

import('classes.util.formular.EditorSelect');
import('classes.consumer.ConsumerHelper');
import('classes.exception.WrongArgumentException');
import('classes.language.LanguageEnumeration');
import('classes.administration.AdminStyleService');
import('classes.util.html.FormularHelper');

$mode = extractVar(PARAM_INSTALL_MODE, 'form');
$DATA = extractVar('data', array());

$showFormular = TRUE;

if($mode == INSTALL_MODE_EXECUTE)
{
    import('classes.consumer.ConsumerInstaller');
    import('classes.consumer.ConsumerDefinition');
    import('classes.consumer.ConsumerService');

    $showFormular = FALSE;
    
    $cs = new ConsumerService();
    
	$minPwd = ConfigurationReader::getConfigurationValue('authentication', 'password.minimum.length', 5);
	$minUid = ConfigurationReader::getConfigurationValue('authentication', 'username.minimum.length', 5);

    if( isset($DATA['newdomain']) && $DATA['newdomain'] != ''  && 
        isset($DATA['admin']) && strlen($DATA['admin']) >= $minUid && 
        isset($DATA['password']) && strlen($DATA['password']) >= $minPwd && 
        isset($DATA['check']) && $DATA['check'] == $DATA['password'] && 
        $cs->getConsumerIdForDomain($DATA['newdomain']) < 0 ) 
    {
        $def = new ConsumerDefinition();
        $def->setDomain($DATA['newdomain']);
        $def->setAdminPassword($DATA['password']);
        $def->setAdminUser($DATA['admin']);
        $def->setSitename( (isset($DATA['sitename']) ? $DATA['sitename'] : '') );
        $def->setAdminStyle($DATA['default_style']);
        $def->setDefaultEditor($DATA['default_editor']);
        $def->setWriteStatistics($DATA['statistics']);
        $def->setMailServer($DATA['mailserver']);
        $def->setWebmasterEmail($DATA['webmastermail']);
        $def->setDefaultLanguage($DATA['default_lang']);
        //$def->setIsDefaultConsumer(true);
        
        $installer = new ConsumerInstaller();
        $res = $installer->createConsumer($def);
        
        if ($res == CONSUMER_ERROR_WRONG_TYPE) {
            displayError('Could not create community, configuration problem.');
            $showFormular = true;
        } else if ($res == CONSUMER_ERROR_UNDEFINED) {
            displayError('Not properly configured, might be a backend problem, please try again.');
            $showFormular = true;
        } else if ($res == CONSUMER_ERROR_CONFIG) {
            displayError('Could not create community configuration. Config writeable? Community already existing?');
            $showFormular = true;
        } 
        else 
        {
            foreach($installer->getError() AS $msg)
                displayError( $msg );
            
            foreach($installer->getInfo() AS $msg)
                displayMessage( $msg );
            
            // TODO implement check for default community
            if(false)
            {
                $helper = new ConsumerHelper();
                if($helper->setDefaultConsumer($DATA['name']))
                    displayMessage('Configured '.$DATA['name'].' as default community!');
                else
                    displayError('Could not configure '.$DATA['name'].' as default community!');
            }

            echo '<a href="http://'.$def->getDomain().'" target="_blank">'.getTranslation('preview').'</a>';
        }
    }
    else
    {
        if (!isset($DATA['newdomain']) || $DATA['newdomain'] == '')
            displayError( getTranslation("error_enter_domain") );
        if (!isset($DATA['admin']) || strlen($DATA['admin']) < $minUid)
            displayError( getTranslation("error_enter_adminuser") . ' ' . $minUid );
        if (!isset($DATA['password']) || strlen($DATA['password']) < $minPwd || !isset($DATA['check']) || $DATA['check'] != $DATA['password'])
            displayError( getTranslation("error_enter_adminpass") . ' ' . $minPwd );
        if ($cs->getConsumerIdForDomain($DATA['newdomain']) >= 0)
            displayError( getTranslation("error_domain_exists") );

        $showFormular = true;
    }
    
}

// -------------------------- [START] Formular --------------------------
if ($showFormular)
{
    $data = $DATA;
    $link = createAdminLink($MENU->getID(), array(PARAM_INSTALL_MODE => INSTALL_MODE_EXECUTE));

    // add hidden fields for statistic server settings!
    //FIXME make the statistic db settings dynamic
    $mail           = (isset($data['mailserver']))      ? $data['mailserver']       : '';
    $sitename 		= (isset($data['sitename']))   		? $data['sitename']    		: '';
    $webmastermail  = (isset($data['webmastermail']))   ? $data['webmastermail']    : '@';
    $newdomain      = (isset($data['newdomain']))       ? $data['newdomain']        : $_SERVER['HTTP_HOST'];
    $admin          = (isset($data['admin']))           ? $data['admin']            : '';

    $editSelect = new EditorSelect();
	$editSelect->setName('data[default_editor]');
	$editSelect->setPreSelected(ConfigurationReader::getConfigurationValue('editor', 'default.editor', 'plaintext'));
	$editorHtml = $editSelect->getHtml();
    
    // load all styles and create a Select Box
    $styleService = new AdminStyleService();
    $styles = $styleService->getAllStyles();
/*    if(count($styles) == 1) {
        $styleHtml = '<input type="hidden" name="data[default_style]" value="'.$styles[0]->getName().'">'.getTranslation('style_'.$styles[0]->getName(),$styles[0]->getName());
    }
    else {*/
        $styleHtml = '<select name="data[default_style]">';
        foreach($styles as $currentStyle) {
            $styleHtml .= '<option value="'.$currentStyle->getName().'">'.getTranslation('style_'.$currentStyle->getName(),$currentStyle->getName()).'</option>';
        }
        $styleHtml .= '</select>';
    //}

    $statistics   = '<select name="data[statistics]">';
    $statistics  .= '<option value="TRUE" selected>'.getTranslation('statistics_on').'</option>';
    $statistics  .= '<option value="0">'.getTranslation('statistics_off').'</option>';
    $statistics  .= '</select>';

    // template service	
    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("InstallCommunity.tpl.htm", true, true);

    $tpl->setVariable("FORM_LINK", $link);
    $tpl->setVariable("WIDTH_INPUT", WIDTH_INPUT);

    $tpl->setVariable("NEW_DOMAIN", createTextInputType('newdomain',$newdomain,''));

    $tpl->setVariable("SITENAME", createTextInputType('sitename',$sitename,''));
    $tpl->setVariable("SITE_EMAIL", createTextInputType('webmastermail',$webmastermail,''));
    $tpl->setVariable("MAILSERVER", createTextInputType('mailserver',$mail,''));
    $tpl->setVariable("EDITOR_CHOOSER", $editorHtml);
    $tpl->setVariable("STYLE_CHOOSER", $styleHtml);
    $tpl->setVariable("STATISTIC_CHOOSER", $statistics);
    $tpl->setVariable("DEFAULT_LANGUAGE", getDefaultLanguageChooser());

    $tpl->setVariable("ADMIN_NAME", createTextInputType('admin',$admin,''));
    $tpl->setVariable("ADMIN_PWD_CHOOSE", createPasswordField('password','',''));
    $tpl->setVariable("ADMIN_PWD_CHECK", createPasswordField('check','',''));

	$tpl->show();
}
// -------------------------- [END] Formular --------------------------


// get a html language chooser 
function getDefaultLanguageChooser() 
{
    $def_languages = '<select name="data[default_lang]">';
    $enum = new LanguageEnumeration();
    for($i = 0; $i < $enum->count(); $i++) 
    {
        $language = $enum->next();
        $sel = '';
        if ($language->getLocale() == ADMIN_LANGUAGE)
            $sel = ' selected';
        $def_languages .= '<option value="'.$language->getLocale().'"'.$sel.'>'.$language->getName(ADMIN_LANGUAGE).'</option>';
    }
    $def_languages .= '</select>';
    return $def_languages;
}

admin_footer();

?>