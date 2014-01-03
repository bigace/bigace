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

define('PARAM_COMMUNITY_STATE',          'zhtf5fikj');
define('PARAM_COMMUNITY_MODE',           's4lv09G4d');
define('PARAM_COMMUNITY_MAINTENANCE',    'jhgf854ih');

define('STATE_VALUE_ACTIVE',            'q39854ljh');
define('STATE_VALUE_DEACTIVE',          '64exfkbghp');

define('MODE_CHANGE_STATE',             'u5FN80Ky');

/**
 * Administrate your Consumer settings with this Plugin
 */
import('classes.consumer.ConsumerHelper');
import('classes.exception.WrongArgumentException');

$mode = extractVar(PARAM_COMMUNITY_MODE, '');
$consumerHelper = new ConsumerHelper();

$COMMUNITY = $GLOBALS['_BIGACE']['SESSION']->getCommunity();
$isActive = $COMMUNITY->isActivated();

if ($mode == MODE_CHANGE_STATE) 
{
    $state = extractVar(PARAM_COMMUNITY_STATE, $isActive);
    if ($state == STATE_VALUE_ACTIVE) {
        $consumerHelper->activateConsumer(_CID_);
        $isActive = true;
    } else if ($state == STATE_VALUE_DEACTIVE) {
        $consumerHelper->deactivateConsumer(_CID_);
        $isActive = false;
    }
    
    $maintenance = extractVar(PARAM_COMMUNITY_MAINTENANCE, '');
    $fpointer = fopen($COMMUNITY->getMaintenanceFilename(), "wb");
    fputs($fpointer, stripslashes($maintenance));
    //fputs($fpointer, $maintenance);
    fclose($fpointer);    
    unset($fpointer);
}

$tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("CommunityMaintenance.tpl.htm", false, true);

$tpl->setVariable("PARAM_MODE_MAINTENANCE", PARAM_COMMUNITY_MODE);
$tpl->setVariable("MAINTENANCE_MODE", MODE_CHANGE_STATE);
$tpl->setVariable("STATE_VALUE_ACTIVE", STATE_VALUE_ACTIVE);
$tpl->setVariable("STATE_VALUE_DEACTIVE", STATE_VALUE_DEACTIVE);
$tpl->setVariable("PARAM_STATUS", PARAM_COMMUNITY_STATE);
$tpl->setVariable("STATE_DEACTIVE", ($isActive ? '' : 'checked="checked"'));
$tpl->setVariable("STATE_ACTIVE", ($isActive ? 'checked="checked"' : ''));
$tpl->setVariable("PARAM_MAINTENANCE", PARAM_COMMUNITY_MAINTENANCE);
$tpl->setVariable("MAINTENANCE_TEXT", $COMMUNITY->getMaintenanceHTML());
$tpl->setVariable("MARKITUP_DIR", _BIGACE_DIR_ADDON_WEB.'markitup/');

$tpl->show();

unset($COMMUNITY);
unset($tpl);
unset($isActive);
unset($consumerHelper);
unset($showListing);
unset($mode);

admin_footer();

?>