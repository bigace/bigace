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
 * Plugin for administration of User Groups.
 */

check_admin_login();
admin_header();

import('classes.util.html.FormularHelper');
import('classes.group.Group');
import('classes.group.GroupService');
import('classes.group.GroupEnumeration');
import('classes.group.GroupAdminService');
import('classes.right.RightAdminService');

define('UG_PARAM_MODE', 'sdewrt43trw');
define('UG_PARAM_GROUP_ID', 'jv5D9lgC5');
define('UG_PARAM_GROUP_NAME', 'groupName');
define('UG_PARAM_USER_ID', 'fH68nd5aPV7Hvb');

define('UG_MODE_INDEX', 'jh65djh0');    // display the group listing
define('UG_MODE_DISPLAY_CREATE_FORM', 'uzxjhzg65p9Gok');   // shows the screen to add data for the new user group
define('UG_MODE_CREATE_GROUP', 'izd4Hx09Lmvc');   // creates the new user group
define('UG_MODE_ADMIN_GROUP', 'hgfd7J94cxW');   // show group members
define('UG_MODE_REMOVE_FROM_GROUP', 'asgdfizu7bb65d4r');
define('UG_MODE_ADD_TO_GROUP', '7fk4ypmMwq5sW');
define('UG_MODE_DELETE_GROUP', 'aser567890olmnbhgtr54');
require_once(_BIGACE_DIR_LIBS.'sanitize.inc.php');

$mode = extractVar(UG_PARAM_MODE,UG_MODE_INDEX);

switch($mode)
{
    case UG_MODE_DISPLAY_CREATE_FORM:
        $groupName = extractVar(UG_PARAM_GROUP_NAME, "");
        $groupName = sanitize_plain_text($groupName);
        showCreateScreen($groupName);
        unset($groupName);
        break;

    case UG_MODE_ADMIN_GROUP:
        $groupID = extractVar(UG_PARAM_GROUP_ID, null);
        if($groupID != null) {
            showGroupMember($groupID);
        } else {
            showIndexScreen();
        }
        unset($groupID);
        break;

    case UG_MODE_DELETE_GROUP:
        $groupID = extractVar(UG_PARAM_GROUP_ID, null);
        if($groupID != null && check_csrf_token()) 
        {
	    	// delete all memberships
	    	$gs = new GroupService();
	    	$allMember = $gs->getGroupMember($groupID);
            $gas = new GroupAdminService();
	    	foreach($allMember AS $m) {
	    		$gas->removeFromGroup($groupID,$m->getID());
	    	}
	
	    	// delete all permissions
	    	$ras = new RightAdminService(_BIGACE_ITEM_MENU);
	    	$ras->deleteAllGroupRight($groupID);
	    	
	    	// finally remove group
            $ga = new GroupAdminService();
            $id = $ga->deleteGroup($groupID);
            
            $GLOBALS['LOGGER']->logAudit("Deleted Group ".$groupID." including permissions and memberships");
        }    	
        showIndexScreen();
        break;
        
    case UG_MODE_CREATE_GROUP:
        $groupName = extractVar(UG_PARAM_GROUP_NAME, null);
        $groupName = sanitize_plain_text($groupName);
        
        if($groupName == null || strlen(trim($groupName)) == 0) {
            showCreateScreen($groupName, getTranslation('info_name_not_empty'));
        } else {
            $nameExists = false;
            $groupEnum = new GroupEnumeration();
            for($i=0; $i < $groupEnum->count(); $i++)
            {
                $t = $groupEnum->next();
                if(strcasecmp($t->getName(),$groupName) == 0) {
                    $nameExists = true;
                    break;
                }
            }
            if($nameExists) {
                showCreateScreen($groupName, getTranslation('info_name_exist'));
            } else {
                $ga = new GroupAdminService();
                $id = $ga->createGroup($groupName);
                showIndexScreen();
            }
        }
        unset($groupID);
        break;

    case UG_MODE_REMOVE_FROM_GROUP:
        $userID = extractVar(UG_PARAM_USER_ID, null);
        $groupID = extractVar(UG_PARAM_GROUP_ID, null);
        if($userID != null && $groupID != null) {
            if($userID != _AID_ && $userID != _BIGACE_SUPER_ADMIN) {
                $services = ServiceFactory::get();
                $PRINCIPALS = $services->getPrincipalService();
                $principal = $PRINCIPALS->lookupByID($userID);
                if($principal != null) {
                    $groupAdmin = new GroupAdminService();
                    $groupAdmin->removeFromGroup($groupID, $principal->getID());
                }
                unset($principal);
                unset($PRINCIPALS);
                unset($services);
            } else {
                displayError( getTranslation('not_allowed') );
            }
            showGroupMember($groupID);

        } else {
            displayError( getTranslation('wrong_parameter') );
            showIndexScreen();
        }
        unset($userID);
        break;

    case UG_MODE_ADD_TO_GROUP:
        $userID = extractVar(UG_PARAM_USER_ID, null);
        $groupID = extractVar(UG_PARAM_GROUP_ID, null);
        if($userID != null && $groupID != null) {
            if($userID != _AID_ && $userID != _BIGACE_SUPER_ADMIN) {
                $services = ServiceFactory::get();
                $PRINCIPALS = $services->getPrincipalService();
                $principal = $PRINCIPALS->lookup($userID);
                if($principal != null) {
                    $groupAdmin = new GroupAdminService();
                    $groupAdmin->addToGroup($groupID, $principal->getID());
                }
                unset($principal);
                unset($PRINCIPALS);
                unset($services);
            } else {
                displayError( getTranslation('not_allowed') );
            }
            showGroupMember($groupID);

        } else {
            displayError( getTranslation('wrong_parameter') );
            showIndexScreen();
        }
        unset($userID);
        break;

    default:
        showIndexScreen();
        break;
}

