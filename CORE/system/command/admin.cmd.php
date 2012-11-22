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
 * This Command is specialized for handling administration requests.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.command
 */

import('classes.exception.ExceptionHandler');
import('classes.exception.NotAllowedException');
import('classes.exception.NotFoundException');
import('classes.util.links.AdministrationLink');

// ----------------------------------------------------------------------------
// ------------        SET UP THE ADMINISTRATION ENVIRONMENT       ------------
// ----------------------------------------------------------------------------
//definition to make sure each script is included only within a proper set up environment
define('_VALID_ADMINISTRATION', '_IN_ADMINISTRATION');
// plugin start directory
define('_ADMIN_PLUGIN_DIRECTORY', _BIGACE_DIR_ADMIN . 'plugins/');
// plugin start directory
define('_ADMIN_INCLUDE_DIRECTORY', _BIGACE_DIR_ADMIN . 'plugins/includes/');
// template directory
define('_ADMIN_TEMPLATE_DIRECTORY', _BIGACE_DIR_ADMIN . 'templates/');
// a boolean variable in the global scope with this name defines whether the footer will be shown or not
define('HIDE_ADMIN_FOOTER', 'hideAdminFooter');
// the width for all large tables (to have a overall size)
define('ADMIN_MASK_WIDTH_LARGE', '100%');
// the width for all smaller tables (to have a overall size)
define('ADMIN_MASK_WIDTH_SMALL', '100%');

// #################### ADMINISTRATION MENU IDs #######################
define ('_ADMIN_ID_MAIN',               'index');

// SUB MENU ADMIN PLUGINs for linking
define ('_ADMIN_ID_MENU_CREATE',        'itemMenuCreate');
define ('_ADMIN_ID_USER_ADMIN',         'userAdmin');
define ('_ADMIN_ID_CATEGORY_ADMIN',     'categoryAdmin');
define ('_ADMIN_ID_FRIGHT_ADMIN',       'frightListing');
define ('_ADMIN_ID_CATEGORY_CREATE',    'categoryCreate');
define ('_ADMIN_ID_FILE_MAIN',          'fileAdmin');
define ('_ADMIN_ID_IMAGE_MAIN',         'imagesAdmin');
define ('_ADMIN_ID_WELCOME',            'welcome');

define ('_LANGUAGE_PARAM', 'languageID');

// ############# CONSTANTS USED THROUGHOUT ADMIN SCRIPTS ##############
define ('_PARAM_ADMIN_MODE',        'mode');
define ('_MODE_EDIT_ITEM',          'changeattrib');
define ('_MODE_SAVE_ITEM',          '6');
define ('_MODE_UPDATE_WITH_UPLOAD', '19');
define ('_MODE_MOVE_ITEM',          'moveItemVersion');
define ('_MODE_DISPLAY_HISTORY',    'displayHistoryVersion');
define ('_MODE_DELETE_HISTORY',     'deleteSelectedHistoryVersions');
define ('_MODE_DELETE_HISTORY_MENU','deleteHistoryVersions');
define ('_MODE_REFRESH_HISTORY_CONTENT', 'refreshHistoryVersionContent');
define ('_MODE_CREATE_LANGUAGE',    'createLanguageVersion');
define ('_MODE_DELETE_LANGUAGE',    'deleteLanguageVersion');
define ('_MODE_BROWSE_MENU',        '3');
define ('_MODE_CHANGE_RIGHT',       'changeRight');
define ('_MODE_CREATE_RIGHT',       'createRight');
define ('_MODE_DELETE_RIGHT',       'deleteRight');
define ('_MODE_SHOW_UPLOAD_FORM', 	'1');
define ('_MODE_PROCESS_UPLOAD', 	'2');
define ('_MODE_SHOW_UPLOAD_RESULT', '3');
define ('_MODE_DOWNLOAD_AS_ZIP',    'downloadZippedItems');
define ('_MODE_SET_GROUP_PERM',     'setGroupPerm');  // set group permissions for multiple items
define ('_MODE_UPDATE_MULTIPLE',    'multipleUpdate'); // switch to form to update multiple items at once
define ('_MODE_DELETE_MULTIPLE',    'multipleDelete'); // delete multiple items at once
define ('_MODE_PARENT_MULTIPLE',    'multipleParent'); // move multiple items to new parent
define ('_BIGACE_COLUMN_MODUL_ID',  'text_3');
define ('_BIGACE_COLUMN_LAYOUT_ID', 'text_4');
define ('USERADMIN_MODE_EDIT_USER', 'admin'); // required for admin header and user admin


