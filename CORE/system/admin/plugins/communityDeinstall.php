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

import('classes.consumer.ConsumerDeinstaller');
import('classes.consumer.ConsumerHelper');

define('PARAM_MODE', 'lkj67dlkOd3V');
define('PARAM_CONSUMER', 'dfgpo9uvzre66');
define('MODE_REQUEST_CONFIRMATION', 'kf6Z3c5A09ij');
define('MODE_DELETE_CONSUMER', 'ikd6V9Zw3aWFn');
define('MODE_SHOW_CONSUMER_LIST', 'default');

$consumerHelper = new ConsumerHelper();
$defaultConsumer = $consumerHelper->getDefaultConsumer();
$allConsumer = $consumerHelper->getAllConsumer();

// check if there is more than one community installed
$i = 0;
foreach($allConsumer AS $cons) {
    if(isAllowedDomain($defaultConsumer, $cons)) {
        $i++;
    }
}

if($i == 0 || count($allConsumer) < 2) {
    displayMessage(getTranslation('error_last_consumer'));
    return;
}

switch ( extractVar(PARAM_MODE) ) 
{
    case MODE_REQUEST_CONFIRMATION:
            $consumerID = extractVar(PARAM_CONSUMER, '-1');
            if($consumerID == '-1') {
                displayError( getTranslation('error_null_consumer') );
                displayChooseConsumerFormular();
            } else {
                $consumer = $consumerHelper->getConsumerByID($consumerID);
                
                if(!isAllowedDomain($defaultConsumer, $consumer)) {
                    displayError( getDomainError($defaultConsumer, $consumer) );
                    displayChooseConsumerFormular();
                } else {
                    displayConfirmFormular($consumerID);
                }
            }
            break;
    case MODE_DELETE_CONSUMER:
            $consumerID = extractVar(PARAM_CONSUMER, '-1');
            if($consumerID == '-1') {
                displayError( getTranslation('error_null_consumer') );
                displayChooseConsumerFormular();
            } else {
                if(defined('_BIGACE_DEMO_VERSION')) {
		            displayError(getTranslation('demo_version_disabled'));
		            displayChooseConsumerFormular();
		        }
				else {
	                $consumer = $consumerHelper->getConsumerByID($consumerID);
	                $defaultConsumer = $consumerHelper->getDefaultConsumer();
	                
	                if(!isAllowedDomain($defaultConsumer, $consumer)) {
	                    displayError( getDomainError($defaultConsumer, $consumer) );
	                    displayChooseConsumerFormular();
	                } else {
	                    deinstallConsumer($consumerID);
	                }
				}
            }
            break;
    default:
            displayChooseConsumerFormular();
            break;
}

/*  ########################################## END DECIDE ABOUT MODUS ########################################## */

function deinstallConsumer($consumerID)
{
    // only remove config if no error occured before
    $removeConfig = true;
    
    $deinstaller = new ConsumerDeinstaller();
    $consumerHelper = new ConsumerHelper();
    
    $deinstaller->removeDatabase($consumerID);
    
    // fetch errors from the installHelper ...
    foreach($deinstaller->getError() AS $msg) {
        displayError($msg);
        $removeConfig = false;
    }

    foreach($deinstaller->getInfo() AS $msg)
        displayMessage($msg);            

    // ... and delete the messages
    $deinstaller->cleanError();
    $deinstaller->cleanInfos();

    // -----------------------------------------------------------------
    // remove the filesystem for the Consumer
    $deinstaller->removeConsumerDirectory( $consumerID );
    
    $err = $deinstaller->getError();
    
    if (count($err) == 0)  {
        // No error occured while deleting consumer files!
        displayMessage( getTranslation('removed_files') );
    }
    else
    {
        $removeConfig = false;
        // List errors that occured while deleting consumer files!
        foreach ($err AS $msg) {
            displayError($msg);
        }
    }

    foreach($deinstaller->getInfo() AS $msg)
        displayMessage($msg);            

    // ... and delete the messages
    $deinstaller->cleanError();
    $deinstaller->cleanInfos();
    
    // -----------------------------------------------------------------
    // last but not least, remove the consumer domain mapping
    if($removeConfig)
    {
        $res = $deinstaller->deleteFromConsumerConfiguration($consumerID);
        
        if (!$res) {
            displayError( getTranslation('error_remove_cconfig') );
        } else {
            displayMessage( getTranslation('remove_cconfig') );
        }
    }
    else
    {
        displayMessage( getTranslation('not_removed_config') );
    }
}