// ---------------------------------------------------------------
// ---------------------------------------------------------------

function showCreateScreen($groupName, $errorMsg = null)
{
	echo createBackLink($GLOBALS['MENU']->getID());

	if($errorMsg != null)
	    displayError($errorMsg);

    echo '<h2>'.getTranslation('add_group').'</h2>
          <form action="'.createAdminLink($GLOBALS['MENU']->getID()).'" method="post">
          <input type="hidden" name="'.UG_PARAM_MODE.'" value="'.UG_MODE_CREATE_GROUP.'" />
          <div><b>'.getTranslation('name').':</b> ' .
          createNamedTextInputType(UG_PARAM_GROUP_NAME,$groupName,'255') . 
          ' <button type="submit">'.getTranslation('create').'</button>
          <br/><i>'.getTranslation('info_new_group').'</i></div>
          </form>';
}

/**
 * This creates the HTML Output of the Index Screen and
 * directly sends it to the Client.
 */
function showIndexScreen()
{
    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("UserGroupIndex.tpl.htm", false, true);
    $tpl->setVariable("CREATE_GROUP_URL", createAdminLink($GLOBALS['MENU']->getID(), array(UG_PARAM_MODE => UG_MODE_DISPLAY_CREATE_FORM)));

    $ENUM = new GroupEnumeration();
    $SERVICE = new GroupService();

    $cssClass = "row1";

    for($i=0; $i < $ENUM->count(); $i++)
    {
        $temp = $ENUM->next();
        $members = $SERVICE->getMemberIDs($temp->getID());

        $tpl->setCurrentBlock("row");
        $tpl->setVariable("CSS", $cssClass);
        $tpl->setVariable("ID", $temp->getID());
        $tpl->setVariable("NAME", $temp->getName());
        $tpl->setVariable("MEMBERS", count($members));
        $tpl->setVariable("ADMIN_LINK", createAdminLink($GLOBALS['MENU']->getID(), array(UG_PARAM_GROUP_ID => $temp->getID(), UG_PARAM_MODE => UG_MODE_ADMIN_GROUP)));
        $tpl->setVariable("DELETE_LINK", createAdminLink($GLOBALS['MENU']->getID(), get_csrf_token(array(UG_PARAM_GROUP_ID => $temp->getID(), UG_PARAM_MODE => UG_MODE_DELETE_GROUP))));
        $tpl->parseCurrentBlock("row");

        $cssClass = ($cssClass == "row1") ? "row2" : "row1";
    }

    $tpl->show();
}


