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
 * @package bigace.api
 * @subpackage job
 */

define('AUTOJOB_STOPPED', '0');
define('AUTOJOB_CORRUPT', '10');
define('AUTOJOB_FAILURE', '20');
define('AUTOJOB_RUNNING', '30');

/**
 * This represents an executable AutoJob, which can be runned
 * at Program Startup in a defined time-cycle.
 * 
 * State:
 *  0  = STOPPED (manually stopped from the user, no more execution until reactivation)
 * 10  = CORRUPT (corrupt, stopped until reactivation)
 * 20  = FAILURE (failure during last execution, will be tried next time)
 * 30  = RUNNING (normal state, running)
 * 
 * Developer should return a useful message in getMessage() if anything went wrong!
 *  
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.api
 * @subpackage job
 */
class AutoJob
{
	/**
     * Returns the Name (unique identifier) of this AutoJob. 
	 * @return String the AutoJob Name
	 */
    function getName() {
        return "";
    }

    /**
     * Returns the Description of this AutoJob. 
	 * @return String the Template Description
     */
    function getDescription() {
    	return "";
    }
    
    /**
     * Calculates and returns the next execution time as Unix Timestamp.
     */
    function getNextExecution() {
    	return 0;
    }
	   
	/**
	 * If your AutoJob requires further configuration, you should
	 * return the configs package name.
	 * This config package will be cached and be editable in the 
	 * Administration.
	 */
	function getConfigPackage() {
		return null;
	}
	
	/**
	 * @return int the state of this and future executions
	 * @see AUTOJOB_STOPPED, AUTOJOB_CORRUPT, AUTOJOB_FAILURE, AUTOJOB_RUNNING
	 */
	function execute() {
		return AUTOJOB_CORRUPT;
	}
	
	/**
	 * Return a log message at least if the execution task failed.
	 * This log message will be stored for later accessability.
	 */
	function getMessage() {
		return "";
	}
	
}
