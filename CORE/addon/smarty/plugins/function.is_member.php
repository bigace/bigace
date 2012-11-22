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
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.smarty
 * @subpackage function
 */

/**
 * Checks if a user is member of a UserGroup.
 *
 * Parameter:
 * - name	(required) the Group Name
 * - id		(required) the Group ID
 * - assign	(required) name of the variable to assign values to
 * - user   (optional) the User to test, if not set uses the current user
 */
function smarty_function_is_member($params, &$smarty)
{
    if(!isset($params['name']) && !isset($params['id'])) {
		$smarty->trigger_error("is_member: missing 'name' or 'id' attribute");
		return;
	}
    if(!isset($params['assign'])) {
		$smarty->trigger_error("is_member: missing 'assign' attribute");
		return;
	}

    $user = $GLOBALS['_BIGACE']['SESSION']->getUser();
    if(isset($params['user'])) {
        $user = $params['user'];
	}

    import('classes.group.GroupService');
    $gs = new GroupService();
    $memberships = $gs->getMemberships($user);
    $isMember = false;

    /* @var $group Group */
    foreach($memberships as $group) {
        if(isset($params['name'])) {
            if(strcmp($group->getName(), $params['name']) === 0) {
                $isMember = true;
                break;
            }
        } else {
            if($group->getID() == $params['id']) {
                $isMember = true;
                break;
            }
        }
    }

	$smarty->assign($params['assign'], $isMember);
}