/**
 * This creates the HTML Output of the GroupMember Screen and directly sends it to the Client.
 */
function showGroupMember($groupID)
{
    $services = ServiceFactory::get();
    $PRINCIPALS = $services->getPrincipalService();

    $GROUP = new Group($groupID);
    $SERVICE = new GroupService();

    $members = $SERVICE->getMemberIDs($GROUP->getID());

    // allow to get back to index screen
    echo createBackLink($GLOBALS['MENU']->getID());

    $allPrincipalForSelect = array();
    $allUser = $PRINCIPALS->getAllPrincipals();
    foreach($allUser AS $user) {
        if($user->getID() != _AID_) {
            if(!in_array($user->getID(), $members))
                $allPrincipalForSelect[$user->getName()] = $user->getName();
        }
    }

    echo '<h2>'.getTranslation('group_name').': '.$GROUP->getName().' ('.$GROUP->getID().')</h2>';
    //<h2>{TRANSLATION_group_name}: {GROUP_NAME} ({GROUP_ID})</h2>

    if(count($allPrincipalForSelect) > 0)
    {
        $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("UserGroupMemberHeader.tpl.htm", false, true);
        $tpl->setVariable("GROUP_ID", $GROUP->getID());
        $tpl->setVariable("GROUP_NAME", $GROUP->getName());
        $tpl->setVariable("USER_SELECT", createNamedSelectBox(UG_PARAM_USER_ID, $allPrincipalForSelect));
        $tpl->setVariable("ADD_TO_GROUP_LINK", createAdminLink($GLOBALS['MENU']->getID(), array(UG_PARAM_GROUP_ID => $GROUP->getID(), UG_PARAM_MODE => UG_MODE_ADD_TO_GROUP)));
        $tpl->setVariable("PARAM_GROUP_ID", UG_PARAM_GROUP_ID);
        $tpl->show();
    }

    $cssClass = "row1";
    if(count($members) > 0)
    {
        $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("UserGroupMember.tpl.htm", false, true);

        $tpl->setVariable("GROUP_ID", $GROUP->getID());
        $tpl->setVariable("GROUP_NAME", $GROUP->getName());
        
        $allowEditUser = check_admin_permission(array("edit_own_profile","admin_users"));
        
        foreach($members AS $memberID)
        {
            $principal = $PRINCIPALS->lookupByID($memberID);

            
            $tpl->setCurrentBlock("row");
            $tpl->setVariable("CSS", $cssClass);
            $tpl->setVariable("ID", $principal->getID());
            if($allowEditUser) {
            	$editUserLink = createAdminLink(_ADMIN_ID_USER_ADMIN, array('mode' => 'admin', 'data[id]' => $principal->getID()));
            	$tpl->setVariable("NAME", '<a class="useredit" href="'.$editUserLink.'">' . $principal->getName() .'</a>');
            }
            else {
            	$tpl->setVariable("NAME", $principal->getName());
            }
            
            if($principal->getID() != _BIGACE_SUPER_ADMIN && $principal->getID() != _AID_) {
                $link = createAdminLink($GLOBALS['MENU']->getID(), array(UG_PARAM_GROUP_ID => $GROUP->getID(), UG_PARAM_USER_ID => $principal->getID(), UG_PARAM_MODE => UG_MODE_REMOVE_FROM_GROUP));
                $tpl->setVariable("ADMIN_LINK", '<a href="'.$link.'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'user_group_delete.png" border="0"></a>');
            } else {
                $tpl->setVariable("ADMIN_LINK", '<img alt="'.getTranslation('not_allowed').'" title="'.getTranslation('not_allowed').'" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'access_denied.png" border="0">');
            }
            $tpl->parseCurrentBlock("row");

            $cssClass = ($cssClass == "row1") ? "row2" : "row1";
        }
        $tpl->show();
    }
    else
    {
        displayMessage( getTranslation('no_group_member') );
    }

}

admin_footer();
