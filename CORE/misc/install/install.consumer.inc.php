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
 * @package bigace.installation
 */

/**
 * Create a new Consumer with this script.
 * This script holds all logic to fetch data and perform the Installation,
 * including File and Database Installation.
 */

// exit if we are not included in the main installation script
if(!defined('_BIGACE_INSTALL_PARENT_')) {
    die('Not runnable alone, go to '.dirname(__FILE__).'/index.php');
}

// TODO
// TODO translate all error messages
// TODO translate all error messages
// TODO


loadClass('administration', 'AdminStyleService');
loadClass('core', 'SQLHelper');
loadClass('sql', 'SimpleMySQLConnection');
loadClass('consumer', 'ConsumerInstaller');
loadClass('consumer', 'ConsumerDefinition');
loadClass('consumer', 'ConsumerHelper');
loadClass('consumer', 'ConsumerService');


/*  ########################################## DECIDE ABOUT MODUS ########################################## */

    $MODE = extractVar('mode' , '');

    switch ($MODE) {
        case '4':
                $cs = new ConsumerService();
                if (
                    isset($DATA['name']) && $DATA['name'] != ''
                    &&
                    $cs->getConsumerIdForDomain($DATA['name']) < 0
                    &&
                    isset($DATA['admin']) && $DATA['admin'] != ''
                    &&
                    isset($DATA['password']) && strlen($DATA['password']) >= 5 
                    &&
                    isset($DATA['check']) && strlen($DATA['check']) >= 5
                    &&
                    $DATA['check'] == $DATA['password']
                    )
                {
                        $GLOBALS['_BIGACE']['SQL_HELPER']  = new SQLHelper( new SimpleMySQLConnection() );
						$GLOBALS['_BIGACE']['SQL_HELPER']->execute("SET NAMES utf8");
						$GLOBALS['_BIGACE']['SQL_HELPER']->execute("SET CHARACTER SET utf8");

                        $def = new ConsumerDefinition();
                        $def->setDomain($DATA['name']);
				        $def->setSitename( (isset($DATA['sitename']) ? $DATA['sitename'] : '') );
                        $def->setAdminPassword($DATA['password']);
                        $def->setAdminUser($DATA['admin']);
                        $def->setAdminStyle('standard');
                        $def->setDefaultEditor('fckeditor');
                        $def->setWriteStatistics($DATA['statistics']);
                        $def->setMailServer('');
                        $def->setWebmasterEmail($DATA['webmastermail']);
                        $def->setDefaultLanguage($DATA['default_lang']);
                        $def->setIsDefaultConsumer(true);

                        $installer = new ConsumerInstaller();
                        $res = $installer->createConsumer($def);

                        if ($res == CONSUMER_ERROR_WRONG_TYPE) {
                            displayError('Could not create Consumer, configuration problem.');
                            displayNextButton($MENU, 'back');
                        } else if ($res == CONSUMER_ERROR_UNDEFINED) {
                            displayError( 'Not properly configured. Add missing field "'.$def->getMissingField().'" and try again!' );
                            displayNextButton($MENU, 'back');
                        } else if ($res == CONSUMER_ERROR_CONFIG) {
                            displayError('Could not create Consumer configuration, probably the Consumer is already existing.');
                            displayNextButton($MENU, 'back');
                        } else {
                            $createdCons = true;                            
                            $helper = new ConsumerHelper();
                            if(!$helper->setDefaultConsumer($DATA['name'])) {
                            	$createdCons = false;
                            }

                        	$errs = $installer->getError();
                        	$hasErrors = count($errs);
                        		
                            // yippieh, we did it!!!
                            if(count($errs) == 0) {
                                header('Location: ' . createInstallLink(MENU_STEP_SUCCESS));
                                exit;
                            }
                           
                            // something bad happended, installaton was not 100% successful 
                            show_install_header($MENU);

                            foreach($errs AS $msg)
                                displayError( $msg );
                            if(!$createdCons)
                                displayError('Could not configure '.$DATA['name'].' as default Community!');

                            echo '<h1>' . getTranslation('menu_step_'.MENU_STEP_COMMUNITY) . '</h1>';

                            $msg = '<a href=#" onclick="switchVisibility(\'infoMessagesDIV\');return false;">'.getTranslation('community_install_infos').'</a>';
                        	$msg = getTranslation('community_install_bad'). '<br>' . $msg;

                            echo '<div id="infoMessagesDIVLink">';
                            displayMessage( $msg );
                            echo '</div>';
                            
                            echo '<div style="display:none" id="infoMessagesDIV">';
                            foreach($installer->getInfo() AS $msg)
                                displayMessage( $msg );
                            if($createdCons)
                                displayMessage('Configured '.$DATA['name'].' as default Community!');
                            echo '</div>';

                            displayNextButtonLink('http://'.$DATA['name'].'/'.BIGACE_DIR_PATH, 'next');

                            show_install_footer($MENU);
                        }


                }
                else
                {
                    show_install_header($MENU);
                    echo '<h1>' . getTranslation('menu_step_'.MENU_STEP_COMMUNITY) . '</h1>';

					$displayDomainInput = false;
                    if (!isset($DATA['name']) || strlen($DATA['name']) == 0) {
                        displayError( getTranslation("error_enter_domain") );
                    } 
                    else {
	                    if ($cs->getConsumerIdForDomain($DATA['name']) >= 0) {
	                        displayError( getTranslation('community_exists') );
							$displayDomainInput = true;
						}
                    }
                    if (!isset($DATA['admin']) || strlen($DATA['admin']) < 4)
                        displayError( getTranslation("error_enter_adminuser") );
                    if (!isset($DATA['password']) || strlen($DATA['password']) < 5 || !isset($DATA['check']) || strlen($DATA['check']) < 5 || $DATA['check'] != $DATA['password'])
                        displayError( getTranslation("error_enter_adminpass") );
                    echo fetchConsumerSettings($DATA,createInstallLink( $MENU, array('mode' => '4') ), $displayDomainInput);
                    show_install_footer($MENU);
                }
                break;
        default:
                show_install_header($MENU);
                echo '<h1>' . getTranslation('menu_step_'.MENU_STEP_COMMUNITY) . '</h1>';
                // Before we show the Installation Screen for a Consumer, we check the File rights
                // if one fails, we display all Result to the End User with a help message, saying what has to be done!
                // this is used in "install.db.inc.php too"

                if( canStartInstallation() )
                {
                    echo fetchConsumerSettings($DATA,createInstallLink( $MENU, array('mode' => '4') ));
                }

                show_install_footer($MENU);
                break;
    }