import('classes.right.RightService');
import('classes.fright.FrightService');
import('classes.template.TemplateService');
import('classes.util.IOHelper');
import('classes.util.LinkHelper');
import('classes.util.CMSLink');
import('classes.util.Translations');

import('classes.item.MasterItemType');
import('classes.item.Item');
import('classes.item.Itemtype');
import('classes.item.ItemService');

import('classes.util.IOHelper');
import('classes.administration.AdminMenu');
import('classes.administration.AdminMenuItem');
import('classes.administration.AdminMenuItemDynamic');

/**
 * Get all allowed Menus.
 */
function getAdminMenus()
{
    $allowed = array();
    foreach($GLOBALS['_BIGACE']['ADMIN']['menu'] AS $key => $submenu) {
        if ($submenu->hasChilds()) {  // admin menu
            $allowed[] = $submenu;
        }
    }

    return $allowed;
}

function getAdminMenu($id)
{
    if(isset($GLOBALS['_BIGACE']['ADMIN']['menu'][$id]))
        return $GLOBALS['_BIGACE']['ADMIN']['menu'][$id];
    return null;
}


/**
 * All administration Frameset links should be wrapped by this method!
 * Imagine design changes, who would fix all the damned links?!?
 */
function createAdminFramesetLink($id, $language, $params = array(), $name = '')
{
    $adminLink = new AdministrationLink($id);
    $adminLink->setCommand('admin');
    $adminLink->setLanguageID($language);
    $adminLink->setAction('ADMIN');
    if($name != '')
        $adminLink->setFilename($name);

    return LinkHelper::getUrlFromCMSLink($adminLink, $params);
}

/**
 * All administration links should be wrapped by this method!
 * Imagine design changes, who would fix all the damned links?!?
 */
function createAdminLink($id, $params = array(), $name = '', $frame='')
{
    if($frame == '') $frame = ($GLOBALS['_BIGACE']['PARSER']->getSubAction() == '' ? 'content' : $GLOBALS['_BIGACE']['PARSER']->getSubAction());
    return createAdminLanguageLink($id, ADMIN_LANGUAGE, $params, $name, $frame);
}

function createAdminLanguageLink($id, $language, $params = array(), $name = '', $frame='content')
{
    $adminLink = new AdministrationLink($id);
    $adminLink->setCommand('admin');
    $adminLink->setLanguageID($language);
    //$adminLink->setAction('ADMIN');
//    $adminLink->setSubAction($frame);
    if($name != '')
        $adminLink->setFilename($name);

    return LinkHelper::getUrlFromCMSLink($adminLink, $params);
}

/**
 * @return the proper initialized Smarty Engine for Administration Templates.
 */
function getAdminSmarty()
{
    import('classes.smarty.BigaceSmarty');
    $smarty = BigaceSmarty::getAdminSmarty();
    $smarty->assign('PUBLIC_DIR', _BIGACE_DIR_PUBLIC_WEB);
    $smarty->assign('STYLE_DIR', $GLOBALS['_BIGACE']['style']['DIR']);
    $smarty->assign('BIGACE_VERSION', _BIGACE_ID);
    $smarty->assign('SMALL_FORM', ADMIN_MASK_WIDTH_SMALL);
    $smarty->assign('LARGE_FORM', ADMIN_MASK_WIDTH_LARGE);
    return $smarty;
}

/**
 * Helper method for building an array to be used by the
 * <code>createAdminLink()</code>
 * function.
 * Submit a Name and an Array and it is refactored.
 *
 * Lets say we have the Array:
 * <code>$a = array('foo' => 'bar')</code>
 * and call
 * <code>prepareArrayLink('data', $a)</code>
 * the Result will look like that:
 * <code>array('data[foo]' => 'bar')</code>
 */
