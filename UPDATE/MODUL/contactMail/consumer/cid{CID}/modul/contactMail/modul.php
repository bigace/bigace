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
 * @package bigace.modul
 */

import('classes.modul.ModulService');
import('classes.modul.Modul');
import('classes.email.TextEmail');
import('classes.util.html.FormularHelper');

define('WEBMAIL_MODE_FORMULAR',     1); // Show Overview mit Input Values (if user has WebMail Admin Fright show link to Admin Box)
define('WEBMAIL_MODE_SEND',         2); // Send email if all required fields are submitted, otherwise show formular

define('WEBMAIL_PRJ_FIELD_ANSWER',  'contactMail_answer'); // save the answer text in this project field
define('WEBMAIL_PRJ_FIELD_SENDTO',  'contactMail_sendto'); // save the email recipients in this project field
define('WEBMAIL_PRJ_FIELD_CNT_TOP',  'contactMail_content_top'); // whether to show content above or below
define('WEBMAIL_PRJ_FIELD_CNT_BOTTOM',  'contactMail_content_bottom'); // whether to show content below the formular
define('WEBMAIL_PRJ_FIELD_OPTIONAL_FIELDS',  'contactMail_optional_fields'); // whether to show content above the formular
define('WEBMAIL_PRJ_FIELD_SYS_INFOS',  'contactMail_system_infos'); // whether system infos will be included in the email
define('WEBMAIL_PRJ_FIELD_SUBJECT',  'contactMail_subject'); // start email subject with this string

loadLanguageFile('bigace');
loadLanguageFile('formular');

/**
 * This modul displays a Mail Formular, that can send an Email to a 
 * configured List of recipients.
 * 
 * It also displays a Feedback message to the User when successful sended,
 * or otherwise a Error message.
 */

// holds the formular values submitted by the user
$MYMAIL = extractVar('mail_fields', array());

// get the email configuration
$modul = new Modul($MENU->getModulID());
$modulService = new ModulService();
$config = $modulService->getModulProperties($MENU, $modul);

// defines if this webmail page is properly configured!
$configured = false;
if (isset($config[WEBMAIL_PRJ_FIELD_SENDTO]) && strlen(($config[WEBMAIL_PRJ_FIELD_SENDTO])) >= 3 && strpos($config[WEBMAIL_PRJ_FIELD_SENDTO], '@') !== false) {
    $configured = true;
}

$useCaptcha = (isset($config['contactMail_use_captcha']) ? (bool)$config['contactMail_use_captcha'] : false);
$contentBelow = (isset($config[WEBMAIL_PRJ_FIELD_CNT_BOTTOM]) && ((bool)$config[WEBMAIL_PRJ_FIELD_CNT_BOTTOM]) === true); 
$contentTop = (isset($config[WEBMAIL_PRJ_FIELD_CNT_TOP]) && ((bool)$config[WEBMAIL_PRJ_FIELD_CNT_TOP]) === true);
$inludeSystemInfo = (isset($config[WEBMAIL_PRJ_FIELD_SYS_INFOS]) && ((bool)$config[WEBMAIL_PRJ_FIELD_SYS_INFOS]) === true);
$startSubject = (isset($config[WEBMAIL_PRJ_FIELD_SUBJECT]) ? $config[WEBMAIL_PRJ_FIELD_SUBJECT] : "");

/* #########################################################################
 * ############################  Show Admin Link  ##########################
 * #########################################################################
 */
if ($modul->isModulAdmin())
{
    import('classes.util.links.ModulAdminLink');
    import('classes.util.LinkHelper');
    $mdl = new ModulAdminLink();
    $mdl->setItemID($MENU->getID());
    $mdl->setLanguageID($MENU->getLanguageID());

    ?>
    <script type="text/javascript">
    <!--
    function openAdmin()
    {
        fenster = open("<?php echo LinkHelper::getUrlFromCMSLink($mdl); ?>","ModulAdmin","menubar=no,toolbar=no,statusbar=no,directories=no,location=no,scrollbars=yes,resizable=no,height=350,width=400,screenX=0,screenY=0");
        bBreite=screen.width;
        bHoehe=screen.height;
        fenster.moveTo((bBreite-400)/2,(bHoehe-350)/2);
    }
    // -->
    </script>
    <?php

    echo '<div class="modulAdminLink" align="left"><a onClick="openAdmin(); return false;" href="'.LinkHelper::getUrlFromCMSLink($mdl).'"><img src="'._BIGACE_DIR_PUBLIC_WEB.'system/images/preferences.gif" border="0" align="top"> '.Translations::translateGlobal('modul_admin').'</a></div>';
}    

