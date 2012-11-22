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
 */

/**
 * DO NOT MAKE CHANGES IN THIS FILE, BUT ONLY IN "config.system.php".
 * 
 * This file is overwritten with every new installation or upgrade.
 *
 * It defines default values that apply to most installations, which can 
 * be overwritten by putting them into "/system/config/config.system.php" 
 * or the community dependend config file "/consumer/cid{CID}/config/config.system.php".
 * 
 * The order in which configurations are loaded is:
 * - /config/system/config.system.php
 * - /consumer/cid{CID}/config/config.system.php
 * - /config/system/config.default.php
 *  
 * This file guarantees compatible settings to old (upgraded) BIGACE versions. 
 */

// ------------------------------ [FALLBACK CONFIGS] ----------------------------
// Timezone of your server, see http://de3.php.net/timezones
if(!defined('BIGACE_TIMEZONE'))     define ('BIGACE_TIMEZONE', date_default_timezone_get());
// whether we use url rewriting or not  
if(!defined('BIGACE_URL_REWRITE'))	define ('BIGACE_URL_REWRITE', (defined('_BIGACE_URL_REWRITE') ? _BIGACE_URL_REWRITE: 'false'));
// Directory of the BIGACE installation, relative from DocumentRoot. Set if BIGACE BaseDir is not your websites basedir.
if(!defined('BIGACE_DIR_PATH'))		define ('BIGACE_DIR_PATH', (defined('_BIGACE_DIR_PATH') ? _BIGACE_DIR_PATH : ''));
// whether BIGACE should use SSL for login/register and administration
if(!defined('BIGACE_USE_SSL'))   	define ('BIGACE_USE_SSL', false);
// Base URLs for creating links to BIGACE applications.
if(!defined('BIGACE_URL_HTTP'))  	define ('BIGACE_URL_HTTP', 'http://'. $_SERVER['HTTP_HOST'] . '/');
// If you use an SSL Proxy, you could change the SSL URL
if(!defined('BIGACE_URL_HTTPS')) 	define ('BIGACE_URL_HTTPS', 'https://'. $_SERVER['HTTP_HOST'] . '/');

// ------------------------------ [DATABASE CONNECTION] ----------------------------
// set to 'latin1' if you upgraded your system
if(!isset($_BIGACE['db']['character-set']))	$_BIGACE['db']['character-set'] = 'utf8';
// 'localhost' will work in almost all environments
if(!isset($_BIGACE['db']['type']))          $_BIGACE['db']['type'] = 'mysql';		
// prefix for the table names (can be empty!)
if(!isset($_BIGACE['db']['prefix']))        $_BIGACE['db']['prefix'] = '';				
	
// compatibility between 2.4 and 2.5
if(!isset($_BIGACE['db']['name']))  $_BIGACE['db']['name'] = $_BIGACE['db']['table'];
if(!isset($_BIGACE['db']['table'])) $_BIGACE['db']['table'] = $_BIGACE['db']['name'];

/**
 * If Statistics should be logged in a different database, uncomment the next lines.
 * 
 * if(!isset($_BIGACE['statistic'])) 
 * {
 *   $_BIGACE['statistic']['type']     = $_BIGACE['db']['type'];
 *   $_BIGACE['statistic']['host']     = $_BIGACE['db']['host'];
 *   $_BIGACE['statistic']['name']     = $_BIGACE['db']['name'];
 *   $_BIGACE['statistic']['user']     = $_BIGACE['db']['user'];
 *   $_BIGACE['statistic']['password'] = $_BIGACE['db']['pass'];
 *   $_BIGACE['statistic']['prefix']   = $_BIGACE['db']['prefix'];
 * }
 */
// ---------------------------------------------------------------------------------

// ------------------------------ [SESSION SETTINGS] --------------------------------
	

/* By default we use the PHP default Session Identifier.
 * You may change it by uncommenting the following line.
 * Allowed setting is: String
 * 
 * $_BIGACE['system']['session_name'] = 'BSID'; 
 */
	
/* By default (and for security) we use browser based sessions. If uncommented, the session 
 * lives the amount of configured seconds, and therefor may exist after you restarted your browser.
 * Allowed setting is: Integer
 * 
 * $_BIGACE['system']['session_lifetime'] = 3600;
 */

// ------------------------------ [LOGGING] ------------------------------
/* If this is not set, log level is fetched from the database
 * Following Log Level would cause to log ALL available information:
 * E_ALL | E_DEBUG | E_SQL;
 * Take care, it increases the Scripts runtime!
 * 
 * Uncomment to get more runtime information:
 * $_BIGACE['log']['level']         = E_ALL | E_DEBUG | E_SQL;
 * $_BIGACE['log']['level_search']  = E_ALL | E_DEBUG | E_SQL;
 */

