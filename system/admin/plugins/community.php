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

define('PARAM_COMMUNITY_MODE',           's4lv09G4d');
define('PARAM_COMMUNITY_URL',            'o8F5hJ39y');
define('PARAM_COMMUNITY_MAINTENANCE',    'jhgf854ih');

define('PARAM_NEW_COMMUNITY_URL',        'kjhgzt87D');

define('MODE_DELETE_COMMUNITY',          'v1kDz4t8p');
define('MODE_ADD_COMMUNITY',             'j6dbng376');
define('MODE_DEFAULT_COMMUNITY',         'Pb74x1Whj');
require_once(_BIGACE_DIR_LIBS . 'sanitize.inc.php');

/**
 * Administrate your Consumer settings with this Plugin
 */
import('classes.consumer.ConsumerHelper');
import('classes.exception.WrongArgumentException');

$mode = extractVar(PARAM_COMMUNITY_MODE, '');
$showListing = true;
$consumerHelper = new ConsumerHelper();

if (defined('_BIGACE_DEMO_VERSION') && $mode != '')
{
    displayError(getTranslation('demo_version_disabled'));
}
else if ($mode == MODE_ADD_COMMUNITY)
{
    $oldURL = extractVar(PARAM_COMMUNITY_URL, '');
    $newURL = extractVar(PARAM_NEW_COMMUNITY_URL, '');
    
    if ($newURL == '' || $oldURL == '') 
    {
        if ($oldURL == '') 
            displayError( getTranslation('error_create_param') );
        if ($newURL == '') 
            displayError( getTranslation('error_add_url') );
    } 
    else
    {
        // make sure to work with an existing url
        if($consumerHelper->getConsumerIdForDomain($oldURL) < 0)
        {
            displayError( getTranslation('error_alias_community_missing') . ': ' . $oldURL );
        }
        else
        {
            if ($consumerHelper->getConsumerIdForDomain($newURL) >= 0) {
                displayError( getTranslation('error_alias_exists') . ': ' . $newURL );
            } else {
            	$newURL = sanitize_plain_text($newURL);
                $consumerHelper->duplicateConsumerValues($oldURL, $newURL);
            }
        }
            
    }
}
else if ($mode == MODE_DELETE_COMMUNITY) 
{
    $domainToDelete = extractVar(PARAM_COMMUNITY_URL, '');
    if($domainToDelete != '') {
        if($consumerHelper->getConsumerIdForDomain($domainToDelete) >= 0) {
            $consumerHelper->removeConsumerByDomain($domainToDelete);
        } else {
            displayError( getTranslation('error_delete_community_missing') . ': ' . $domainToDelete );
        }
    }
}
else if ($mode == MODE_DEFAULT_COMMUNITY) 
{
    $newDefaultDomain = extractVar(PARAM_COMMUNITY_URL, '');
    if($newDefaultDomain != '') {
        $tempId = $consumerHelper->getConsumerIdForDomain($newDefaultDomain);
        if($tempId >= 0) {
            $defaultCommunity = $consumerHelper->getDefaultConsumer();
            if ($defaultCommunity != null && $defaultCommunity->getID() == $tempId) {
                displayMessage( getTranslation('message_remove_default') );
                $consumerHelper->removeDefaultConsumer();
            } else {
                displayMessage( getTranslation('message_set_default') . ': ' . $newDefaultDomain);
                $consumerHelper->setDefaultConsumer($newDefaultDomain);
            }
        } else {
            displayError( getTranslation('error_default_community_missing') . ': ' . $domainToDelete );
        }
    } else {
        displayError('Could not set empty URL');
    }
}



// ------------- START OUPUT -----------------

$tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("CommunityListing.tpl.htm", false, true);

$allConsumer = $consumerHelper->getAllConsumer();
$defaultCommunity = $consumerHelper->getDefaultConsumer();
$css = "row1";

foreach($allConsumer AS $consumer) 
{
    $isDefault = false;
    $tpl->setVariable("CSS", $css);
    $tpl->setVariable("COMMUNITY_DEFAULT", ($defaultCommunity != null && $defaultCommunity->getID() == $consumer->getID() ? "active": "inactive"));
    $tpl->setVariable("COMMUNITY_URL", $consumer->getDomainName());
    $tpl->setVariable("COMMUNITY_ID", $consumer->getID());
    $tpl->setVariable("COMMUNITY_STATUS", ($consumer->isActivated() ? "active": "inactive"));
    $tpl->setVariable("PARAM_DUPLICATE_URL", PARAM_COMMUNITY_URL);
    $tpl->setVariable("PARAM_DEFAULT_URL", PARAM_COMMUNITY_URL);
    $tpl->setVariable("PARAM_COMMUNITY_MODE", PARAM_COMMUNITY_MODE);
    $tpl->setVariable("ADD_MODE", MODE_ADD_COMMUNITY);
    $tpl->setVariable("DEFAULT_MODE", MODE_DEFAULT_COMMUNITY);
    $tpl->setVariable("PARAM_NEW_URL", PARAM_NEW_COMMUNITY_URL);

    $allAlias = $consumer->getAlias();
    if(count($allAlias) > 0)
    {
        $tpl->setCurrentBlock("alias");
        foreach($allAlias AS $alias)
        {
            if ($alias != DEFAULT_COMMUNITY) 
            {
                if ($alias != $consumer->getDomainName())
                {
                    $tpl->setVariable("ALIAS_URL", $alias);
                    $tpl->setVariable("DELETE_URL", createAdminLink($GLOBALS['MENU']->getID(), array(PARAM_COMMUNITY_MODE => MODE_DELETE_COMMUNITY, PARAM_COMMUNITY_URL => $alias)));
                    $tpl->parseCurrentBlock();
                }
            } 
            else 
            {
                $isDefault = true;
            }
        }
    }

    $tpl->parse("community");
	$css = ($css == "row1" ? "row2" : "row1");    
}

$tpl->show();

unset($consumerHelper);
unset($showListing);
unset($mode);
unset($tpl);

admin_footer();

?>