if(!$configured) 
{
    echo '<p><b>';
    echo getTranslation('unconfigured', 'This formular is not properly configured. Please call your Administrator.');
    echo '</b></p>';
}
else
{
	/*********************************************
	* START OUTPUT
	*********************************************/
	
	if($contentTop) {
		echo $MENU->getContent();
	}
	
	$showForm = true;
	$formTitle = '';
	
	if (isset($_POST['mail_fields']))
	{
		$create = !$useCaptcha; 
		if($useCaptcha) 
		{
    		$captcha = ConfigurationReader::getValue("system", "captcha", null);
			if($captcha == null) {
	        	$GLOBALS['LOGGER']->logError("Captcha failed, wrong configuration: 'system/captcha'");
			}
			else {
			    if(($create = $captcha->validate($_POST['captcha'],$_POST['validate'])) === false) {
					$showForm = true;
					$formTitle = getTranslation('captcha.wrong.code');
			    }
			}
		}
		
	    if (!isset($MYMAIL['text']) || $MYMAIL['text'] == '' || !isset($MYMAIL['name']) || $MYMAIL['name'] == '' )
	    {
	    	// Show input values again. Required fields are missing.
	    	$showForm = true;
			$formTitle = getTranslation('form.missing.values');
	    } 
	    else if($create) 
	    {
	    	$LANGUAGE = new Language(_ULC_);
	        // Send email
	        
	        $email = new TextEmail();
	        $email->setCharacterSet($LANGUAGE->getCharset());
	        $email->setTo( $config[WEBMAIL_PRJ_FIELD_SENDTO] );
	        $email->setFromName( $MYMAIL['name']  );
	        $email->setFromEmail( $MYMAIL['from'] );
	        $email->setSubject( $startSubject.$MYMAIL['subject'] );
	        $dateToSend = date("d.m.Y g:i", time());
	        $contentToSend  = getTranslation('email_intro', 'The following message was sent via the BIGACE-Webmail-Modul!');
	        
	        $addInfos = '';
	        
		    $optFields = getOptionalFields($config);
		    if(count($optFields) > 0) {
		    	foreach($optFields AS $key) {
		    		if(isset($MYMAIL[$key]))
						$addInfos .= "\r\n" . $key.':' . $MYMAIL[$key];
		    	}
		    }
	        
		    if(strlen($addInfos) > 0) {
		        $contentToSend .= "\r\n\r\n-----------------------------------------------------\r\n";
		        $contentToSend .= $addInfos;
		    }
		    
	        $contentToSend .= "\r\n\r\n-----------------------------------------------------\r\n\r\n";
	        $contentToSend .= $MYMAIL['text'];
	        if ($inludeSystemInfo) {
		        $contentToSend .= "\r\n\r\n-----------------------------------------------------\r\n\r\n";
		        $contentToSend .= 'System Information:' . "\r\n\r\n";
		        $contentToSend .= 'URL : '. LinkHelper::getUrlFromCMSLink( LinkHelper::getCMSLinkFromItem($GLOBALS['MENU']) ) . "\r\n";
		        $contentToSend .= 'CID : '. _CID_ . "\r\n";
		        $contentToSend .= 'CMS : BIGACE '. _BIGACE_ID . "\r\n";
		        $contentToSend .= 'DATE: '. $dateToSend . "\r\n";
	        }
	        
	        $email->setContent( $contentToSend );
	        $didMail = $email->sendMail();
	    
	        echo '<div class="mailAnswer">';
	        echo '<p>';
	        if ($didMail) 
	        {
                echo (	isset($config[WEBMAIL_PRJ_FIELD_ANSWER]) ? 
    					$config[WEBMAIL_PRJ_FIELD_ANSWER] : 
    					getTranslation('email_sent')
    			);
				$showForm = false;
	        }
	        else {
	            echo getTranslation('contact_error_msg');
	            $formTitle = getTranslation('error_retry','Error... Please retry!');
	            $showForm = true;
	        }
	        echo '</p>';
	        echo '</div>';
	    }
	}
	
	if($showForm) 
	{
	    showMailFormular($formTitle, $MYMAIL, $config, $modul, $useCaptcha);
	}

	if($contentBelow) {
		echo $MENU->getContent();
	}
	
}