function prepareArrayLink($name, $values)
{
    $url = array();
    if (is_array($values))
    {
        foreach($values AS $key => $val)
        {
            if (is_array($val))
                array_merge($url, prepareArrayLink($key, $val) );
            else
                $url[$name . '['.$key.']'] = $val;
        }
    }
    return $url;
}

/**
 * Creates a back link with the current Style settings.
 * If no text was submitted, the Link text will look like that:
 * <code>getTranslation('to_overview')</code>
 *
 * @param String the Admin Menu ID
 * @param Array the Values to be appended to the Link
 * @param String the Description of the Link)
 */
function createBackLink($id, $values = array(), $text = '') {
    return createStyledBackLink(createAdminLink($id, $values), (($text == '') ? getTranslation('to_overview') : $text));
}

/**
 * Check if the given Name is a valid Admin Directory.
 * Currently only the simple check for '.', '..' and 'CVS' is performed.
 * @param String the relative Directory name
 * @return boolean whether this might be a valid Admin directory or not
 */
function isAllowedAdminDirectory($dir) {
    return ($dir != '.' && $dir != '..' && strtoupper($dir) != 'CVS');
}


/**
 * Returns the Menus between ADMIN INDEX where the current User has
 * sufficient functional rights to access.
 * @access public
 * @param array an array of <code>AdminMenu</code> Objects
 */

