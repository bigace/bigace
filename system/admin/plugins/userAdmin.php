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

/**
 * Administration of users.
 * If user has no global user-admin permissions, he will only see its own data.
 */

check_admin_login();
admin_header();

import('classes.util.html.FormularHelper');
import('classes.language.Language');
import('classes.language.LanguageEnumeration');
import('classes.group.GroupEnumeration');
import('classes.group.GroupService');
import('classes.group.GroupAdminService');
import('classes.group.Group');
import('classes.right.RightAdminService');
import('classes.exception.NoFunctionalRightException');
import('classes.core.ServiceFactory');
import('classes.util.formular.GroupSelect');

require_once(_BIGACE_DIR_LIBS . 'sanitize.inc.php');

define('USERADMIN_PARAM_GROUPLIST', 'listingGroupID');

define('USERADMIN_MODE_KILL_USER', 'killUser');
define('USERADMIN_MODE_DELETE_USER', 'deleteUser');
define('USERADMIN_MODE_UPDATE_USER', 'updateData');
define('USERADMIN_MODE_UPDATE_SETTINGS', 'updateSettings');
define('USERADMIN_MODE_UPDATE_PASSWORD', 'changePassword');

define('USERADMIN_MODE_SHOW_LIST', 'showList');
define('USERADMIN_MODE_REMOVE_GROUP', 'removeFromGroup');
define('USERADMIN_MODE_ADD_GROUP', 'addToGroup');

define('USERADMIN_INDEX_SETTINGS', 0);
define('USERADMIN_INDEX_DATA', 1);
define('USERADMIN_INDEX_PASSWORD', 2);
define('USERADMIN_INDEX_GROUP', 3);

$services = ServiceFactory::get();
$PRINCIPALS = $services->getPrincipalService();

// list of configured attributes for the user
$userAttributes = parse_ini_file(_BIGACE_DIR_CID.'config/user_attributes.ini', TRUE);
$attributeList = $userAttributes['attributes'];

// ID of the group that is preselected and its members are shown
$groupListing = extractVar(USERADMIN_PARAM_GROUPLIST, null);
$mode = extractVar('mode', USERADMIN_MODE_SHOW_LIST);
$data = extractVar('data', array('id' => $GLOBALS['_BIGACE']['SESSION']->getUserID()));

// check if user is allowed to edit all user or only its own data
if(!isAllowedToEditUser()) {
    if($mode == USERADMIN_MODE_DELETE_USER) {
        ExceptionHandler::processAdminException( new NoFunctionalRightException('You are not allowed to perform this action!', createAdminLink($GLOBALS['MENU']->getID())) );
    }

    // kick user from all masks he is not allowed to see
    if($mode != USERADMIN_MODE_UPDATE_USER && $mode != USERADMIN_MODE_UPDATE_PASSWORD && $mode != USERADMIN_MODE_UPDATE_SETTINGS) {
        $mode = USERADMIN_MODE_EDIT_USER;
    }
}

echo '

<script language="JavaScript">
<!--
function comparePasswords(mform) {
    if(document.password.passwordnew.value == document.password.passwordcheck.value) {
      return true;
    } else{
        alert("'.getTranslation('msg_pwd_no_match').'");
        document.password.passwordcheck.value = "";
        document.password.passwordcheck.focus();
    }
    return false;
}
//-->
</script>
';

