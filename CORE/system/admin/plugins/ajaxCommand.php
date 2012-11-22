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
 * @package bigace.administration
 */

/**
 * Handles Ajax requests.
 */

check_admin_login();

define('_PARAM_PARENT_ID', 'parentID');          // the (new) parent of the item id the request was performed for
define('_PARAM_TREE_ID', 'treeID');              // the item id this request was performed for
define('_PARAM_TREE_LANGUAGE', 'treeLng');       // the language this request was performed for
define('_PARAM_AJAX_MSG', 'text');               // the result text for user feedback

// load required xml functions
require_once(_ADMIN_INCLUDE_DIRECTORY.'xml.php');

$ajaxCmd = extractVar('ajaxCmd', null);
if(!is_null($ajaxCmd)) 
{
    $cmdFile = null;
    switch($ajaxCmd)
    {
        case 'TreeJSON':
        case 'MovePage':
        case 'DeletePage':
            $cmdFile = $ajaxCmd;
            break;
    }
    
    if(!is_null($cmdFile)) 
    {
        require_once(_ADMIN_INCLUDE_DIRECTORY.'ajaxcmd/'.$cmdFile.'.php');
        $cmd = new $cmdFile();
        $cmd->execute();
        unset($cmd);
    }
    else
    {
        SetXmlHeaders();
        echo '<?xml version="1.0"?>';

        echo "\n<UnknownCommand>\n";
        echo createBooleanNode('Result', false, array('command' => $ajaxCmd));
        echo createPlainNode(_PARAM_AJAX_MSG, 'Unknown command: ' . $ajaxCmd);
        echo "\n</UnknownCommand>\n";
        
        $GLOBALS['LOGGER']->logError("AJAX command was requested but does not exist: " . $ajaxCmd);
        
        exit;
    }
} 
else {
        SetXmlHeaders();
        echo '<?xml version="1.0"?>';

        echo "\n<UnknownCommand>\n";
        echo createBooleanNode('Result', false, array('command' => $ajaxCmd));
        echo createPlainNode(_PARAM_AJAX_MSG, 'Missing command!');
        echo "\n</UnknownCommand>\n";
        
        $GLOBALS['LOGGER']->logError("AJAX command was requested without command name");
        
        exit;
}

