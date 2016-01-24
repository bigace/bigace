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

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

/**
 * This command is used to render menus with the Smarty Template Engine.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.command
 */

import('classes.smarty.BigaceSmarty');
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
        // if user has the rights to read this Page, the template is loaded
        if ($USER_MENU_RIGHT->canRead())
        {
            import('classes.smarty.SmartyDesign');
            import('classes.smarty.BigaceSmarty');
            $smarty = BigaceSmarty::getSmarty();
            $smarty->assign('MENU', $MENU);
            $smarty->assign('USER', $GLOBALS['_BIGACE']['SESSION']->getUser());

            if($GLOBALS['_BIGACE']['PARSER']->getSubAction() != '') {
                // load template without using a design
                $GLOBALS['SMARTY_TEMPLATE'] = new SmartyTemplate($GLOBALS['_BIGACE']['PARSER']->getSubAction());
                if($GLOBALS['SMARTY_TEMPLATE']->getName() != $GLOBALS['_BIGACE']['PARSER']->getSubAction()) {
                    $GLOBALS['LOGGER']->logError('Could not find template by SubAction: '.$GLOBALS['_BIGACE']['PARSER']->getSubAction().'!');
                }
            }
            else {
                // load a design first and extract the template
                if($GLOBALS['_BIGACE']['PARSER']->getAction() != '') {
                    // Load Design by Parameter
                    $GLOBALS['SMARTY_DESIGN'] = new SmartyDesign($GLOBALS['_BIGACE']['PARSER']->getAction());
                    if($GLOBALS['SMARTY_DESIGN']->getName() != $GLOBALS['_BIGACE']['PARSER']->getAction()) {
                        $GLOBALS['LOGGER']->logError('Could not find design by Action: '.$GLOBALS['_BIGACE']['PARSER']->getAction().'!');
                    }
                }
                else {
                    $GLOBALS['SMARTY_DESIGN'] = new SmartyDesign($MENU->getLayoutName());

                    // if this happens, we might have an updated system, that does not yet
                    // migrated its templates to the smarty system!
                    // Redirect to the deprecated Menu Command...
                    if($GLOBALS['SMARTY_DESIGN']->getName() != $MENU->getLayoutName()) {
                        $GLOBALS['LOGGER']->logError('Called Smarty Command (Menu ID '.$MENU->getID().'), but could not find template: '.$MENU->getLayoutName().'! Redirect to menu command!');
                        header("HTTP/1.1 301 Moved Permanently");
                        header('Location: ' . createCommandLink(_BIGACE_CMD_MENU, $MENU->getID()));
                    }
                }

                // TODO: create a TemplateNotFound Exception
                if($GLOBALS['SMARTY_DESIGN']->getName() == '') {
                   	import('classes.exception.ExceptionHandler');
                 	import('classes.exception.NotFoundException');
                    ExceptionHandler::processCoreException( new NotFoundException(404, 'Could not find Smarty Template for ID: ' . $GLOBALS['_BIGACE']['PARSER']->getItemID()) );
                }

                $GLOBALS['SMARTY_STYLESHEET'] = $GLOBALS['SMARTY_DESIGN']->getStylesheet();
                $GLOBALS['SMARTY_TEMPLATE']   = $GLOBALS['SMARTY_DESIGN']->getTemplate();
                $smarty->assign('stylesheet', $GLOBALS['SMARTY_STYLESHEET']->getURL());
            }

            Hooks::do_action('page_header', $MENU);

			$MENU_SERVICE->increaseViewCounter($MENU->getID(),$MENU->getLanguageID());
          	$html = $smarty->fetch($GLOBALS['SMARTY_TEMPLATE']->getFilename());
            echo Hooks::apply_filters('parse_content', $html, $MENU);
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


/**
 * Displays the hidden footer.
 */
include_once(_BIGACE_DIR_LIBS . 'footer.inc.php');