switch($mode)
{
    case USERADMIN_MODE_REMOVE_GROUP:
        if (isAllowedToEditUser() && isset($data['id']) && isset($data['group']) && $data['id'] != _AID_) {
            $services = ServiceFactory::get();
            $PRINCIPALS = $services->getPrincipalService();
            $principal = $PRINCIPALS->lookupByID($data['id']);
            if($principal != null) {
                $groupAdmin = new GroupAdminService();
                $groupAdmin->removeFromGroup($data['group'], $principal->getID());
                unset($groupAdmin);
                editUser($principal->getID(), USERADMIN_INDEX_GROUP);
            }
            else {
                showUserList();
            }
            unset($principal);
            unset($PRINCIPALS);
            unset($services);
        } else {
            displayError('missing_values');
            showUserList();
        }
        break;

    case USERADMIN_MODE_ADD_GROUP:
        if (isAllowedToEditUser() && isset($data['id']) && isset($data['group']) && $data['id'] != _AID_) {
            $services = ServiceFactory::get();
            $PRINCIPALS = $services->getPrincipalService();
            $principal = $PRINCIPALS->lookupByID($data['id']);
            if($principal != null) {
                $groupAdmin = new GroupAdminService();
                $groupAdmin->addToGroup($data['group'], $principal->getID());
                unset($groupAdmin);
                editUser($principal->getID(), USERADMIN_INDEX_GROUP);
            }
            else {
                showUserList();
            }
            unset($principal);
            unset($PRINCIPALS);
            unset($services);
        } else {
            displayError('missing_values');
            showUserList();
        }
        break;

    case USERADMIN_MODE_DELETE_USER:
        if (isset($data['id']) && isset($data['checkDelete'])) {
            askForDeleteConfirm($data['id']);
        } else {
            showUserList();
        }
        break;

    case USERADMIN_MODE_EDIT_USER:
        if (isAllowedToEditUser()) {
            editUser($data['id']);
        } else {
            editUser($GLOBALS['_BIGACE']['SESSION']->getUserID());
        }
        break;

    case USERADMIN_MODE_KILL_USER:
        if (isAllowedToEditUser() && isset($data['id']) && isset($data['checkKill']) && $data['id'] != _BIGACE_SUPER_ADMIN && $data['id'] != _AID_)
        {
            $p = $GLOBALS['PRINCIPALS']->lookupByID($data['id']);
            // deletes user from system.
            if ($p != null && $GLOBALS['PRINCIPALS']->deletePrincipal($p)) {
                displayMessage(getTranslation('msg_deleted_user'));
            } else {
                displayError(getTranslation('msg_not_deleted_user'));
            }
        }
        showUserList();
        break;

    case USERADMIN_MODE_UPDATE_PASSWORD:
        $data['new'] = extractVar('passwordnew', '');
        $data['check'] = extractVar('passwordcheck', '');

        if (isset($data['id']) && $data['new'] != '' && $data['check'] != '') {
            changeUsersPassword($data['id'], $data['new'], $data['check']);
        }
        else {
            displayError(getTranslation('missing_values'));
            showUserList();
        }
        break;

        case USERADMIN_MODE_UPDATE_USER:
            if ( isset($data['id']) ) {
                // if not has fright, user may only edit own profile
                if (isAllowedToEditUser() || ($data['id'] == $GLOBALS['_BIGACE']['SESSION']->getUserID())) {
                    $didChange = updateUserData($data);
                    if($didChange != null) {
                        if ($didChange) {
                            displayMessage(getTranslation('msg_changed_userdata'));
                        } else {
                            displayError(getTranslation('msg_not_changed_userdata'));
                        }
                    }
                    editUser($data['id'], USERADMIN_INDEX_DATA);
                }
                else
                {
                    // will only be entered if user is not allowed to admin and has changed the url
                    displayError(getTranslation('msg_not_changed_userdata'));
                    editUser($GLOBALS['_BIGACE']['SESSION']->getUserID(), USERADMIN_INDEX_DATA);
                }
            }
            else {
                showUserList();
            }
            break;

    case USERADMIN_MODE_UPDATE_SETTINGS:
        if (isset ($data['id']))
        {
            $mayChange = isAllowedToEditUser();
            if (!$mayChange)
            {
                $mayChange = ($data['id'] == $GLOBALS['_BIGACE']['SESSION']->getUserID());
            }

            if ($mayChange)
            {
                $lang = $data['lang'];
                $email = $data['email'];
                
                $p = $GLOBALS['PRINCIPALS']->lookupByID($data['id']);
                if ($p != null) {
                    $failure = false;
                    $did = false;

                    if($p->getLanguageID() != $lang) {
                        $did = true;
                        if(!$GLOBALS['PRINCIPALS']->setParameter($p,PRINCIPAL_PARAMETER_LANGUAGE, $lang))
                            $failure = true;
                    }

                    if($p->getEmail() != $email) {
                        $did = true;
                        if(!$GLOBALS['PRINCIPALS']->setParameter($p,PRINCIPAL_PARAMETER_EMAIL, $email))
                            $failure = true;
                    }
                    
                    if(isAllowedToEditUser()) {
                        $active = (isset($data['active']) && $data['active'] == 0) ? false : true;
                        if($p->isActive() != $active) {
                            $did = true;
                            if(!$GLOBALS['PRINCIPALS']->setParameter($p,PRINCIPAL_PARAMETER_ACTIVE, $active))
                                $failure = true;
                        }
                    }

                    if($did) {
                        if ($failure)  {
                            displayError(getTranslation('msg_not_changed_usersetting'));
                        } else {
                            displayMessage(getTranslation('msg_changed_usersetting'));
                        }
                    }
                    editUser($data['id'], USERADMIN_INDEX_SETTINGS);
                }
                else
                {
                    displayError(getTranslation('msg_not_changed_usersetting','Changing User settings failed!'));
                    showUserList();
                }
            }
            else {
                showUserList();
            }
        }
        else {
            showUserList();
        }
        break;

    default:
        showUserList($groupListing);
        break;
}



