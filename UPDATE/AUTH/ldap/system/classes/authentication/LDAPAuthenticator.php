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

import('api.authentication.Authenticator');
import('classes.authentication.DefaultAuthenticator');
import('classes.principal.DefaultPrincipal');
import('classes.principal.DefaultPrincipalService');

/**
 * The LDAPAuthenticator uses an LDAP for authentication.
 * 
 * Please edit the file /system/config/ldap.php instead of setting variables in here!
 * 
 * TODO
 * Set user attribute after creation => "ldap user"
 * 
 * Now, if user logs in and fails against ldap, check if user exists locally and if so:
 * deactivate that user + send email to administrator
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage authentication
 */
class LDAPAuthenticator extends DefaultAuthenticator
{

  private $_account_prefix = "cn=";
  private $_account_suffix = "";
  private $_base_dn = ""; 

  // array of ids with bigace groupd ids to assign for new created user
  private $_default_groups = array("0");
  private $_group_mappings = array();

  // An array of domain controllers. Specify multiple controllers if you 
  // would like the class to balance the LDAP queries amongst multiple servers
  private $_domain_controllers = array ("localhost");
  
  // optional account with higher privileges for searching
  // not really that optional because you can't query much as a user
  private $username=NULL;
  private $password=NULL;

  // some values depending on the used schema
  private $_objectclass='person';
  private $_group_attribute='gidNumber';
  
  // want to authenticate against bigace if ldap failed?
  private $_auth_local=false;
  
  // Use SSL, your server needs to be setup
  private $_use_ssl=true;
  private $_prot_version=3;
  
  // You should not need to edit anything below this line
  //******************************************************************************************
  
  //other variables
  private $_conn;
  private $_bind;
  
    function LDAPAuthenticator()
    {
      // ----------------------------------------------------------------------------
      // FETCH LDAP CONFIGURATION
      include(_BIGACE_DIR_ROOT . '/system/config/ldap.php');
    
    if(!isset($LDAPCONF))
      die("FATAL: LDAP is not configured, check /system/config/ldap.php.");
    
    if(!is_array($LDAPCONF))
      die("FATAL: LDAP config is wrong, check /system/config/ldap.php.");
      
    $options = $LDAPCONF;
    
      $GLOBALS['LOGGER']->logDebug('LDAP Options: ' . print_r($options,true));
      // ----------------------------------------------------------------------------

    if(count($options) > 0) 
    {
      if (array_key_exists("objectclass",$options)){ $this->_objectclass=$options["objectclass"]; }
      if (array_key_exists("account_prefix",$options)){ $this->_account_prefix=$options["account_prefix"]; }
      if (array_key_exists("account_suffix",$options)){ $this->_account_suffix=$options["account_suffix"]; }
      if (array_key_exists("base_dn",$options)){ $this->_base_dn=$options["base_dn"]; }
      if (array_key_exists("domain_controllers",$options)){ $this->_domain_controllers=$options["domain_controllers"]; }
      if (array_key_exists("username",$options)){ $this->username=$options["username"]; }
      if (array_key_exists("password",$options)){ $this->password=$options["password"]; }
      if (array_key_exists("use_ssl",$options)){ $this->_use_ssl=$options["use_ssl"]; }
      if (array_key_exists("protocal_version",$options)){ $this->_prot_version=$options["protocal_version"]; }
      if (array_key_exists("auth_local",$options)){ $this->_auth_local=$options["auth_local"]; }
      if (array_key_exists("default_groups",$options)){ $this->_default_groups=$options["default_groups"]; }
      if (array_key_exists("group_attribute",$options)){ $this->_group_attribute=$options["group_attribute"]; }
      if (array_key_exists("group_mappings",$options)){ $this->_group_mappings=$options["group_mappings"]; }
    }
    
    unset($LDAPCONF);
  
    //connect to the LDAP server with the username/password
    //select a random domain controller
    mt_srand(doubleval(microtime()) * 100000000); // for older php versions
    $dc = ($this->_domain_controllers[array_rand($this->_domain_controllers)]);
    if ($this->_use_ssl){
      $this->_conn = ldap_connect("ldaps://".$dc);
    } else {
      $this->_conn = ldap_connect($dc);
    }
    
    //set some ldap options
    ldap_set_option($this->_conn, LDAP_OPT_PROTOCOL_VERSION, $this->_prot_version);
    
    //bind as a domain admin if they've set it up
    if ($this->username != NULL && $this->password != NULL){
      $this->_bind = @ldap_bind($this->_conn,$this->username,$this->password);
      if (!$this->_bind){
        if ($this->_use_ssl){
          echo ("FATAL: Bind failed. Either the LDAPS connection failed or the login credentials are incorrect."); exit();
        } else {
          echo ("FATAL: Bind failed. Check the login credentials."); exit();
        }
      }
    }
    
    return (true);    
    }
  
