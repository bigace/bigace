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

loadLanguageFile('about');

$ACTION = "credits";
$FEEDBACK = array(
    'name' => '',
    'message' => '',
    'email' => '@',
    'subject' => '',
    'copy' => false,
    'error' => '',
    'status' => '',
    'url' => createAdminLink($GLOBALS['MENU']->getID())
);

// ------ FEEDBACK EMAIL -------------------------------------------------------
$services = ServiceFactory::get();
$PRINCIPALS = $services->getPrincipalService();
$principal = $PRINCIPALS->lookupByID($GLOBALS['_BIGACE']['SESSION']->getUserID());
$attributes = $PRINCIPALS->getAttributes($principal);
$name = (isset($attributes['firstname']) ? $attributes['firstname']: '');
$name .= (isset($attributes['lastname']) ? ' '.$attributes['lastname']: '');
$FEEDBACK['name'] = $name;

// Damn spammer - they really find every email in the www...
// I guess masking doesn't solve the problem since it was alread found, but i try it nevertheless!
$t = ConfigurationReader::getConfigurationValue("admin", "feedback.email", base64_decode('ZmVlZGJhY2tAYmlnYWNlLmRl'));

if (isset($_POST) && isset($_POST['mode']))
{
    import('classes.email.TextEmail');

    $data = (isset($_POST['data']) ? $_POST['data'] : array());
    
	$from = (isset($data['email']) && strlen(trim($data['email']) && trim($data['email']) != '@') > 0) ? $data['email'] : $GLOBALS['_BIGACE']['MAIL']['from_email'];
	$name = (isset($data['name']) && strlen(trim($data['name'])) > 0) ? $data['name'] : '';
	$subject = (isset($data['subject']) && strlen(trim($data['subject'])) > 0) ? $data['subject'] : "";
	$message = (isset($data['message']) && strlen(trim($data['message'])) > 0) ? $data['message'] : "";
	$sendCopy = (isset($data['emailOwn']));
	
	if($subject != "" && $message != "")
	{
		$sent = false;
		$msg = 'Email was submitted';
		$temail = new TextEmail();
		$temail->setSubject($subject);
		if ($name != '')
			$temail->setFromName($name);
		$temail->setFromEmail($from);
		$temail->setContent($message);
		$temail->setRecipient(AUTHOR_EMAIL);

	    $LANGUAGE = new Language(ADMIN_LANGUAGE);
	    $temail->setCharacterSet($LANGUAGE->getCharset());
	    
		// Send the message
		if($temail->sendMail($message))
		{
			$msg = getTranslation('feedback_send_success');
			$sent = true;
			// Send a copy of this email to the initiator of this message
			if($sendCopy)
			{
				$temail->setRecipient($from);
				if($temail->sendMail($message)){
					$msg .= '<br>' . getTranslation('feedback_send_cp_success');
				}
				else {
					$msg .= '<br>' . getTranslation('feedback_error_cp_sending');
				}
			}
			
        	$FEEDBACK['status'] = $msg;
		}
		else 
		{
			$FEEDBACK['error'] = getTranslation('feedback_error_sending');
		}
		
		// only set values if message couldn't be sent
		if ($sent === false) 
		{
		    $FEEDBACK['name'] = $name;
		    $FEEDBACK['email'] = $from;
		    $FEEDBACK['message'] = $message;
		    $FEEDBACK['copy'] = $sendCopy;
		    $FEEDBACK['subject'] = $subject;
		}
	}
	else
	{
    	$FEEDBACK['error'] = getTranslation('feedback_error_required');
	}

	$ACTION = 'feedback';
}

// ------ FETCH CREDITS --------------------------------------------------------
$dirName = _ADMIN_INCLUDE_DIRECTORY.'credits/';
$allFiles = array();
$handle = opendir ( $dirName );
while (false !== ($file = readdir ($handle))) {
    $fullname = $dirName . $file;
    if (is_file($fullname) && is_readable($fullname)) {
   		array_push($allFiles, $fullname);
    }
}
closedir($handle);

$allCredits = array();

foreach($allFiles AS $currentIniFile) 
{
    $ini = parse_ini_file($currentIniFile, TRUE);
    $title = isset($ini["title"]) ? $ini["title"] : 'Credit';
    $allInis = array();

    foreach($ini AS $key => $value) {
        if ($key != "title" && is_array($value))  {
            $allInis[$key] = $value;
	    }
    }
    $allCredits[$title] = $allInis;
}

$allCredits = Hooks::apply_filters('credits', $allCredits);

// Load the license
$license = file_get_contents(_ADMIN_INCLUDE_DIRECTORY.'gpl.txt');

// -----------------------------------------------------------------------------

$smarty = getAdminSmarty();
$smarty->assign("ACTION", $ACTION);
$smarty->assign("FEEDBACK", $FEEDBACK);
$smarty->assign("CREDITS", $allCredits);
$smarty->assign("LICENSE", nl2br($license));
$smarty->assign("YEAR_TODAY", date("Y"));
$smarty->display('About.tpl');

unset($smarty);

admin_footer();

