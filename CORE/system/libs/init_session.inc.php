<?php
//
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS                                           |
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
// | BIGACE is distributed in the hope that it will be useful,              |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// +------------------------------------------------------------------------+
//

/**
 * Library used to initialize a proper BIGACE Session!
 *
 * This file starts the session, checks the requested language,
 * loads the System Configuration, loads the Standard functions,
 * initiates the Database Connection and checks if User
 * is active and not blocked for this session.
 * Configured AutoJobs will be executed here as well.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.libs
 */


// ---------------------------- [CLEAN UP THE ENVIRONMENT] ------------------------------------
$banned = array( '_files', '_env', '_get', '_post', '_cookie', '_server', '_session', 'globals' );
$input = array_merge($_GET, $_POST, $_COOKIE, $_ENV, $_FILES); // attacks through $_SERVER ???
foreach ($input	as $key => $value)
{
    $intval = intval( $key );
    $failed = in_array( strtolower( $key ), $banned ); // PHP GLOBALS injection bug
    // if a parameter name is a number, this matches. failed in template admin
    //$failed |= is_numeric( $key ); // PHP Zend_Hash_Del_Key_Or_Index bug
    if ($failed) {
		die( 'BIGACE catched illegal variable <b>' . implode( '</b> or <b>', $banned ) . '</b>.' );
    }
}
unset($banned);
unset($input);

// remove all slashes which might have been added by magic quotes, which will be removed with php 6
// see http://de3.php.net/magic_quotes
if (get_magic_quotes_gpc()) {
	$in = array(&$_GET, &$_POST, &$_COOKIE);
	while (list($k,$v) = each($in)) {
    	foreach ($v as $key => $val) {
        	if (!is_array($val)) {
				$in[$k][$key] = stripslashes($val);
				continue;
			}
			$in[] =& $in[$k][$key];
		}
	}
}

// fix missing $_SERVER['REQUEST_URI'] - see http://koivi.com/apache-iis-php-server-array.php
if ( empty( $_SERVER['REQUEST_URI'] ) )
{
	// IIS Mod-Rewrite
	if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) {
		$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
	}
	// IIS Isapi_Rewrite
	else if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
		$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
	}
	else {
		// If root then simulate that no script-name was specified
		if (empty($_SERVER['PATH_INFO']))
			$_SERVER['REQUEST_URI'] = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')) . '/';
		elseif ( $_SERVER['PATH_INFO'] == $_SERVER['SCRIPT_NAME'] )
			// Some IIS + PHP configurations puts the script-name in the path-info (No need to append it twice)
			$_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'];
		else
			$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];

		// Append the query string if it exists and isn't null
		if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
			$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
		}
	}
}

// Fix for PHP as CGI hosts that set SCRIPT_FILENAME to something ending in php.cgi for all requests
if ( isset($_SERVER['SCRIPT_FILENAME']) && ( strpos($_SERVER['SCRIPT_FILENAME'], 'php.cgi') == strlen($_SERVER['SCRIPT_FILENAME']) - 7 ) )
	$_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];

// Fix for Dreamhost and other PHP as CGI hosts
if (strpos($_SERVER['SCRIPT_NAME'], 'php.cgi') !== false)
	unset($_SERVER['PATH_INFO']);

// Fix empty PHP_SELF
$PHP_SELF = $_SERVER['PHP_SELF'];
if ( empty($PHP_SELF) )
	$_SERVER['PHP_SELF'] = $PHP_SELF = preg_replace("/(\?.*)?$/",'',$_SERVER["REQUEST_URI"]);
// --------------------------------------------------------------------------------------------


// -------------------------- [CREATE BIGACE RUNTIME ENVIRONMENT] -----------------------------
require_once(dirname(__FILE__).'/constants.inc.php');

set_include_path(get_include_path() . PATH_SEPARATOR . _BIGACE_DIR_ROOT.'/system/classes' . PATH_SEPARATOR . dirname(__FILE__));

// Global array where all BIGACE CMS informations are stored in
$_BIGACE = array();
$_BIGACE['START_TIME'] = microtime(true);
$GLOBALS['_BIGACE'] = &$_BIGACE;

// load system configuration
require_once( _BIGACE_DIR_ROOT . '/system/config/config.system.php');

// if you do not want to allow community dependend configurations, set this to false
if(!defined('ALLOW_COMMUNITY_CONFIG')) define('ALLOW_COMMUNITY_CONFIG', true);