function admin_header($isSimpleContent = false)
{
    $langToDisplay  = extractVar(_LANGUAGE_PARAM, ADMIN_LANGUAGE);
    $LANGUAGE       = new Language( $langToDisplay );

	$jsDir 			= _BIGACE_DIR_PUBLIC_WEB.'system/javascript/';
	$description    = (strlen(trim($GLOBALS['MENU']->getDescription())) > 0) ? $GLOBALS['MENU']->getDescription() : "";

	header( "Content-Type:text/html; charset=" . $LANGUAGE->getCharset() );

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html>
<head>
    <title>BIGACE '.getTranslation('admin').'</title>
    <meta name="description" content="'.getTranslation('admin').'">
    <link rel="stylesheet" href="'.$GLOBALS['_BIGACE']['style']['DIR'].'style.css" type="text/css">
    <!--[if gte IE 6]>
    <link rel="stylesheet" href="'.$GLOBALS['_BIGACE']['style']['DIR'].'ie.css" type="text/css" media="all" />
    <![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset='.extractVar('adminCharset', $LANGUAGE->getCharset()).'">
    <meta name="generator" content="BIGACE '._BIGACE_ID.'">
    <meta name="robots" content="noindex,nofollow">
	<link rel="shortcut icon" type="image/x-icon" href="'._BIGACE_DIR_PUBLIC_WEB.'system/images/favicon.ico" />
	<script type="text/javascript" src="'.$jsDir.'administration.js"></script>
    <script type="text/javascript" src="'.$jsDir.'overlib_mini.js"></script>
    <script type="text/javascript" src="'.BIGACE_URL_ADDON.'jquery/jquery-1.3.2.min.js"></script>
    <script type="text/javascript" src="'.BIGACE_URL_ADDON.'jquery/jquery-ui-1.7.2.custom.min.js"></script>
    <link type="text/css" rel="stylesheet" href="'.BIGACE_URL_ADDON.'jquery/themes/smoothness/jquery-ui-1.7.2.custom.css" />
    <script type="text/javascript" src="'.BIGACE_URL_ADDON.'jquery/tablesorter/jquery.tablesorter.js"></script>';
    Hooks::do_action('admin_html_head');
echo '
</head>
<body>

<div class="adminScript">
';
    if(!$isSimpleContent)
    {
	echo '
        <table border="0" width="100%" class="scriptTitle">
            <tr>
                <td valign="bottom"><h1 onclick="toogleVisibilityById(\'menuInfoID\')" style="display:inline">'.$GLOBALS['MENU']->getTitle().'</h1></td>
                <td align="right"><a class="manualLink" href="http://wiki.bigace.de/bigace:manual:'.$GLOBALS['MENU']->getId().'" target="_blank"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'manual.png" align="absmiddle"></a></td>
			</tr>
        </table>
            <p class="scriptInfo" id="menuInfoID" style="display:block">'.$description.'</p>
            <script type="text/javascript">
            <!--
            	$(function() { toogleVisibilityById(\'menuInfoID\'); });
            // -->
            </script>
    <div id="mainContent">
    ';

    }
    Hooks::do_action('admin_header');
}

function admin_footer($isSimpleContent = false)
{
    // The Copyright footer should not be shown in Simple Content
    if(!$isSimpleContent) {
        import('classes.util.html.CopyrightFooter');
        CopyrightFooter::toString();
    	Hooks::do_action('admin_footer');
    }
    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("AdminContentFooter.tpl.html", false, false);
    $tpl->show();
}

/**
 * Checks proper access of the Administration.
 */
function check_admin_login()
{
    if(!defined('_VALID_ADMINISTRATION'))
        die('This file can NOT not be executed directly!');

    if ($GLOBALS['_BIGACE']['SESSION']->isAnonymous()) {
        ExceptionHandler::processCoreException( new NotAllowedException('anonymous', 'Access forbidden for anonymous user!') );
    }
}

function check_csrf_token($token = null)
{
    if ($token === null && isset($_POST['hashtoken'])) {
        $token = $_POST['hashtoken'];
    }

    if ($token === null && isset($_GET['hashtoken'])) {
        $token = $_GET['hashtoken'];
    }

    if ($token !== null) {
        $hash = $GLOBALS['_BIGACE']['SESSION']->getSessionValue('csrf.hash');
        $ttl = $GLOBALS['_BIGACE']['SESSION']->getSessionValue('csrf.ttl');
        if ($ttl > time() && strcmp($hash, $token) === 0) {
            return true;
        }
    }

    return false;
}

function get_csrf_token($get = false)
{
    $hash = $GLOBALS['_BIGACE']['SESSION']->getSessionValue('csrf.hash');

    if (is_array($get)) {
        $get['hashtoken'] = $hash;
        return $get;
    }

    if ($get === true) {
        return array('hashtoken' => $hash);
    }

    return '<input type="hidden" name="hashtoken" value="'.$hash.'" />';
}

function check_admin_permission($permission = array(), $userid = null)
{
    if($userid == null)
        $userid = $GLOBALS['_BIGACE']['SESSION']->getUserID();

    if(!is_array($permission)) {
        $permission = array( $permission );
    }

    if(count($permission) == 0 || $permission[0] == '')
        return true;

    foreach ($permission AS $fright) {
        if ($GLOBALS['FRIGHT_SERVICE']->hasFright($userid, $fright)) {
            return true;
        }
    }
    return false;
}

// ----------------------------------------------------------------------------
// Language for administration frontend
$adminLang = new Language($GLOBALS['_BIGACE']['PARSER']->getLanguage());
if($adminLang->isAdminLanguage()) {
    define('ADMIN_LANGUAGE', $GLOBALS['_BIGACE']['PARSER']->getLanguage());
} else {
    define('ADMIN_LANGUAGE', 'en'); // hard fallback, english is always available
}

$GLOBALS[HIDE_ADMIN_FOOTER] = FALSE;

if (!$GLOBALS['_BIGACE']['SESSION']->isAnonymous())
{
        // ----------------------------------------------------------------------------
        // ------------        SET UP THE ADMINISTRATION ENVIRONMENT       ------------
        // ----------------------------------------------------------------------------

        require_once(_BIGACE_DIR_ADMIN.'styling.php');

        $FRIGHT_SERVICE = new FrightService();
        $RIGHT_SERVICE = new RightService();

        // Prepare Templare Service and Standard Values!
        $TEMPLATE_SERVICE = new TemplateService();
        $TEMPLATE_SERVICE->addTemplateDirectory($GLOBALS['_BIGACE']['style']['class']->getTemplateDirectory());
        $TEMPLATE_SERVICE->addTemplateDirectory(_ADMIN_TEMPLATE_DIRECTORY);
        setBigaceTemplateValue('ADDON_DIR', _BIGACE_DIR_ADDON_WEB);
        setBigaceTemplateValue('PUBLIC_DIR', _BIGACE_DIR_PUBLIC_WEB);
        setBigaceTemplateValue('STYLE_DIR', $GLOBALS['_BIGACE']['style']['DIR']);
        setBigaceTemplateValue('STYLE_CSS', $GLOBALS['_BIGACE']['style']['class']->getCSS());
        setBigaceTemplateValue('BIGACE_VERSION', _BIGACE_ID);
        setBigaceTemplateValue('SMALL_FORM', ADMIN_MASK_WIDTH_SMALL);
        setBigaceTemplateValue('LARGE_FORM', ADMIN_MASK_WIDTH_LARGE);
        setBigaceTemplateValue('CSRF_TOKEN', get_csrf_token());

        // Maximum upload File Size
        define('UPLOAD_MAX_SIZE', ini_get('upload_max_filesize'));
        // @deprecated
        $UPLOAD_MAX_SIZE = UPLOAD_MAX_SIZE;

        // load global admin translation
    	Translations::loadGlobal('administration', ADMIN_LANGUAGE);
    	Translations::loadGlobal('bigace', ADMIN_LANGUAGE);
    	Translations::loadGlobal('admin_menus', ADMIN_LANGUAGE);

    	// ############################ ADMIN MENU ############################
    	$GLOBALS['_BIGACE']['ADMIN']['menu'] = array();

	    $GLOBALS['_BIGACE']['ADMIN']['menu']['menu'] =
	        array('id'         => 'menu',
	              'name'       => 'menu',
	              'pluginpath' => _ADMIN_PLUGIN_DIRECTORY,
	              'childs'     => array(
	                                    'jsMenuTree' => array('frights' => _BIGACE_FRIGHT_ADMIN_MENUS.',edit_menus', 'hide' => true),
	                                    'menuReorder' => array('frights' => _BIGACE_FRIGHT_ADMIN_MENUS.',edit_menus', 'hide' => true),
	                                    'menuAttributes'       => array('frights' => _BIGACE_FRIGHT_ADMIN_MENUS.',edit_menus', 'hide' => true),
	                                    'menuTree'       => array('frights' => _BIGACE_FRIGHT_ADMIN_MENUS.',edit_menus', 'translate' => false),
	                                    //'itemMenuCreate' => array('frights' => _BIGACE_FRIGHT_ADMIN_MENUS),
	                                    'menuWorkflow'   => array('frights' => _BIGACE_FRIGHT_ADMIN_MENUS.',edit_menus'),
	                                    'itemMenu'       => array('frights' => _BIGACE_FRIGHT_ADMIN_MENUS.',edit_menus', 'translate' => false)
	                                )
	        );

	    $GLOBALS['_BIGACE']['ADMIN']['menu']['media'] =
	        array('id'         => 'media',
	              'name'       => 'media',
	              'pluginpath' => _ADMIN_PLUGIN_DIRECTORY,
	              'childs'     => array('imagesAdmin'   => array('frights' => _BIGACE_FRIGHT_ADMIN_ITEMS.',edit_items', 'translate' => false),
	                                    'fileAdmin'     => array('frights' => _BIGACE_FRIGHT_ADMIN_ITEMS.',edit_items', 'translate' => false),
	                                    'mediaUpload'   => array('frights' => _BIGACE_FRIGHT_ADMIN_ITEMS),
	                                    'categoryAdmin' => array('frights' => 'admin_categorys')
	                                )
	        );

	    $GLOBALS['_BIGACE']['ADMIN']['menu']['smarty'] =
	        array('id'         => 'smarty',
	              'name'       => 'smarty',
	              'pluginpath' => _ADMIN_PLUGIN_DIRECTORY,
	              'childs'     => array('design'        => array('frights' => 'smarty.designs.edit'),
	                                    'templates'     => array('frights' => 'smarty.templates.edit'),
	                                    'stylesheet'    => array('frights' => 'smarty.stylesheets.edit')
	                                )
	        );

	    $GLOBALS['_BIGACE']['ADMIN']['menu']['user'] =
	        array('id'         => 'user',
	              'name'       => 'user',
	              'pluginpath' => _ADMIN_PLUGIN_DIRECTORY,
	              'childs'     => array('userAdmin'         => array('frights' => 'edit_own_profile,admin_users'),
	                                    'groupAdmin'        => array('frights' => 'admin_users,edit.usergroup'),
	                                    'groupPermission'   => array('frights' => 'edit_frights,group_frights'),
	                                    'userCreate'        => array('frights' => 'admin_users')
	                                )
	        );

	    $GLOBALS['_BIGACE']['ADMIN']['menu']['system'] =
	        array('id'         => 'system',
	              'name'       => 'system',
	              'pluginpath' => _ADMIN_PLUGIN_DIRECTORY,
	              'childs'     => array('configurations'    => array('frights' => 'admin_configurations'),
	              				'autojobs'          => array('frights' => 'autojobs.admin'),
	                                    'modules'         => array('frights' => 'module_all_rights'),
	                                    //'adminHints'        => array('frights' => 'system_admin'),
	                                    'languages'         => array('frights' => 'languages_all_rights'),
	                                )
	        );

	    // only database logger supported
        if(is_class_of($GLOBALS['LOGGER'],'DBLogger')) {
            $GLOBALS['_BIGACE']['ADMIN']['menu']['system']['childs']['logging'] = array('frights' => 'logging.messages');
        }
        // only show if statistics are written
        if(ConfigurationReader::getConfigurationValue('system', 'write.statistic', false)) {
            $GLOBALS['_BIGACE']['ADMIN']['menu']['system']['childs']['statistic'] = array('frights' => 'view_statistics');
        }

	    $GLOBALS['_BIGACE']['ADMIN']['menu']['extensions'] =
	        array('id'         => 'extensions',
	              'name'       => 'extensions',
	              'dynamic'    => TRUE,
	              'pluginpath' => _ADMIN_PLUGIN_DIRECTORY,
	              'childs'     => array(
	                                    'updates'           => array('frights' => 'updates_manager')
	        				)
	        );

	    $GLOBALS['_BIGACE']['ADMIN']['menu']['communities'] =
	        array('id'         => 'communities',
	              'name'       => 'communities',
	              'pluginpath' => _ADMIN_PLUGIN_DIRECTORY,
	              'childs'     => array('community'             => array('frights' => 'community.admin'),
	                                    'communityDeinstall'    => array('frights' => 'community.deinstallation'),
	                                    'communityInstall'      => array('frights' => 'community.installation'),
	                                    'maintenance'           => array('frights' => 'community.maintenance'),
	                                    //'dbDump'                => array('frights' => 'export_database')
	                                )
	        );

		// query for plugin menus
	    $GLOBALS['_BIGACE']['ADMIN']['menu'] = Hooks::apply_filters('admin_menu', $GLOBALS['_BIGACE']['ADMIN']['menu']);

	    $MENU = null;

	    foreach($GLOBALS['_BIGACE']['ADMIN']['menu'] AS $menuName => $menuDef)
	    {
	        $current = new AdminMenu($menuDef);
	        if($GLOBALS['_BIGACE']['PARSER']->getItemID() == $menuName) $MENU = $current;

	        $childs = $current->getChildNames();
	        foreach($childs as $name => $values) {
	            $values['id'] = $name;
	            $values['pluginpath'] = $current->getPath();
	            $currentSub = new AdminMenuItem($values, $current);
	            if($GLOBALS['_BIGACE']['PARSER']->getItemID() == $name) $MENU = $currentSub;
	            if(check_admin_permission($currentSub->getPermissions())) {
	                $current->addChild($currentSub);
	            }
	        }

	        // find dynamic childs
	        if($current->isDynamic()) {
	            $path = $current->getPath();
	            $inis = IOHelper::getFilesFromDirectory($path, 'ini', false);
	            foreach($inis AS $name) {
	                $name = getNameWithoutExtension($name);
	                $currentSub = new AdminMenuItemDynamic($path, $name, $current);
	                if($GLOBALS['_BIGACE']['PARSER']->getItemID() == $name) $MENU = $currentSub;
	                // if dynamic menu is allowed to
	                if(check_admin_permission($currentSub->getPermissions())) {
	                    $current->addChild($currentSub);
	                }
	            }
	        }


	        $GLOBALS['_BIGACE']['ADMIN']['menu'][$menuName] = $current;
	    }

	    if($MENU == null) {
	        if($GLOBALS['_BIGACE']['PARSER']->getItemID() == _BIGACE_TOP_LEVEL)
	            $MENU = new AdminMenuItem(array('id' => _ADMIN_ID_WELCOME, 'pluginpath' => _ADMIN_PLUGIN_DIRECTORY), null);
	        else
	            $MENU = new AdminMenuItem(array('id' => $GLOBALS['_BIGACE']['PARSER']->getItemID(), 'pluginpath' => _ADMIN_PLUGIN_DIRECTORY, 'translate' => FALSE), null);
	    }
        // ####################################################################

        $token = $GLOBALS['_BIGACE']['SESSION']->getSessionValue('csrf.hash');
        $ttl = $GLOBALS['_BIGACE']['SESSION']->getSessionValue('csrf.ttl');
        if ($token === null || ($ttl < time() && $token !== null)) {
            $GLOBALS['_BIGACE']['SESSION']->setSessionValue('csrf.hash', getRandomString());
        }
        $GLOBALS['_BIGACE']['SESSION']->setSessionValue('csrf.ttl', time() + 1800);

        // FIXME remove config ???
	    if(ConfigurationReader::getConfigurationValue('admin', 'check.csrf', false))
	    {
    	    /*
		    // this test blocks CSRF attacks
	        if(!isset($_SERVER['HTTP_REFERER'])) { // if someone posted, a referer must be set
	            if( count($_POST) != 0 || count($_GET) > 4 ) // no matter if url rewriting is active or not, not more than 4 get parameter are allowed without referer
		            $MENU = new AdminMenuItem(array('id' => _ADMIN_ID_WELCOME, 'pluginpath' => _ADMIN_PLUGIN_DIRECTORY), null);
	        }

	        if((isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'http://' . $_SERVER['SERVER_NAME']) !== 0
	        					&& strpos($_SERVER['HTTP_REFERER'], 'https://' . $_SERVER['SERVER_NAME']) !== 0)) {
	            import('classes.exception.NoFunctionalRightException');
	            ExceptionHandler::processAdminException( new NoFunctionalRightException('SECURITY WARNING: Your referer does not match the root url.', createAdminLink(_ADMIN_ID_MAIN)) );
	            return;
	        }
	        */
	    }

        // --------------------------------------------------------------------------
        // thats it, now the requested admin plugin is loaded... if it exists ;)
        if(file_exists($MENU->getFileName()))
        {
            if(check_admin_permission($MENU->getPermissions())) {
                $GLOBALS['TEMPLATE_SERVICE']->addTemplateDirectory($MENU->getPath());
                //include(_ADMIN_PLUGIN_DIRECTORY . $GLOBALS['_BIGACE']['PARSER']->getItemID() . '.php');
                if($MENU->isTranslated()) {
                    $MENU->loadTranslation();
                }
                include($MENU->getFileName());
            }
            else {
                import('classes.exception.NoFunctionalRightException');
                ExceptionHandler::processAdminException( new NoFunctionalRightException('Protected Area. You are not allowed to enter!', createAdminLink(_ADMIN_ID_MAIN)) );
                $GLOBALS['LOGGER']->logInfo("** User with ID: ".$GLOBALS['_BIGACE']['SESSION']->getUserID()." tried to access Administration Menu '".$MENU->getID()."' without sufficient rights **");
            }
        }
        else {
            import('classes.exception.NotFoundException');
            ExceptionHandler::processCoreException( new NotFoundException(404, 'Could not find Admin Plugin: '.$MENU->getID()) );
        }
}
else
{
	// changed for 2.4 - show login instead of error message
	import('classes.util.LinkHelper');
	import('classes.util.links.LoginFormularLink');
	$lfl = new LoginFormularLink();
	$lfl->setRedirectID($GLOBALS['_BIGACE']['PARSER']->getItemID());
	$lfl->setRedirectCommand($GLOBALS['_BIGACE']['PARSER']->getCommand());
	header('Location: ' . LinkHelper::getUrlFromCMSLink($lfl));
//    ExceptionHandler::processCoreException( new NotAllowedException('anonymous', 'Anonymous User are NOT allowed to access the Administration Panel!') );
}

if (!$GLOBALS[HIDE_ADMIN_FOOTER]) {
    include_once(_BIGACE_DIR_LIBS.'footer.inc.php');
}