function displayConfirmFormular($consumerID)
{
    $consumerHelper = new ConsumerHelper();
    $consumer = $consumerHelper->getConsumerByID($consumerID);

    echo '<b>'.getTranslation('confirm_title').'</b><br/><br/>';
    echo getTranslation('confirm_info').'<br/><br/>';

    $alias = '';
    foreach($consumer->getAlias() AS $aliasName) {
        if($aliasName != $consumer->getDomainName()) {
            $alias .= $aliasName . '<br>';
        }
    }

	$tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("DeinstallCommunity.tpl.htm", true, true);
    $tpl->setVariable("CONSUMER_ID", $consumer->getID());
    $tpl->setVariable("CONSUMER_NAME", $consumer->getDomainName());
    $tpl->setVariable("CONSUMER_DELETE", createAdminLink($GLOBALS['MENU']->getID(), array(PARAM_MODE => MODE_DELETE_CONSUMER, PARAM_CONSUMER => $consumer->getID())));
    $tpl->setVariable("CONSUMER_ALIAS", $alias);
	$tpl->show();
}

function displayChooseConsumerFormular()
{
	$tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("DeinstallCommunity.tpl.htm", true, true);
	$cssClass = 'row1';
	
    $possible = array();
    
    $consumerHelper = new ConsumerHelper();
    $allConsumer = $consumerHelper->getAllConsumer();
    $defaultConsumer = $consumerHelper->getDefaultConsumer();
    
    foreach ($allConsumer AS $consumer)
    {
        if(isAllowedDomain($defaultConsumer, $consumer))
        {
            $name = $consumer->getDomainName();
            $alias = '';
            foreach($consumer->getAlias() AS $aliasName) {
                if($aliasName != $consumer->getDomainName()) {
                    $alias .= $aliasName . '<br>';
                }
            }
            
    	    $tpl->setCurrentBlock("row");
    	    $tpl->setVariable("CSS", $cssClass);
    	    $tpl->setVariable("CONSUMER_ID", $consumer->getID());
    	    $tpl->setVariable("CONSUMER_NAME", $name);
	        $tpl->setVariable("CONSUMER_ALIAS", $alias);
    	    $tpl->setVariable("CONSUMER_DELETE", createAdminLink($GLOBALS['MENU']->getID(), array(PARAM_MODE => MODE_REQUEST_CONFIRMATION, PARAM_CONSUMER => $consumer->getID())));
    	    $tpl->parseCurrentBlock("row");
    	    $cssClass = ($cssClass == 'row1') ? 'row2' : 'row1';
    	    // getHelpImage( getTranslation('uninstall_cid_help') )
    	}
    }
	$tpl->show();
}

function isAllowedDomain($defaultConsumer, $consumer)
{
    if ($consumer == null)
        return false;
    
    if(!is_null($defaultConsumer) && $defaultConsumer->getID() == $consumer->getID())
        return false;
    
    if(_CID_ == $consumer->getID())
        return false;
    
    return true;
}

function getDomainError($defaultConsumer, $consumer)
{
    if ($consumer == null)
        return getTranslation('error_null_consumer');
    
    if($defaultConsumer->getID() == $consumer->getID())
        return getTranslation('error_default_consumer');
    
    if(_CID_ == $consumer->getID())
        return getTranslation('error_current_consumer');
    
    return 'ERROR';
}

admin_footer();

?>