// ------------------------------ [DEMO VERSION] --------------------------------
/* Defines whether you are running in Demo Mode or normal.  
 * NOTE: Some features will not work in DEMO VERSION for security reason.
 * Only uncomment the following line, if you going to host a Demo Installation!
 * DO NOT RELY ON THIS AS SECURITY LAYER!  
 *  
 * define('_BIGACE_DEMO_VERSION', true); 
 */

// ------------------------------ [SECURITY] ------------------------------
/* This feature is not yet activated, because you have to migrate user table entries.  
 * DO NOT CHANGE! You will not be able to login after changing these values!
 * 
 * the password salt lowers the risk of rainbow attacks 
 * define ('BIGACE_AUTH_SALT', '{AUTH_SALT}');
 * 
 * the password split position used during salting (a random value between 1 and 31)
 * define ('BIGACE_SALT_LENGTH', '{SALT_LENGTH}');
 */
	

// ------------------------------ [DB TUNING] ------------------------------
/**
 * Every Item call results in a Database Select, fetching the specified columns below.
 * Default settings are good for most situations. There might be circumstances where you can improve them!
 * 
 * For required fields, have a look at the Docu of bigace.classes.item.Item.
 * Make sure to include all required fields in the 'full' definition.
 *
 * There are three default TreeTypes:
 * - full (ITEM_LOAD_FULL)
 * - light (ITEM_LOAD_LIGHT)
 * - default (only called if the other ones could not be found)
 * 
 * It is possible to create own defintions and use these in your Layouts...
 * 
 * You pass the treetype in the Item Constructor and in many methods of 
 * the Item Classes. See PHP Doc!
 * 
 * Some example configurations are mentioned below.
 */

/*
$_BIGACE['SELECT']['default']['full']    = 'a.id,a.cid,a.language,a.mimetype,a.name,a.parentid,a.description,a.catchwords,a.createdate,a.createby,a.modifieddate,a.modifiedby,a.workflow,a.text_1,a.text_2,a.text_3,a.text_4,a.text_5,a.num_1,a.num_2,a.num_3,a.num_4,a.num_5,a.date_1,a.date_2,a.date_3,a.date_4,a.date_5';
$_BIGACE['SELECT']['default']['light']   = 'a.id,a.language,a.name,a.parentid,a.description,a.text_1';
$_BIGACE['SELECT']['default']['default'] = 'a.id,a.language,a.name,a.parentid,a.description,a.text_1';
$_BIGACE['SELECT']['default']['custom']  = 'a.id,a.language,a.name';

$_BIGACE['SELECT']['item_1']['full']    = 'a.*';
$_BIGACE['SELECT']['item_1']['light']   = 'a.id,a.language,a.name,a.parentid,a.description,a.text_1,a.text_3,a.text_4';
$_BIGACE['SELECT']['item_1']['default'] = 'a.id,a.language,a.name,a.parentid,a.description,a.text_1';

$_BIGACE['SELECT']['item_4']['full']    = 'a.id,a.cid,a.language,a.mimetype,a.name,a.parentid,a.description,a.catchwords,a.createdate,a.createby,a.modifieddate,a.modifiedby,a.workflow,a.text_1,a.text_2,a.text_3,a.text_4,a.text_5,a.num_1,a.num_2,a.num_3,a.num_4,a.num_5,a.date_1,a.date_2,a.date_3,a.date_4,a.date_5';
$_BIGACE['SELECT']['item_4']['light']   = 'a.id,a.language,a.name,a.parentid,a.description,a.text_1';
$_BIGACE['SELECT']['item_4']['default'] = 'a.id,a.language,a.name,a.parentid,a.description,a.text_1';

$_BIGACE['SELECT']['item_5']['full']    = 'a.id,a.cid,a.language,a.mimetype,a.name,a.parentid,a.description,a.catchwords,a.createdate,a.createby,a.modifieddate,a.modifiedby,a.workflow,a.text_1,a.text_2,a.text_3,a.text_4,a.text_5,a.num_1,a.num_2,a.num_3,a.num_4,a.num_5,a.date_1,a.date_2,a.date_3,a.date_4,a.date_5';
$_BIGACE['SELECT']['item_5']['light']   = 'a.id,a.language,a.name,a.parentid,a.description,a.text_1';
$_BIGACE['SELECT']['item_5']['default'] = 'a.id,a.language,a.name,a.parentid,a.description,a.text_1';
*/
	
?>