// redirect to installer in case of uninstalled systems
if($_BIGACE['db']['host'] == '{CID_DB_HOST}')
{
	if(file_exists(_BIGACE_DIR_ROOT.'/misc/install/index.php')) {
	    if(strpos($PHP_SELF,'/public/') === false) header('Location: misc/install/index.php');
	    else header('Location: ../misc/install/index.php');
	}
	echo 'System is not properly configured. Please check /system/config/config.system.php.';
    exit;
}

// ------------------------------ [DIRECTORYS] -------------------------------------
$_BIGACE['DIR']['libs']         = _BIGACE_DIR_LIBS;
$_BIGACE['DIR']['editor']       = _BIGACE_DIR_EDITOR;
$_BIGACE['DIR']['admin']        = _BIGACE_DIR_ADMIN;
$_BIGACE['DIR']['php_public']	= _BIGACE_DIR_PUBLIC;
$_BIGACE['DIR']['addon']        = _BIGACE_DIR_ADDON;

// load standard procedures
require_once(dirname(__FILE__).'/functions.inc.php');

// load required core classes
import('classes.core.SQLHelper');
import('classes.core.Session');
import('classes.core.ServiceFactory');
import('classes.core.Hooks');
import('classes.language.Language');
import('classes.consumer.ConsumerService');

$services = ServiceFactory::get();

// ---------------------------- [INITIALIZE LOGGING FRAMEWORK] --------------------------------
$GLOBALS['LOGGER'] = $services->getService('logger');
if(isset($_BIGACE['log']['level']))	$GLOBALS['LOGGER']->setLogLevel($_BIGACE['log']['level']);

// we will do our own error handling
error_reporting(E_ALL);

// user defined error handling function
function _bigace_error_handler($errno, $errmsg, $filename = '', $linenum = '', $vars = '') {
    $GLOBALS['LOGGER']->logScriptError($errno, $errmsg, $filename, $linenum, $vars);
}
set_error_handler('_bigace_error_handler');

// make sure to close the logger ressource
register_shutdown_function ('_bigace_finalize');

function _bigace_finalize() {
    $GLOBALS['LOGGER']->finalize();
}
// --------------------------------------------------------------------------------------------


// --------------------------------- [DATABASE DEFINITION] ------------------------------------
// Define default Select Columns...
$_BIGACE['SELECT']['default']['full']    = 'a.*';
$_BIGACE['SELECT']['default']['light']   = 'a.id,a.language,a.name,a.parentid,a.description,a.unique_name,a.text_1,a.num_3';
$_BIGACE['SELECT']['default']['default'] = 'a.id,a.language,a.name,a.parentid,a.description,a.unique_name,a.text_1,a.num_3';

$dbTemp = $services->getService('database');
if(!$dbTemp->isConnected()) {
	import('classes.exception.ExceptionHandler');
	import('classes.exception.CoreException');
	ExceptionHandler::processSystemException( new CoreException('db', "Database connection could not be established.") );
    exit;
}
$_BIGACE['SQL_HELPER']  = new SQLHelper( $dbTemp );
unset($dbTemp);
// --------------------------------------------------------------------------------------------


// -------------------------------- [COMMUNITY ENVIRONMENT] -----------------------------------
// Find current community
$communityService = new ConsumerService();
$community = $communityService->getConsumerByName($_SERVER['HTTP_HOST'],$_SERVER['REQUEST_URI']);
define ('_CID_', $community->getID());
unset($communityService);

// Consumer specific Directorys
define ('_BIGACE_DIR_CID', _BIGACE_DIR_CONSUMER . 'cid' . _CID_ . '/');
define ('_CID_DIR_PATH', BIGACE_DIR_PATH . $community->getPath());

$_BIGACE['DIR']['consumer']			  = _BIGACE_DIR_CID;
$_BIGACE['DIR']['language']			  = _BIGACE_DIR_CID . 'language/';
$_BIGACE['DIR']['cache']              = _BIGACE_DIR_CID . 'cache/';
$_BIGACE['DIR']['modul']              = _BIGACE_DIR_CID . 'modul/';
$_BIGACE['DIR']['stylesheets']        = _BIGACE_DIR_PUBLIC . 'cid'._CID_.'/';

// Load Community dependend config if allowed
if (ALLOW_COMMUNITY_CONFIG && file_exists(_BIGACE_DIR_CID . 'config/config.system.php')) {
    include_once(_BIGACE_DIR_CID . 'config/config.system.php');
}
require_once( _BIGACE_DIR_ROOT . '/system/config/config.default.php');
// --------------------------------------------------------------------------------------------


