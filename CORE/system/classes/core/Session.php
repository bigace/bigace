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
 * @subpackage core
 */

import('classes.core.ServiceFactory');
import('classes.language.Language');

/**
 * Represents a BIGACE Session, which initializes values like Community ID, User ID and language.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage core
 */
class Session
{
    /**
     * @access private
     */
    private $USER;
    /**
     * @access private
     */
    private $id;
    /**
     * @access private
     */
    private $community;

    function Session($community)
    {
        bigace_session_gc();
        
        $this->community = $community;
        $cid = $this->community->getID();        
        
		if(bigace_session_started()) {
		    $this->id = bigace_session_id();
		    if($this->id == '') {
		        session_regenerate_id();
		        $this->id = bigace_session_id();
		    }

		    $sessionCID = $this->getSessionValue("BIGACE_SESS_CID");
		    // Fetch Community ID and Community
		    if($sessionCID == null) {
		        $this->setSessionValue("BIGACE_SESS_CID", $cid);
		    } 
		    else if ($sessionCID != $cid) {
		        //die('Session Hijacking?! Rejecting Session ID: '.$id.', Session CID: ' . $sessionCID . ', real CID: ' . $cid);
		        $this->destroy();
		        $this->finalize();
		    }
		}

        // Set the User ID
        $uid = ($this->isValueSet("BIGACE_SESS_UID")) ? $this->getSessionValue("BIGACE_SESS_UID") : _AID_;
        $this->setUserByID($uid);
        unset($uid);
    }
    
    /**
     * Fetch the Consumer this Session belongs to.
     * @return Consumer the Consumer for this Session
     * @deprecated use getCommunity()
     */
    function getConsumer() {
        return $this->community;
    }
    /**
     * Fetch the Community for this Session.
     * @return Consumer the Community for this Session
     */
    function getCommunity() {
        return $this->community;
    }
    
    /**
     * Sets the User by its ID.
     * @param int the User ID
     */
    function setUserByID($uid)
    {
		if($uid != _AID_) {
			$this->setSessionValue("BIGACE_SESS_UID", $uid);
		}
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();
        $this->USER = $PRINCIPALS->lookupByID($uid);
    }
    
