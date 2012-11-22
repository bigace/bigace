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

/**
 * Plugin for creating users.
 */
import('classes.language.Language');
import('classes.language.LanguageEnumeration');
import('classes.group.GroupEnumeration');
import('classes.group.GroupAdminService');
import('classes.right.RightAdminService');
import('classes.core.ServiceFactory');
import('classes.util.html.FormularHelper');
import('classes.util.formular.GroupSelect');
import('classes.email.EmailHelper');

  
include_once(_ADMIN_INCLUDE_DIRECTORY.'answer_box.php');
require_once(_BIGACE_DIR_LIBS.'sanitize.inc.php');

if (isset($_GET['mode']) && $_GET['mode'] == 'create' && 
	isset($_POST['userName']) && isset($_POST['passwordnew']) && 
	isset($_POST['passwordcheck'])) 
{
		$newUserName = sanitize_plain_text(htmlspecialchars($_POST['userName']));
		$data = array();
		$data['new'] = $_POST['passwordnew'];
	    $data['check'] = $_POST['passwordcheck'];
	    $data['language'] = $_POST['language'];
	    $data['state'] =  $_POST['state'];
	    $data['groups'] =  $_POST['userGroups'];
	   	$data['email'] =  $_POST['email'];
	    
	    if(!is_array($data['groups']))
	    	$data['groups'] = array($data['groups']);
	    	
	    $error = null;

	    // check if any required value is empty
    	if ($data['new'] == '' || $data['check'] == '' || $_POST['userName'] == '' || $_POST['email'] == '') { 
	    	$error = getTranslation('create_user_missing_values');
    	}
		
	    // check if the email adress is valid formatted
	    if($error == null && !bigace_is_valid_email($data['email'])) {
	    	$error = getTranslation('msg_email_is_wrong');
	    }
	    
	    // check if both passwords matches
	    if ($error == null && $data['check'] != $data['new']) {
	    	$error = getTranslation('msg_pwd_no_match');
	    }

	    // try to find an existing user with same name
		$services = ServiceFactory::get();
		$PRINCIPALS = $services->getPrincipalService();

        $princ = $PRINCIPALS->lookup($newUserName);
        if ($princ != null) {
        	$error = getTranslation('msg_user_exists') . ': ' . $newUserName;
        }
       
        // if any problem was found, display the creation formular again
	    if($error != null) {
            displayError($error);
            createNewUserMask( $newUserName );
	    }
	    else {
            $newPass 	 = $data['new'];
            $newLanguage = (isset($data['language'])) ? $data['language'] : '2';

            $newPrincipal = $PRINCIPALS->createPrincipal($newUserName, $newPass, $newLanguage);
            if($newPrincipal != null)
            {
                $newState    = (isset($data['state'])) ? $data['state'] : false;

                $PRINCIPALS->setParameter($newPrincipal, PRINCIPAL_PARAMETER_ACTIVE, (bool)$newState);
                $PRINCIPALS->setParameter($newPrincipal, PRINCIPAL_PARAMETER_EMAIL, $data['email']);

                $groupAdmin = new GroupAdminService();
                foreach($data['groups'] AS $newGroup) {
                    $groupAdmin->addToGroup($newGroup, $newPrincipal->getID());
                }

                unset($groupAdmin);
                unset($newGroup);
                unset($newState);
                unset($newLanguage);
                unset($newPass);
            }
            $hidden = array(
                            'mode'		   => 'admin',
                            'data[id]'     => $newPrincipal->getID()
            );
            $msg = array(
                            getTranslation('name')  => $newPrincipal->getName(),
                            'ID'                    => $newPrincipal->getID()
            );
            displayAnswer(getTranslation('msg_user_created'), $msg, createAdminLink( _ADMIN_ID_USER_ADMIN ), $hidden, getTranslation('admin'), 'user_add.png');
	    }
} 
else
{
    createNewUserMask( (isset($_POST['userName']) ? $_POST['userName'] : '') );
}

// ---------------------------------------------------------
//     FUNCTIONS FOLLOW
// ---------------------------------------------------------

function createNewUserMask( $name ) 
{
	$smarty = getAdminSmarty();

	$lang_info = new LanguageEnumeration();
    
    // fetch Languages for drop down
    $langs = array();
	for ($i = 0; $i < $lang_info->count(); $i++)
	{
		$tl = $lang_info->next();
		$langs[$tl->getLanguageName()] = $tl->getLanguageID();
	}

    // Create User Group Drop Down
    $groupSelector = new GroupSelect();
    $groupSelector->setName("userGroups[]");
    $groupSelector->setIsMultiple();
    $groupSelector->setSize(3);
	
    $smarty->assign('EMAIL', '');
	$smarty->assign('USERNAME', (($name == null || strlen(trim($name)) == 0) ? '' : $name));
	$smarty->assign('CREATE_URL', createAdminLink($GLOBALS['MENU']->getID(), array('mode' => 'create')));
	$smarty->assign('LANGUAGES', $langs);
	$smarty->assign('GROUPS', $groupSelector->getHtml());
	$smarty->display('UserCreate.tpl');
	
	unset($smarty);
}

admin_footer();