// ---------------------------------------------------------
//     FUNCTIONS FOLLOW
// ---------------------------------------------------------



function changeUsersPassword($userID, $password, $passwordCheck)
{
    $mayChange = isAllowedToEditUser();
    if (!$mayChange) {
        $mayChange = ($userID == $GLOBALS['_BIGACE']['SESSION']->getUserID());
    }

    if ($mayChange)
    {
        if(defined('_BIGACE_DEMO_VERSION') && $userID == _BIGACE_SUPER_ADMIN)
        {
            displayError(getTranslation('demo_version_disabled'));
        }
        else
        {
	        if ($password != '' && $passwordCheck != '') {
	            if ( $passwordCheck == $password ) {
	                $p = $GLOBALS['PRINCIPALS']->lookupByID($userID);
	                if ($p != null && $GLOBALS['PRINCIPALS']->setParameter($p,PRINCIPAL_PARAMETER_PASSWORD, $password)) {
	                    displayMessage(getTranslation('msg_pwd_changed'));
	                } else {
	                    displayError(getTranslation('msg_pwd_not_changed'));
	                }
	            } else {
	                displayError(getTranslation('msg_pwd_no_match'));
	            }
	        } else {
	            displayError(getTranslation('missing_values'));
	        }
        }
        editUser($userID, USERADMIN_INDEX_PASSWORD);
    }
    else
    {
        showUserList();
    }
}

/**
 * Show all User values that can be edited.
 */
function editUser($uid, $index = null)
{
    if($index == null) {
        $index = USERADMIN_INDEX_SETTINGS;
    }

    if (!isAllowedToEditUser()) {
        $principal = $GLOBALS['_BIGACE']['SESSION']->getUser();
    } else {
        if($uid == _BIGACE_SUPER_ADMIN && $GLOBALS['_BIGACE']['SESSION']->getUserID() != _BIGACE_SUPER_ADMIN)
            $principal = $GLOBALS['_BIGACE']['SESSION']->getUser();
        else
            $principal = $GLOBALS['PRINCIPALS']->lookupByID($uid);
    }

    $tpl = getAdminSmarty();
    $tpl->assign('SELECTED_INDEX', $index);
    if (!isAllowedToEditUser())
        $tpl->assign('BACK_LINK', '');
    else
        $tpl->assign('BACK_LINK', createBackLink($GLOBALS['MENU']->getID(), array()));
    $tpl->assign('USER_SETTINGS_FORM', createUserSettingsForm($principal));
    $tpl->assign('USER_DATA_FORM', createUserDataForm($principal, USERADMIN_MODE_UPDATE_USER));

    $ugf = "";
    $upf = "";
    $udf = "";
    
    if($principal->getID() != _AID_)
    {
        $ugf = createUserGroupForm($principal);
        $upf = createPasswordForm($principal, USERADMIN_MODE_UPDATE_PASSWORD);
        if($principal->getID() != _BIGACE_SUPER_ADMIN && $principal->getID() != $GLOBALS['_BIGACE']['SESSION']->getUserID()) {
            $udf = createDeleteForm($principal);
        }
    }
    
    $tpl->assign('USER_GROUP_FORM', $ugf);
    $tpl->assign('USER_PASSWORD_FORM', $upf);
    $tpl->assign('USER_DELETE_FORM', $udf);

    $tpl->display('UserSettings.tpl');    
}

