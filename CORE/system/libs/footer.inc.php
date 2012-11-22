<?php
//
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS for MySQL                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) Kevin Papst                                              |
// | Web           http://www.bigace.de                                     |
// | Mirror        http://bigace.sourceforge.net/                           |
// | Sourceforge   http://sourceforge.net/projects/bigace/                  |
// +------------------------------------------------------------------------+
// | This source file is subject to version 2 or (at your option) any later |
// | version, of the GNU General Public License as published by the Free    |
// | Software Foundation, available at:                                     |
// | http://www.gnu.org/licenses/gpl.html                                   |
// +------------------------------------------------------------------------+
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// +------------------------------------------------------------------------+
//

/**
 * This scripts prints the "invisible" Copyright Footer. 
 * You may not remove this footer when redistributing this software.
 *
 * If needed, you can turn it off by setting a Configuration entry.
 * 
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.libs
 */

	/* Show Debug Messages if configured */
    if ( $GLOBALS['LOGGER']->isDebugEnabled() ) 
    {
        // Logs all Request and Session Variables
        foreach ($_SESSION AS $key => $value) { 
            $GLOBALS['LOGGER']->logDebug('SESSION['.$key.']' .' = ' . $value);
        } 

        foreach ($_COOKIE AS $key => $value) { 
            $GLOBALS['LOGGER']->logDebug('COOKIE['.$key.']' .' = ' . $value);
        } 

        if ( isset($_POST) ) {
            foreach ($_POST AS $key => $value) { 
                $GLOBALS['LOGGER']->logDebug('POST['.$key.']' .' = ' . $value);
            } 
        }

        if ( isset($_GET) ) {
            foreach ($_GET AS $key => $value) { 
                $GLOBALS['LOGGER']->logDebug('GET['.$key.']' .' = ' . $value);
            } 
        }
    }
 	
 	if(!ConfigurationReader::getConfigurationValue('system', 'hide.footer', false)) 
 	{
    	echo "\n\n";
    	echo '<!--'."\n";
    	echo "\n";
    	echo '   Site is running BIGACE '._BIGACE_ID.' '."\n";
    	echo '        a PHP based Web CMS for MySQL' . "\n";
    	echo '             (C) Kevin Papst (www.bigace.de)' . "\n";
    	echo "\n";
    	$footer = array();
    	
    	// show the Command ID if is admin or menu
    	if (isset ($GLOBALS['_BIGACE']['PARSER'])) 
    	{
        	if ($GLOBALS['_BIGACE']['PARSER']->getCommand() == 'menu') {
        		$footer[] = 'Menu ID   : ' . $GLOBALS['_BIGACE']['PARSER']->getItemID();
            } else if ($GLOBALS['_BIGACE']['PARSER']->getCommand() == 'admin'){
        		$footer[] = 'Admin ID  : ' . $GLOBALS['_BIGACE']['PARSER']->getItemID();
            }
        }
        
        // switch whether we show the simple or extended footer
	 	if(!ConfigurationReader::getConfigurationValue('system', 'footer.type.extended', false)) 
	 	{
	 		$footer[] = 'User ID   : ' . $GLOBALS['_BIGACE']['SESSION']->getUserID();
	 	}
	 	else
	 	{
	 		$bg_uo = $GLOBALS['_BIGACE']['SESSION']->getUser();	
	 		// Extended Footer shows further infos
    		$footer[] = 'User      : ' . $bg_uo->getName() . ' ('.$bg_uo->getID().')';
        	if ($GLOBALS['_BIGACE']['PARSER']->getCommand() == _BIGACE_CMD_MENU && isset($MENU)) {
    			$footer[] = 'Template  : ' . $MENU->getLayoutName();
        		$mod = $MENU->getModul();
	    		$footer[] = 'Modul     : ' . $mod->getName();
        	}
        	unset($bg_uo);
			$footer[] = 'SQLs      : ' . $GLOBALS['_BIGACE']['SQL_HELPER']->getCounter();
	 	}
    	$footer[] = 'Language  : ' . _ULC_;
    	$footer[] = 'Community : ' . _CID_;
    	$footer[] = 'Time      : ' . (float)(microtime(true) - $GLOBALS['_BIGACE']['START_TIME']).'s';
    	    	
    	foreach($footer AS $footerEntry) {
    		echo '         ' . $footerEntry . "\n";
    	}
    	
    	echo "\n";
    	echo '-->';
    }
    
?>