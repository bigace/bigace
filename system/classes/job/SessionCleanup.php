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
 * @subpackage job
 */

import('api.job.AutoJob');

/**
 * This AutoJob cleans up all Session that are timed out.
 * You can configure the time by setting the config key:
 * $_BIGACE['system']['session_lifetime']
 * 
 * If not time is configured, all session older than 24 hours will be deleted.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage job
 */
class SessionCleanup extends AutoJob
{

	/**
     * Returns the Name (unique identifier) of this AutoJob. 
	 * @return String the AutoJob Name
	 */
    function getName() {
        return "Session Cleanup";
    }

    /**
     * Returns the Description of this AutoJob. 
	 * @return String the Template Description
     */
    function getDescription() {
    	return "Cleans up all timed-out Session.";
    }
    
    function getNextExecution() {
    	return time() + 120;
    }

	/**
	 * null for no required config, otherwise a String
	 */
	function getConfigPackage() {
		return null;
	}
	
	/**
	 * @return whether the execution was OK or FAILED
	 */
	function execute() {
		 // 12h if nothing is configured
		$lifetime = (isset($GLOBALS['_BIGACE']['system']['session_lifetime']) ? $GLOBALS['_BIGACE']['system']['session_lifetime'] : 86400);
	    $values = array( 'SESSION_LIFETIME' => (time() - $lifetime) );
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('session_gc');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
	    $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	    if($temp->isError())
	    	return AUTOJOB_FAILURE;
	    return AUTOJOB_RUNNING;
   	}
	
}