function createUserGroupForm($principal)
{
    $gs = new GroupService();
    $memberships = $gs->getMemberships($principal);

    $templateName = 'AdminUserGroups.tpl.htm';
    if(!isAllowedToEditUser()) {
        $templateName = 'ViewUserGroups.tpl.htm';
    }

    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile($templateName, true, true);
    $cssClass = "row1";

    $gs = new GroupSelect();
    $gs->setName('data[group]');

    foreach($memberships AS $membership)
    {
        $gs->addGroupIDToHide($membership->getID());
        $tpl->setCurrentBlock('row');
        $tpl->setVariable("CSS", $cssClass) ;
        $tpl->setVariable("GROUP_NAME", $membership->getName());
        $tpl->setVariable("REMOVE_LINK", '<a href="'.createAdminLink($GLOBALS['MENU']->getID(), array('mode' => USERADMIN_MODE_REMOVE_GROUP, 'data[id]' => $principal->getID(), 'data[group]' => $membership->getID())).'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'user_group_delete.png" border="0"></a>');
        $tpl->parseCurrentBlock();

        $cssClass = ($cssClass == "row1") ? "row2" : "row1";
    }

    if(count($memberships) == 0)
        $tpl->touchBlock('noGroupMember');

    $gs->generate();

    if(count($gs->getOptions()) > 0) {
        $tpl->setCurrentBlock('addToGroupForm');
        $tpl->setVariable('GROUP_SELECT', $gs->getHtml());
        $tpl->setVariable('ADD_TO_GROUP_LINK', createAdminLink($GLOBALS['MENU']->getID(), array('mode' => USERADMIN_MODE_ADD_GROUP)));
        $tpl->setVariable('PARAM_NAME_USER', 'data[id]');
        $tpl->setVariable('USER_ID', $principal->getID());
        $tpl->parseCurrentBlock();
    }

    return $tpl->get();
}


/**
 * Creates the User List.
 */
function showUserList($groupID = null)
{
    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("ChooseUserGroup.tpl.htm", false, true);
    $tpl->setVariable('GROUP_SELECT', getGroupSelector($groupID));
    $tpl->setVariable('ACTION_CHOOSE_GROUP', createAdminLink($GLOBALS['MENU']->getID()));
    $tpl->show();
    unset($tpl);

    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("AdminUserList.tpl.htm", false, true);

    $cssClass = "row1";

    if($groupID == null || strlen(trim($groupID)) == 0 || ((int)$groupID) != $groupID) {
        $USER_INFO = $GLOBALS['PRINCIPALS']->getAllPrincipals();
    }
    else {
        $groupService = new GroupService();
        $USER_INFO = $groupService->getGroupMember($groupID);
    }


    if(count($USER_INFO) == 0)
    {
        displayMessage('No Group members found');
    }
    else
    {
        for ($i=0; $i < count($USER_INFO); $i++)
        {
            $temp = $USER_INFO[$i];

              $cssClass = ($cssClass == "row1") ? "row2" : "row1";

            $rowType = "activerow";

            if (!$temp->isActive()) {
            $rowType = "deactiverow";
            }

            $userName = sanitize_plain_text($temp->getName());

            $lang = new Language($temp->getLanguageID());

            $tpl->setCurrentBlock($rowType) ;
            $tpl->setVariable("ACTION_EDIT_USER", createAdminLink($GLOBALS['MENU']->getID(), array('mode' => USERADMIN_MODE_EDIT_USER, 'data[id]' => $temp->getID()))) ;
            $tpl->setVariable("USER_EMAIL", $temp->getEmail()) ;
            $tpl->setVariable("USER_ID", $temp->getID()) ;

            $tpl->setVariable("CSS", $cssClass) ;
            $tpl->setVariable("LANG_LOCALE", $lang->getLocale()) ;
            $tpl->setVariable("LANG_NAME", $lang->getName()) ;
            $tpl->setVariable("USER_NAME", $userName) ;
            $tpl->parseCurrentBlock() ;

            $tpl->parse("outerrow");
        }
        $tpl->show();
    }
}


function askForDeleteConfirm( $uid )
{
    $temp = $GLOBALS['PRINCIPALS']->lookupByID($uid);
    $userName = sanitize_plain_text($temp->getName());

    $config = array(
                'width'         =>  ADMIN_MASK_WIDTH_SMALL,
                'size'          =>  array('left' => '30%'),
                'align'         =>  array (
                                        'table'     =>  'left',
                                        'left'      =>  'left',
                                        'title'     =>  'center'
                                    ),
                'image'         =>  $GLOBALS['_BIGACE']['style']['DIR'].'user_delete.png',
                'title'         =>  getTranslation('user_delete_confirm'),
                'form_action'   =>  createAdminLink($GLOBALS['MENU']->getID()),
                'form_method'   =>  'post',
                'form_hidden'   =>  array(
                                        'mode'      => USERADMIN_MODE_KILL_USER,
                                        'data[id]'  => $uid,
                                        'data[checkKill]' => $uid
                                ),
                'entries'       =>  array ($userName => 'empty') ,
                'form_submit'   =>  true,
                'submit_label'  =>  getTranslation('delete'),
                'form_reset'    =>  "location.href='".createAdminLink($GLOBALS['MENU']->getID(), array('mode'=>USERADMIN_MODE_EDIT_USER, 'data[id]' => $uid))."'",
                'reset_label'   =>  getTranslation('back','Back')
    );
    echo createTable($config);
}

/**
* Edit the User data.
* Including the language, the active state and the system name
*/
function createUserSettingsForm($temp)
{
    $entries            = array();
    $hidden             = array();
    $hidden['mode']     = USERADMIN_MODE_UPDATE_SETTINGS;
    $hidden['data[id]'] = $temp->getID();

    $userName = sanitize_plain_text($temp->getName());

    $entries[getTranslation('name')] = $userName;//$temp->getName();//createTextInputType('name', $temp->getName(), 30);
    $entries[getTranslation('email')] = createTextInputType('email', $temp->getEmail(), 99);
    
    $lang_info = new LanguageEnumeration();

    $sel = '<select name=data[lang]>';
    for ($i = 0; $i < $lang_info->count(); $i++)
    {
        $tl = $lang_info->next();
        $sel .= '<option value="'.$tl->getLanguageID().'"';
        if ($tl->getLanguageID() == $temp->getLanguageID()) {
            $sel .= ' selected';
        }
        $sel .= '>'.$tl->getLanguageName().'</option>'."\n";
    }
    $sel .= '</select>';

    $entries[getTranslation('language')]  = $sel;

    if (isAllowedToEditUser())
    {
/*
        $group_info = new GroupEnumeration();
        $groups = array();
        for ($i = 0; $i < $group_info->count(); $i++)
        {
            $temp_group = $group_info->next();
            $groups[$temp_group->getName()] = $temp_group->getID();
        }
        $entries[getTranslation('group')] = createSelectBox('group_id', $groups, '');
*/
        $entries[getTranslation('admin_user_active')] = '<img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'user_active.png">' . createRadioButton('active', '1', $temp->isActive()) . ' ' . getTranslation('user_active') . '<br>'
                                           . '<img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'user_inactive.png">' . createRadioButton('active', '0', !$temp->isActive()) . ' ' . getTranslation('user_inactive');
    }
    else
    {
        // user edits own profile, do not allow to edit group or state
        // must not be set via hidden fields, cause the user admin service only saves them when submitted

        //$hidden['data[group_id]'] = $group->getID();
        //$hidden['data[active]']   = ($temp->isActive() ? '1' : '0');
    }



    $config = array(
                'width'         =>  ADMIN_MASK_WIDTH_SMALL,
                'size'          =>  array('left' => '200px'),
                'align'         =>  array (
                                        'table'     =>  'left',
                                        'left'      =>  'left',
                                        'title'     =>  'center'
                                    ),
                'image'         =>  $GLOBALS['_BIGACE']['style']['DIR'].'user.png',
                'title'         =>  getTranslation('edit_userdata_short'),
                'form_action'   =>  createAdminLink($GLOBALS['MENU']->getID()),
                'form_method'   =>  'post',
                'form_hidden'   =>  $hidden,
                'entries'       =>  $entries,
                'form_submit'   =>  true,
                'submit_label'  =>  getTranslation('save')
    );
    return createTable($config);
}

/**
 * @return null if nothing changed, false if change failed or was not allowed, true if everything worked fine
 */
function updateUserData($data)
{
    if(isset($data['id']))
    {
        if(isAllowedToEditUser() || $data['id'] = $GLOBALS['_BIGACE']['SESSION']->getUserID())
        {
            $p = $GLOBALS['PRINCIPALS']->lookupByID($data['id']);
            $attributes = $GLOBALS['PRINCIPALS']->getAttributes($p);
            $worked = null;
            foreach($GLOBALS['attributeList'] AS $att => $name)
            {
                if(isset($data[$att])){
                    if(!isset($attributes[$att]) || $attributes[$att] != $data[$att]) {
                        $worked = true;
                        if(!$GLOBALS['PRINCIPALS']->setAttribute($p, $att, $data[$att])){
                            $worked = false;
                        }
                    }
                }
            }
            return $worked;
        }
    }
    return false;
}


function createUserDataForm($p, $mode)
{
    $attributes = $GLOBALS['PRINCIPALS']->getAttributes($p);

    $entries = array();
    foreach($GLOBALS['attributeList'] AS $att => $name)
    {
        $value = '';
        if(isset($attributes[$att])){
            $value = $attributes[$att];
        }
        $title = getTranslation($att, $name . '(!)');
        $entries[$title] = createTextInputType($att, $value, 50);
    }

    $config = array(
                'size'          =>  array('left' => '200px'),
                'image'         =>  $GLOBALS['_BIGACE']['style']['DIR'].'userdata.png',
                'title'         =>  getTranslation('edit_userdata'),
                'form_action'   =>  createAdminLink($GLOBALS['MENU']->getID()),
                'form_method'   =>  'post',
                'form_hidden'   =>  array(
                                        'mode'      => $mode,
                                        'data[id]'  => $p->getID()
                                ),
                'entries'       =>  $entries,
                'form_submit'   =>  true,
                'submit_label'  =>  getTranslation('save')
    );
    return createTable($config);
}

/**
 * Displays the Formular to change User Password
 */
function createPasswordForm($principal, $mode)
{
    $config = array(
                'size'          =>  array('left' => '200px'),
                'title'         =>  getTranslation('edit_password'),
                'image'         =>  $GLOBALS['_BIGACE']['style']['DIR'].'password.png',
                'form_action'   =>  createAdminLink($GLOBALS['MENU']->getID()),
                'form_method'   =>  'post',
                'form_hidden'   =>  array(
                                        'mode'      => $mode,
                                        'data[id]'  => $principal->getID()
                                ),
                'entries'       =>  array(
                                        getTranslation('new_password')      =>   '<input type="password" name="passwordnew" maxlength="30" size="35" value="">',
                                        getTranslation('rewrite_password')  =>   '<input type="password" name="passwordcheck" maxlength="30" size="35" value="">'
                                ),
                'form_name'     =>  'password',
                'form_onsubmit' =>  'return comparePasswords(this)',
                'form_submit'   =>  true,
                'submit_label'  =>  getTranslation('save')
    );
    return createTable($config);
}

function createDeleteForm($principal)
{
    $config = array(
            'size'          =>  array('left' => '200px'),
            'image'         =>  $GLOBALS['_BIGACE']['style']['DIR'].'user_delete.png',
            'title'         =>  getTranslation('delete_user'),
            'form_action'   =>  createAdminLink($GLOBALS['MENU']->getID()),
            'form_method'   =>  'post',
            'form_hidden'   =>  array(
                                    'mode'          => USERADMIN_MODE_DELETE_USER,
                                    'data[id]'      => $principal->getID()
                            ),
            'entries'       =>  array(
                                    createCheckBox('checkDelete', 'confirmDelete', false) . '&nbsp;' . getTranslation('delete_user_info') => 'empty'
                            ),
            'form_name'     =>  'deleteUserForm',
            'form_submit'   =>  true,
            'submit_label'  =>  getTranslation('delete')
    );
    return createTable($config);
}

function getGroupSelector($preSelect = null)
{
    $gs = new GroupSelect();
    $gs->setName(USERADMIN_PARAM_GROUPLIST);
    $gs->setPreSelectedID($preSelect);
    $gs->setOnChange('this.form.submit();');
    $opt = new Option();
    $opt->setText(getTranslation('group_all'));
    $opt->setValue(' ');
    $gs->addOption($opt);
    return $gs->getHtml();
}

/**
 * Checks if the current User is allowed to edit all User profiles.
 */
function isAllowedToEditUser() {
    return $GLOBALS['FRIGHT_SERVICE']->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'admin_users');
}

admin_footer();