function getOptionalFields($config)
{
    $optFields = (isset($config[WEBMAIL_PRJ_FIELD_OPTIONAL_FIELDS]) ? $config[WEBMAIL_PRJ_FIELD_OPTIONAL_FIELDS] : '');
    if(strlen(trim($optFields)) > 0)
    {
	    return explode(",", trim($optFields));
    }
	return array();
}

/*
* Show Email Formular and - if user has the rights - show link to the admin box
*/
function showMailFormular($title, $MYMAIL, $config, $modul, $useCaptcha)
{
    if (!isset($MYMAIL['text'])) $MYMAIL['text'] = '';
    if (!isset($MYMAIL['name'])) $MYMAIL['name'] = '';
    if (!isset($MYMAIL['from'])) $MYMAIL['from'] = '';
    if (!isset($MYMAIL['subject'])) $MYMAIL['subject'] = '';
    
    $formUrl = LinkHelper::getUrlFromCMSLink( LinkHelper::getCMSLinkFromItem($GLOBALS['MENU']) );
    
    $fields = array(
    	'name' 		=> getTranslation('form_name','Name').'*',
    	'from' 		=> getTranslation('form_email','E-Mail'),
    	'subject'	=> getTranslation('form_subject','Subject'),
    );
    
    // add additional configured fields
    $optFields = getOptionalFields($config);
    if(count($optFields) > 0)
    {
	    foreach($optFields AS $fieldName) {
	    	if(strlen(trim($fieldName)) > 0)
	    		$fields[$fieldName] = $fieldName;
	    }
    }
    
	?>					    
	<form name="SendEmail" id="EmailForm" action="<?php echo $formUrl; ?>" method="post">
	<div id="autoGenerated">
		<table cellspacing="1" cellpadding="4" align="center" width="100%" class="autoTable" summary="Email Formular">
			<?php
			if ($title != '') {
				echo 
				'<tr>
					<th colspan="2">'.$title.'</th>
				</tr>';
			}
			
			$css = 'autoRow1';
			foreach($fields AS $key => $title) 
			{
				echo '<tr>';
				echo '<td class="'.$css.'" align="left"><label for="email'.$key.'">'.$title.'</label></td>';
				echo '<td class="'.$css.'" align="left"><input id="email'.$key.'" type="text" size="40" name="mail_fields['.$key.']" value="'.(isset($MYMAIL[$key]) ? $MYMAIL[$key] : '').'" /></td>';
				echo '</tr>';
				$css = ($css == 'autoRow1' ? 'autoRow2' : 'autoRow1');
			}
			?>
			<tr>
				<td class="<?php echo $css; ?>" align="left"><label for="emailText"><?php echo getTranslation('form_message','Message'); ?>*</label></td>
				<td class="<?php echo $css; ?>" align="left"><textarea id="emailText" name="mail_fields[text]" cols="50" rows="10"><?php echo $MYMAIL['text']; ?></textarea></td>
			</tr>
			<?php 
			if($useCaptcha) 
			{
				$css = ($css == 'autoRow1' ? 'autoRow2' : 'autoRow1');
	    		$captcha = ConfigurationReader::getValue("system", "captcha", null);
				if($captcha == null) {
		        	$GLOBALS['LOGGER']->logError("Captcha failed, wrong configuration: 'system/captcha'");
				}
				else {
				    $ccode = $captcha->get();
					?>
					<tr>
						<td class="<?php echo $css; ?>" align="left"><label for="emailCaptcha"><?php echo getTranslation('captcha');?> *</label></td>
						<td class="<?php echo $css; ?>" align="left">
						    <input type="hidden" name="validate" value="<?php echo $ccode; ?>" />
							<img border="0" src="<?php echo $ccode; ?>" alt="<?php echo getTranslation('captcha');?>" />
							<br />
		                    <input id="emailCaptcha" name="captcha" maxlength="30" size="15" type="text" />
						</td>
					</tr>
					<?php
				}
			}
			?>
			<tr>
				<td class="<?php echo ($css == 'autoRow1' ? 'autoRow2' : 'autoRow1'); ?>" colspan="2" align="left">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" class="<?php echo $css; ?>" align="left"><span class="mandatory">(*) <?php echo getTranslation('required','Required'); ?></span></td>
			</tr>
			<tr>
				<td class="autoBottom" colspan="2" align="center"><input class="autoSubmit" type="submit" value="<?php echo getTranslation('form_send','Send'); ?>" /></td>
			</tr>
		</table>
	</div>
	</form>
	<?php
}
