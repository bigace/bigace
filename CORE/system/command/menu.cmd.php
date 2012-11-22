<?php
//
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS for MySQL                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) Kevin Papst                                              |
// | Web           http://www.bigace.de                                     |
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

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

/**
 * This Command is the main Command for BIGACE and therefor the most enhanced one.
 * It fetches a Menu by its ID, represented either by its preconfigured Layout or by the given URL Parameters.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.command
 */

import('classes.item.MasterItemType');
import('classes.item.Item');
import('classes.menu.Menu');
import('classes.menu.MenuService');
import('classes.right.RightService');

// initialize some global services used within all scripts and classes
$RIGHT_SERVICE      = new RightService();
$ITEM_SERVICE       = new MasterItemType();
$MENU_SERVICE		= new MenuService();

// Load the requested Menu and set it as global variable
$MENU				= $MENU_SERVICE->getMenu( $GLOBALS['_BIGACE']['PARSER']->getItemID(), $GLOBALS['_BIGACE']['PARSER']->getLanguage() );

$LANGUAGE = new Language($MENU->getLanguageID());
header( "Content-Type:text/html; charset=" . $LANGUAGE->getCharset() );
unset($LANGUAGE);

//if (count($GLOBALS['_BIGACE']['PARSER']->getExtensions()) > 0 || $MENU->exists())
if (strcmp($GLOBALS['_BIGACE']['PARSER']->getItemID(), (int)$GLOBALS['_BIGACE']['PARSER']->getItemID()) == 0 && $MENU->exists())
{
	$USER_MENU_RIGHT = $RIGHT_SERVICE->getMenuRight($GLOBALS['_BIGACE']['SESSION']->getUserID(), $MENU->getId());

    $COMMUNITY = $GLOBALS['_BIGACE']['SESSION']->getConsumer();
    if ($GLOBALS['_BIGACE']['SESSION']->isAnonymous() && !$COMMUNITY->isActivated() && !isset($_POST['ignoreMaintenance']))
    {
        // Display the Maintenance Exception Screen
        import('classes.exception.ExceptionHandler');
        import('classes.exception.MaintenanceException');

        ExceptionHandler::processCoreException( new MaintenanceException($COMMUNITY->getMaintenanceHTML()) );
        unset($COMMUNITY);
    }
    else
    {
        unset($COMMUNITY);
        if ($USER_MENU_RIGHT->canRead())
        {
            /*
             * If User has the rights to read this Page, the Menus Layout is loaded.
             * From here one the handling is given to the Developer.
             */
			import('classes.layout.Layout');
            $LAYOUT = new Layout($MENU->getLayoutName());

            // if this happens, we might have an migrated system with old existing MENU links
            // Redirect to the Smarty Command...
            if(!$LAYOUT->exists()) {
                import('classes.smarty.SmartyDesign');
                $GLOBALS['SMARTY_DESIGN'] = new SmartyDesign($MENU->getLayoutName());
                if($GLOBALS['SMARTY_DESIGN']->getName() == $MENU->getLayoutName()) {
                    $GLOBALS['LOGGER']->logError('Redirect from Menu Command (ID '.$MENU->getID().'), to Smarty for template: '.$MENU->getLayoutName().'. Send 301 Header as well.');
					import('classes.util.LinkHelper');
                    header("HTTP/1.1 301 Moved Permanently");
                    header('Location: ' . LinkHelper::getUrlFromCMSLink( LinkHelper::getCMSLinkFromItem($MENU) ));
                    die();
                }
            }

            /* Start: Parsing URL and try to load Layout by Parameter */
            $tempParam = $GLOBALS['_BIGACE']['PARSER']->getAction();
            $keyParam = $GLOBALS['_BIGACE']['PARSER']->getSubAction();

    		if ($tempParam != '') {
    	        $try = new Layout($tempParam, $keyParam);
    	        if ($try->exists()) {
                    $GLOBALS['LOGGER']->logDebug('Found Layout by using URL Parameter ('.$tempParam.','.$keyParam.') : ' . $try->getName());
    	            $LAYOUT = $try;
    	        } else {
                    $GLOBALS['LOGGER']->logError('Could not find Layout by using URL Parameter ('.$tempParam.','.$keyParam.')!');
    	    	}
    	        unset($try);
            }
            unset($tempParam);
            unset($keyParam);

    		$layouturl = $LAYOUT->getFullURL();

            if ( file_exists($layouturl) )
            {
            	Hooks::do_action('page_header', $MENU);

            	$MENU_SERVICE->increaseViewCounter($MENU->getID(),$MENU->getLanguageID());

                ob_start();
                include_once($layouturl);
                echo Hooks::apply_filters('parse_content', ob_get_clean(), $MENU);
            }
            else
            {
		        import('classes.exception.ExceptionHandler');
		        import('classes.exception.NotFoundException');

    		    ExceptionHandler::processCoreException( new NotFoundException(404, 'Missing Layout: '.$LAYOUT->getName().' ('.$LAYOUT->getURL().') for Menu ID: '.$MENU->getID()) );
    	    }
    	}
    	else /* User has no rights to read the Menu */
    	{
	        import('classes.exception.ExceptionHandler');
	        import('classes.exception.CoreException');

    	    if ($GLOBALS['_BIGACE']['SESSION']->isAnonymous()) {
    	        ExceptionHandler::processCoreException( new CoreException('login', 'User is not logged in, missing read rights!') );
            } else {
    	        ExceptionHandler::processCoreException( new CoreException(403, 'User has no rights ro read Menu!') );
    	    }
    	}
    } // no maintenance
}
else /* Requested Menu does not exist */
{
	import('classes.exception.ExceptionHandler');
	import('classes.exception.NotFoundException');

	ExceptionHandler::processCoreException( new NotFoundException(404, 'Could not find Menu ID ['.$GLOBALS['_BIGACE']['PARSER']->getItemID().'] for URL ['.$_SERVER['REQUEST_URI'].']') );
}


// Display the hidden footer
include_once (_BIGACE_DIR_LIBS.'footer.inc.php');

?>