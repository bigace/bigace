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
 * @package bigace.classes
 * @subpackage authentication
 */

if(!defined('_BIGACE_ID'))
	die('BIGACE is not running!');

/**
 * This is an example of a complete configuration for your LDAP Authenticator:
 * 
 * $LDAPCONF = array(
 *   "objectclass"			=> 'person',
 * 	 "account_prefix" 		=> 'cn=',
 *   "account_suffix" 		=> ',cn=test,dc=bigace',
 * 	 "base_dn" 				=> 'dc=bigace',
 * 	 "domain_controllers" 	=> array ("localhost"),
 * 	 "username" 			=> 'cn=admin,dc=bigace',
 * 	 "password" 			=> 'secret',
 * 	 "use_ssl" 				=> true,
 * 	 "protocal_version" 	=> 3,
 * 	 "auth_local"			=> false,
 *	 "default_groups"		=> array("0"),
 *	 "group_attribute"		=> "gidNumber",
 *   "group_mappings"		=> array("1002" => "20", "1005" => "40"),
 * );
 * ------------------------------------------------------------------------------------
 * Followed by a minimal configuration:
 * 
 * $LDAPCONF = array(
 *   "account_suffix" 		=> ',cn=test,dc=bigace',
 * 	 "base_dn" 				=> 'dc=bigace',
 * 	 "username" 			=> 'cn=admin,dc=bigace',
 * 	 "password" 			=> 'secret'
 * );
 * ------------------------------------------------------------------------------------
 * Description of the config values:
 *
 *  - objectclass			Default: person
 * 							The class to be used for querying user information.
 *  
 *  - account_prefix		Default: cn= (used for all objects of type "person")
 *							Prefix to prepend to the username search string
 * 
 *  - account_suffix		Suffix to append to the username search string
 * 
 *  - base_dn				The base tree of your LDAP queries
 * 				
 *  - domain_controllers	Default: localhost
 * 							Array of possible host controller. Leave as is if you are unsure.
 * 
 *  - username				Administrator username, used for initiating connection
 * 
 *  - password				Administrator password
 * 
 *  - use_ssl				Default: true / Recommended: true
 * 							Wheter to use ldaps:// for login or plain text
 * 
 *  - protocal_version		Default: 3 / Recommended: 3
 * 							The LDAP version to speak (nowadays 3 should be commonly used).
 * 
 *  - auth_local			Default: false / Recommended: false
 * 							Use true ONLY(!) for testing purpose. If you activate this, your user might be able to login 
 * 							at bigace, even if they are deactivated in your LDAP (if you didn't delete them in bigace)!
 * 
 *  - default_groups		Default: array("0")
 * 							Array of BIGACE Group IDs, which will be assigned to new created users
 * 
 *  - group_attribute		Default: gidNumber 
 * 							Name of the attribute to be used for group matchings. 
 * 							This string is only  used if the parameter group_mappings is set.
 * 
 *  - group_mappings		Default: array() 
 *  						Mapping between LDAP and BIGACE Group IDs
 *   
 * If you need Community dependend configs, see example at the bottom! 
 */

$LDAPCONF = array (
	"objectclass"			=> 'person',
	"account_prefix" 		=> 'cn=',
	"account_suffix" 		=> ',cn=test,dc=bigace',
	"base_dn" 				=> 'dc=bigace',
	"domain_controllers" 	=> array ("localhost"),
	"username" 				=> 'cn=admin,dc=bigace',
	"password" 				=> 'secret',
	"use_ssl" 				=> false,
	"protocal_version" 		=> 3,
 	"auth_local" 			=> true,
	"default_groups"		=> array("0"),
	"group_attribute"		=> "gidNumber",
	"group_mappings"		=> array("1002" => "35", "1003" => "40"),
);

/**
 * HOW TO USE AND READ COMMUNITY DEPENDEND SETTINGS
 * ================================================================================
 *  
 * This first example demonstrates how to read all values from database 
 * configurations. Leave the above config as is and then uncomment the next 
 * three lines of code:
 * 
 * foreach($LDAPCONF AS $k => $v) {
 *   $LDAPCONF[$k] = ConfigurationReader::getConfigurationValue('ldap', $k, $v);
 * }
 *
 * $LDAPCONF["domain_controllers"] = explode(",", $LDAPCONF["domain_controllers"]); 
 * $LDAPCONF["default_groups"] = explode(",", $LDAPCONF["default_groups"]); 
 * 
 * TODO
 * explode() "group_mappings" by , and | and make array of it.
 * "1002|35,1003|40" = array("1002" => "35", "1003" => "40")
 * 
 * ================================================================================
 * 
 * The next example demonstrates how to set default values for main config (like 
 * controller, admin username/password, ssl and protocal version) and how to read 
 * only the remaining values from database configs.
 * 
 * $LDAPCONF = array(
 * 	 "objectclass"			=> 'person',
 * 	 "account_prefix" 		=> 'cn=',
 *   "account_suffix" 		=> ',cn=test,dc=bigace',
 * 	 "base_dn" 				=> 'dc=bigace'
 *	 "group_attribute"		=> "gidNumber",
 * );
 *
 * foreach($LDAPCONF AS $k => $v) {
 *   $LDAPCONF[$k] = ConfigurationReader::getConfigurationValue('ldap', $k, $v);
 * }
 *
 * // these values will be valid for all communities, no matter what they configure
 * $temp = array(
 *   "domain_controllers" 	=> array ("localhost"),
 * 	 "username" 			=> 'root',
 * 	 "password" 			=> 'root',
 *   "use_ssl" 				=> true,
 * 	 "protocal_version" 	=> 3
 *	 "auth_local" 			=> false,
 *	 "default_groups"		=> array("0"),
 *   "group_mappings"		=> array("1002" => "35", "1003" => "40"),
 * );
 * 
 * $LDAPCONF = array_merge($LDAPCONF, $temp);
 * 
 * ================================================================================
 * You can configure default values above and just overwrite the required values, like base_dn.
 *  
 */


?>