// ---------------------------- [INITIALZE THE BIGACE SESSION] --------------------------------
if(isset($_BIGACE['system']))
{
	if(isset($_BIGACE['system']['session_name']) && $GLOBALS['_BIGACE']['system']['session_name'] != '')
		session_name($GLOBALS['_BIGACE']['system']['session_name']);

	// set the max lifetime for the session cookie if none browser based sessions are configured
	if ($GLOBALS['_BIGACE']['system']['session_lifetime']) {
		session_set_cookie_params($GLOBALS['_BIGACE']['system']['session_lifetime']);
	}
}

// start a session automatically ONLY if none is running (e.g. included from another script) AND session name is given
if( !bigace_session_started() && (strlen(extractVar(bigace_session_name(), '')) > 0 )) {
	bigace_session_start();
}

// create the global session objects that are used in multiple scripts
$_BIGACE['SESSION'] = new Session($community);
$USER = $_BIGACE['SESSION']->getUser();
// --------------------------------------------------------------------------------------------


// ----------------------------------- [MIXED SETTINGS] ---------------------------------------
// set timezone
if(defined('BIGACE_TIMEZONE')) date_default_timezone_set(BIGACE_TIMEZONE);

import('classes.configuration.ConfigurationReader');
ConfigurationReader::getPackage('system'); // precache system package
if(!isset($_BIGACE['log']['level']))
	$GLOBALS['LOGGER']->setLogLevel(ConfigurationReader::getConfigurationValue('system', 'logger.loglevel', '2047'));

// decide which command is used for handling menus
define ('_BIGACE_CMD_MENU', (ConfigurationReader::getConfigurationValue('system', 'use.smarty', true) ? _BIGACE_CMD_SMARTY : 'menu'));

// item definitions
$_BIGACE['ITEMS'][_BIGACE_ITEM_MENU]  = array ('id' => _BIGACE_ITEM_MENU,  'name' => 'Menu',  'cmd' => _BIGACE_CMD_MENU,  'dir' => $GLOBALS['_BIGACE']['DIR']['consumer'] . 'items/html/');
$_BIGACE['ITEMS'][_BIGACE_ITEM_IMAGE] = array ('id' => _BIGACE_ITEM_IMAGE, 'name' => 'Image', 'cmd' => _BIGACE_CMD_IMAGE, 'dir' => $GLOBALS['_BIGACE']['DIR']['consumer'] . 'items/image/');
$_BIGACE['ITEMS'][_BIGACE_ITEM_FILE]  = array ('id' => _BIGACE_ITEM_FILE,  'name' => 'File',  'cmd' => _BIGACE_CMD_FILE,  'dir' => $GLOBALS['_BIGACE']['DIR']['consumer'] . 'items/file/');

// consumer specific default language
$_BIGACE['DEFAULT_LANGUAGE'] = $community->getLanguage();
if(is_null($_BIGACE['DEFAULT_LANGUAGE'])) {
	$_BIGACE['DEFAULT_LANGUAGE'] = ConfigurationReader::getConfigurationValue('community', 'default.language', 'en');
}

$_BIGACE['DOMAIN'] = BIGACE_URL_HTTP;

// load all configured plugins
$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement("SELECT name FROM {DB_PREFIX}plugins WHERE cid = {CID}", array(), true);
$plugins = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
if($plugins->count() > 0)
{
	for($pi = 0; $pi < $plugins->count(); $pi++)
	{
	    $plugin = $plugins->next();
	    if(file_exists(BIGACE_PLUGINS.$plugin['name'])) {
		    include_once(BIGACE_PLUGINS.$plugin['name']);
	    }
	    else {
		    $sqlString2 = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement("DELETE FROM {DB_PREFIX}plugins WHERE cid = {CID} AND name = {NAME}", array('NAME' => $plugin['name']), true);
		    $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString2);
		    $GLOBALS['LOGGER']->logError('Removed configured, but not existing plugin: ' . $plugin['name']);
	    }
	    unset($plugin);
    }
    unset($pi);
}
unset($plugins);

// Analyze URL to find command and further url parts
import('classes.parser.LinkParser');
$_BIGACE['PARSER'] = new LinkParser($_SERVER['REQUEST_URI']);