    /**
     * Checks if the a Value with the given key is set within this Session.
     * @param String the Key foe the searched Value
     * @return boolean whether the Key is set or not
     */
    function isValueSet($key) {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Sets the given Key-Value Combination for this Session.
     * The Value must be serializable using the PHP method <code>serialize()</code>!
     *  
     * @param String the Key for this Session value
     * @param mixed the Value to set
     */
    function setSessionValue($key, $value) {
		if(!bigace_session_started()) 
			bigace_session_start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Returns the Session Value with the given Key.
     * If none could be found, it returns null.
     * @param String the Key of the searched Value
     * @return mixed the Value or null 
     */
    function getSessionValue($key)
    {
        if (!isset($_SESSION[$key])) {
            return null;
        }
        return $_SESSION[$key];
    }
    
    /**
     * Sets the Language (Locale) of this Session. 
     * The Parameter may not be a Language class, but the Locale String (Language->getLocale()).
     * 
     * @param Language the Language Locale to be set 
     */
    function setLanguage($lang) {
        if (!is_object($lang)) {
            $this->setSessionValue( "BIGACE_SESS_LOC", $lang );
        } else {
            // TODO deprecated! support should be removed
			if(strcasecmp(get_class($lang),'Language') == 0  || is_subclass_of($lang, 'Language'))
            	$this->setSessionValue( "BIGACE_SESS_LOC", $lang->getLocale() );
        }
    }

    /**
     * Returns the Session Language ID. 
     * This can be different from the System Default language and also different
     * from the Users Language!
     * @return String the Language ID, here the Locale
     */
    function getLanguageID() {
        return $this->getSessionValue("BIGACE_SESS_LOC");
    }
    
    /**
     * Returns the ID of the User.
     * @return int the User ID
     */
    function getUserID() {
        return $this->USER->getID();
    }
   
    /**
     * Returns the actual User.
     * @return Principal the Principal using this Session
     */
    function getUser() {
        return $this->USER;
    }

    /**
     * Return if the current user is a SuperUser.
     * @return boolean true if current user is the "community good"
     */
    function isSuperUser() {
	    return $this->USER->isSuperUser();
    }
    /**
     * Returns if the current Session is a anonymous Session.
     * @return boolean whether we use an anonymous session or not
     */
    function isAnonymous() {
        return $this->USER->isAnonymous();
    }

    /**
     * Returns the Session ID.
     * @return String the Session ID
     */
    function getSessionID() {
        return $this->id;
    }
    
    /**
     * Destroy the current session, including the delete from database.
     * @param boolean whether the cookie should be kept or deleted 
     */
    function destroy($deleteCookie = true) {
		if($deleteCookie && isset($_COOKIE))
			SetCookie(bigace_session_name(),"",time()-365*24*60*60,"/"); 
    	session_unset();
        session_destroy();
        bigace_session_destroy( $this->getSessionID() );
		$this->id = '';
		session_id('');
		// fix empty session id and generate new one to make sure 
		// to fetch a new session
		//session_id( substr(getRandomString(),0,26) );
    }
    
    /**
     * Writes the Session Data into the Database for persistence issues.
     */
    function finalize() {
        bigace_session_write( $this->getSessionID(), $_SESSION );
    }

}

/**
 * unused dummy implementation.
 * @access private
 */
function bigace_session_open($path, $name) { return true; }

/**
 * unused dummy implementation.
 * @access private
 */
function bigace_session_close() { return true; }


/**
 * Read session data from Database.
 * @param String the Session ID to get the Data for
 * @return String the Session Data
 */
function bigace_session_read($id) 
{
    $values = array( 'SESSION_ID' => $id );
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('session_read');
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
    $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    if (!$temp->isError()) {
        $temp = $temp->next();
        return($temp["data"]);
    }
    return '';
}

/**
 * Writes Data for a Session.
 * @param String id the Session ID 
 * @param mixed data the Session Data
 * @access private
 */
function bigace_session_write($id, $data) 
{
    if (isset($GLOBALS['_BIGACE']['SESSION']))
    {
        $ip = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
        $values = array( 'SESSION_ID'        => $id,
                         'SESSION_CID'       => _CID_,
                         'SESSION_DATA'      => $data,
                         'SESSION_TIMESTAMP' => time(),
                         'SESSION_IP'        => $ip,
                         'SESSION_USER'      => $GLOBALS['_BIGACE']['SESSION']->getUserID(),
     	);
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('session_write');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        return !$temp->isError();
    } 

    // if no community is installed
    return false;
}

/**
 * Deletes the Session with the given ID.
 * @param String id the Session ID 
 */
function bigace_session_destroy($id) 
{
    $values = array( 'SESSION_ID' => $id );
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('session_destroy');
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
    $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    return $temp->isError();
}

/**
 * Deletes all Sessions with last refresh timestamp older than the
 * configured Session lifetime.
 * The Parameter is ignored, instead the value is fetched from 
 * <code>$GLOBALS['_BIGACE']['system']['session_lifetime']</code>!
 * @param long maxLifeTime the Session lifetime in seconds 
 */
function bigace_session_gc($maxLifeTime = NULL) {
    return true;
}

/**
 * Gets the Session ID.
 * @return String the Session ID
 */
function bigace_session_id() {
    return session_id();
}

/**
 * Gets the Session Name.
 * @return String the Session Name
 */
function bigace_session_name() {
    return session_name();
}

function bigace_session_started() {
	return (bigace_session_id() != '');
}

function bigace_session_start() {
	//set our own read/write routines
	session_set_save_handler ("bigace_session_open", "bigace_session_close", "bigace_session_read", "bigace_session_write", "bigace_session_destroy", "bigace_session_gc");

	// start the session (catch E_NOTICE - PHP 4.3.3 - if already running)
	session_start();
}

?>