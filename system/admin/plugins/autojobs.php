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
 * @package bigace.administration
 */

/**
 * Handles the AutoJob feature.
 */

check_admin_login();
admin_header();

import('api.job.AutoJob');

$smarty = getAdminSmarty();
$smarty->assign('RUN_RESULT', array());

function fetchJobByName($name)
{
	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('autojob_select_single');
	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array('NAME' => $name), true);
	$runJob = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	if($runJob->count() > 0) 
		return $runJob->next();
	return null;
}

// Save Job
if(isset($_POST['jobID']) && isset($_POST['state']) && isset($_POST['mode']) && $_POST['mode'] == 'save')
{
	$runJob = fetchJobByName($_POST['jobID']);
	if($runJob != null) 
	{
		$state = $_POST['state'];
		if($state == AUTOJOB_STOPPED || $state == AUTOJOB_CORRUPT || $state == AUTOJOB_FAILURE || $state == AUTOJOB_RUNNING) 
		{
			$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('autojob_update');
			$vals = array('ID' => $runJob['name'], 'NEXT' => $runJob['next'], 
						  'LAST' => $runJob['last'], 'STATE' => $state, 'MESSAGE' => $runJob['message']);
			$GLOBALS['_BIGACE']['SQL_HELPER']->execute( $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $vals, true) );
		}
	}
}

// Manual execution of a Job
if(isset($_POST['jobID']) && isset($_POST['mode']) && $_POST['mode'] == 'execute')
{
	$runJob = fetchJobByName($_POST['jobID']);
	if($runJob != null) 
	{		
		$temp_c = createInstance($runJob['classname']);
		if(is_class_of($temp_c, 'AutoJob')) {
			$state = $temp_c->execute();
			$msg = $temp_c->getMessage();
			$nextTS = $runJob['next'];
			
			if(isset($_POST['recalculate']) && $_POST['recalculate'] == 1) {
				$nextTS = $temp_c->getNextExecution();
			}
			
			$execState = array(
				'name'			=> $temp_c->getName(),
				'description'	=> $temp_c->getDescription(),
				'state' 		=> $state,
				'message'		=> $msg
			);
			$smarty->assign('RUN_RESULT', $execState);
			// save last run values
			$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('autojob_update');
			$vals = array('ID' => $runJob['name'], 'NEXT' => $nextTS, 
						  'LAST' => time(), 'STATE' => $state, 'MESSAGE' => $msg);
			$GLOBALS['_BIGACE']['SQL_HELPER']->execute( $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $vals, true) );
		}
        else {
            // TODO translate
            displayError("Cron Job corrupt: " . $_POST['jobID'] . "/" . $runJob['classname']);
    		$GLOBALS['LOGGER']->logError('Corrupt AutoJob with ID: ' . $_POST['jobID']);
        }
	}
	else
	{
		$GLOBALS['LOGGER']->logError('Could not run AutoJob with ID: ' . $_POST['jobID']);
	}
}

// load all available Jobs
$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('autojob_select_all');
$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array('TIME' => time()), true);
$jobs = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

$allJobs = array();
for($i = 0; $i < $jobs->count(); $i++) 
{
	$temp = $jobs->next();
	$temp_c = createInstance($temp['classname']);
    if(is_class_of($temp_c, 'AutoJob')) {
        $allJobs[] = array(
            'ID'            => $temp['name'], 
            'CLASS'         => $temp['classname'], 
            'NAME'          => $temp_c->getName(), 
            'DESCRIPTION'   => $temp_c->getDescription(), 
            'NEXT'          => $temp['next'], 
            'LAST'          => $temp['last'], 
            'STATE'         => $temp['state'], 
            'MESSAGE'       => $temp['message']
        );
    }
    else {
        $allJobs[] = array(
            'ID'            => $temp['name'], 
            'CLASS'         => $temp['classname'], 
            'NAME'          => $temp['classname'], 
            'DESCRIPTION'   => "-", 
            'NEXT'          => $temp['next'], 
            'LAST'          => $temp['last'], 
            'STATE'         => (($temp['state'] == AUTOJOB_RUNNING) ? AUTOJOB_CORRUPT : $temp['state']), 
            'MESSAGE'       => "Entry is corrupt, check config and class." // TODO translate
        );
    }
}

$smarty->assign('AUTO_JOBS', $allJobs);
$smarty->display('AutoJobs.tpl');

unset($smarty);

admin_footer();