// Setup URL linking
if(BIGACE_USE_SSL === true)
{
    if( ($_BIGACE['PARSER']->getCommand() == 'admin') ||
	    ($_BIGACE['PARSER']->getCommand() == 'application' && $_BIGACE['PARSER']->getAction() == 'auth') ||
	    ($_BIGACE['PARSER']->getCommand() == 'editor') ||
		(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ) {
		$_BIGACE['DOMAIN'] = BIGACE_URL_HTTPS;
	}
}

define ('BIGACE_HOME', $_BIGACE['DOMAIN'] . _CID_DIR_PATH);
define ('_BIGACE_DIR_ADDON_WEB', BIGACE_HOME . 'addon/');
define ('_BIGACE_DIR_PUBLIC_WEB', BIGACE_HOME . 'public/');

define('BIGACE_URL_ADDON', _BIGACE_DIR_ADDON_WEB);
define('BIGACE_URL_PUBLIC', _BIGACE_DIR_PUBLIC_WEB);
define('BIGACE_URL_PLUGINS', BIGACE_HOME . 'plugins/');

// @deprecated - since 2.6 - make available in {directory} before removing
$_BIGACE['DIR']['http'] = BIGACE_HOME;
$_BIGACE['DIR']['public'] = BIGACE_HOME . 'public/';
// --------------------------------------------------------------------------------------------


// -------------------------------- [LANGUAGE ENVIRONMENT] ------------------------------------
// if no language is set, find out which one is preferred
if(is_null($_BIGACE['PARSER']->getLanguage())) {
	$l = $GLOBALS['_BIGACE']['SESSION']->getLanguageID();
	// if no session language is set and we go and find the preferred one
	if (is_null($l) && ConfigurationReader::getConfigurationValue('community', 'accept.browser.language', false)) {
		// try to find a value based on browser settings
		include_once('detect_language.inc.php');
		$l = get_accepted_language($_BIGACE['DEFAULT_LANGUAGE']);
	}
	// still null?? then use the communities default language!
	if(is_null($l)) {
		$l = $_BIGACE['DEFAULT_LANGUAGE'];
	}
	$_BIGACE['PARSER']->setLanguage($l);
	unset($l);
}

// check if we have a language parameter in session, then use it
$_sessLangID = $_BIGACE['PARSER']->getLanguage();

// check if language really exists
$fixLang = (strlen(trim($_sessLangID)) == 0);
if(!$fixLang) {
    $_lt = new Language($_sessLangID);
    $fixLang = ($_lt->getLocale() != $_sessLangID);
    unset($_lt);
}
if($fixLang) {
    // fix values before something nasty will happen
    $_sessLangID = $_BIGACE['DEFAULT_LANGUAGE'];
    $_BIGACE['PARSER']->setLanguage($_sessLangID);
}

// set session language if one is running!
if(bigace_session_started()) {
    if(is_null($_BIGACE['SESSION']->getLanguageID())) {
	    // setting session language ONLY if a session is running
        $_BIGACE['SESSION']->setLanguage($_sessLangID);
    }
    $_sessLangID = $_BIGACE['SESSION']->getLanguageID();
}

// now set the required environment values
$_sessLang = new Language($_sessLangID);
define('_ULC_', $_sessLang->getID());
// see http://forum.bigace.de/general/setlocale-(initsession)/
// see http://php.net/manual/de/function.setlocale.php
setlocale(
    LC_ALL,
    $_sessLang->getFullLocale().'.utf8',
    $_sessLang->getFullLocale().'.UTF8',
    $_sessLang->getFullLocale().'.utf-8',
    $_sessLang->getFullLocale().'.UTF-8',
    $_sessLang->getFullLocale(),
    $_sessLang->getLocale(),
    'en_US.utf8',
    'en_US.UTF8',
    'en_US.utf-8',
    'en_US.UTF-8',
    'en_US',
    'en'
);
setlocale(LC_ALL, $_sessLang->getFullLocale());
// throw away references
unset($_sessLang);
unset($_sessLangID);
// --------------------------------------------------------------------------------------------


// ---------------------------------- [EXECUTE AUTOJOBS] --------------------------------------
$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('autojob_select_time');
$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array('TIME' => time()), true);
$jobs = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
if($jobs->count() > 0)
{
    import('api.job.AutoJob');
	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('autojob_update');
	for($i = 0; $i < $jobs->count(); $i++)
	{
		$temp = $jobs->next();
		$temp_c = createInstance($temp['classname']);
		if(is_subclass_of($temp_c, 'AutoJob')) {
			$autoResult = $temp_c->execute();
			$vals = array('ID' => $temp['name'], 'NEXT' => $temp_c->getNextExecution(),
						  'LAST' => time(), 'STATE' => $autoResult, 'MESSAGE' => $temp_c->getMessage());
			$GLOBALS['_BIGACE']['SQL_HELPER']->execute( $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $vals, true) );
		}
	}
	unset($temp);
	unset($temp_c);
	unset($vals);
	unset($autoResult);
}
unset($sqlString);
unset($jobs);
// --------------------------------------------------------------------------------------------


// ------------------------------ [CLEAN UP GLOBAL NAMESPACE] ---------------------------------
// the community is referenced in the global session object
unset($community);
// do not allow to use standard service instance
unset($services);