/*  ########################################## END DECIDE ABOUT MODUS ########################################## */



function fetchConsumerSettings($data, $link, $displayDomainInput = false)
{
    $mail           = isset($data['mailserver'])                  ? $data['mailserver']       : '';
    $webmastermail  = isset($data['webmastermail'])               ? $data['webmastermail']    : '@';
    $admin          = isset($data['admin'])                       ? $data['admin']            : '';
    $sitename       = isset($data['sitename'])                    ? $data['sitename']         : '';
    $domain         = isset($data['name']) && $data['name'] != '' ? $data['name']             : $_SERVER['HTTP_HOST'];

    // plaintext is overwritten with the fckeditor extension!
    ?>
    <form action="<?php echo $link; ?>" method="post">
    <input type="hidden" name="data[default_editor]" value="fckeditor">
    <input type="hidden" name="data[default_style]" value="standard">
    <input type="hidden" name="data[mailserver]" value="">
    <?php
	if(!$displayDomainInput)
		echo '<input type="hidden" name="data[name]" value="'.$domain.'">';

/*

// hide them, installation should be as easy and quite as possible

    //$editorHtml = '<select name="data[default_editor]"><option value="fckeditor">FCKEditor</option><option value="plaintext">HTML Sourcecode Editor</option></select>';
    $editorHtml = '';

    // load all styles and create a Select Box
    $styleService = new AdminStyleService();
    $styleHtml = '<select name="data[default_style]">';
    $styles = $styleService->getAllStyles();
    foreach($styles as $currentStyle)
    {
        $styleHtml .= '<option value="'.$currentStyle->getName().'">'.getTranslation('style_'.$currentStyle->getName(),$currentStyle->getName()).'</option>';
    }
    $styleHtml .= '</select>';
*/
    $statistics   = '<select tooltipText="'.getTranslation('statistics_help').'" name="data[statistics]">';
    $statistics .= '<option value="TRUE" selected>'.getTranslation('statistics_on').'</option>';
    $statistics .= '<option value="0">'.getTranslation('statistics_off').'</option>';
    $statistics .= '</select>';

    installTableStart();
	if($displayDomainInput)
	    installRow('cid_domain', 'http://'.createTextInputType('name',$domain,'',false,getTranslation('cid_domain_help')));
    installRowTextInput('sitename', 'sitename', $sitename);
    installTableEnd();

    installTableStart( getTranslation('config_admin') );
    installRowTextInput('bigace_admin', 'admin', $admin);
    installRowTextInput('webmastermail', 'webmastermail', $webmastermail);
    installRowPasswordField('bigace_password', 'password','');
    installRowPasswordField('bigace_check', 'check','');
    installTableEnd();

    installTableStart( getTranslation('config_consumer') );
    //installRowTextInput('mailserver', 'mailserver', $mail);
    //installRow('default_editor', $editorHtml);
    //installRow('default_style', $styleHtml);
    installRow('def_language', getDefaultLanguageChooser( getTranslation('def_language_help') ));
    installRow('statistics', $statistics);
    installTableEnd();
    
    ?>
      <div align="right"><button class="buttonLink" type="submit"><?php echo getTranslation('next'); ?> &gt;&gt;</button></div>
    </form>
    <?php
}