    function __destruct(){ 
      ldap_close ($this->_conn); 
    }

    /**
     * Performs a login against the LDAP server.
     * @return mixed the error flag or a Principal is returned
     */
    public function authenticate($name = NULL, $password = NULL) {
        if ( is_null($name) || is_null($password) || strlen ($name) < 1 ) {
            return AUTHENTICATE_UNKNOWN;
        }

    if($this->authenticate_ldap($name,$password,true)) {

      // okay, user was identified, no lookup in bigace repository
      $dps = new DefaultPrincipalService();
      $prince = $dps->lookup($name);

      // if user can not be found, create it
      if(is_null($prince)) {
        
        $prince = $dps->createPrincipal($name, $password, $GLOBALS['_BIGACE']['DEFAULT_LANGUAGE']);
        
        if($prince === false) {
            $GLOBALS['LOGGER']->logError('Failed creating local user for LDAP login: '.$name);
          return AUTHENTICATE_UNKNOWN;
        }
      }

      // group mappings will be changed every time a user logs in
      if ((is_array($this->_default_groups) && count($this->_default_groups)>0) || 
        (is_array($this->_group_mappings) && count($this->_group_mappings)>0)) {

        import("classes.group.GroupAdminService");
        $gas = new GroupAdminService();

        // add user to thedefault groups (normally at least anonymous)
        $gas->removeAllMemberships($prince->getID());
        foreach($this->_default_groups AS $groupID)
          $gas->addToGroup($groupID, $prince->getID());

        // now, see if we should map local groups against ldap groups
        if(count($this->_group_mappings) > 0) {
          $temp = $this->user_info($name, array($this->_group_attribute));
          if(count($temp) > 0 && isset($temp[0][strtolower($this->_group_attribute)][0])) {
            $gid = $temp[0][strtolower($this->_group_attribute)][0];
            foreach($this->_group_mappings AS $groupID => $bigaceID) {
              if($groupID == $gid) {
                $gas->addToGroup($bigaceID, $prince->getID());
              }
            }
          }
        }
      }
      
      return $prince;
    }
    
    if($this->_auth_local) {
      return parent::authenticate($name, $password);
    }

    return AUTHENTICATE_UNKNOWN;
    }

    /**
     * Authenticate against the LDAP.
     *
     * @param string $username
     * @param string $password
     * @param boolean $prevent_rebind
     * @return boolean whether the authentication worked or not
     */
  private function authenticate_ldap($username,$password,$prevent_rebind=false){
    if ($username==NULL || $password==NULL){ return (false); } //prevent null binding

    
    $uid = $this->_account_prefix.$username.$this->_account_suffix;
    $GLOBALS['LOGGER']->logDebug('LDAP authenticate with: '.$username.' => ' . $uid);
    
    //bind as the user
    $this->_bind = @ldap_bind($this->_conn,$uid,$password);
    if (!$this->_bind){ return (false); }

    //once we've checked their details, back into admin mode
    if ($this->username!=NULL && !$prevent_rebind){
      $this->_bind = @ldap_bind($this->_conn,$this->username.$this->_account_suffix,$this->password);
      if (!$this->_bind){ die("FATAL: Rebind failed."); } //this should never happen in theory
    }
    
    return (true);
  }

  /**
   * Find user information.
   *
   * @param unknown_type $username
   * @param unknown_type $fields
   * @return unknown
   */
  function user_info($username,$fields=NULL){
    if ($username==NULL){ return (false); }
    if (!$this->_bind){ return (false); }

    $filter="(&(objectclass=".$this->_objectclass.")(".$this->_account_prefix.$username."*))";
    if(!is_null($fields))
      $sr=ldap_search($this->_conn,$this->_base_dn,$filter,$fields);
    else
      $sr=ldap_search($this->_conn,$this->_base_dn,$filter);

    $entries = ldap_get_entries($this->_conn, $sr);
    
    return ($entries);
  }
}

?>