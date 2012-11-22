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
import('classes.news.News');
import('classes.item.ItemRequest');
import('classes.item.SimpleItemTreeWalker');
import('classes.menu.MenuService');
import('classes.menu.MenuAdminService');

/**
 * This AutoJob moves all out-dated entries to a 
 * configurable news archive.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage job
 */
class NewsArchiver extends AutoJob
{
	var $msg = "";
	
	/**
     * Returns the Name (unique identifier) of this AutoJob. 
	 * @return String the AutoJob Name
	 */
    function getName() {
        return "News Archive";
    }

    /**
     * Returns the Description of this AutoJob. 
	 * @return String the Template Description
     */
    function getDescription() {
    	return "Moves old News to the Archive.";
    }
    
    /**
     * Next description is always the next day at 01:00:00.
     *
     * @return int the timestamp of the next execution
     */
    function getNextExecution() {
    	return mktime(1, 0, 0, date("m"), date("d") + 1, date("Y"));
    }

	/**
	 * Returns the config package for the archiver
	 */
	function getConfigPackage() {
		return "news";
	}
	
	/**
	 * @return int the state of this and future runs
	 */
	function execute() 
	{
		$confPack = $this->getConfigPackage();
		// -------------------------------- CONFIGURATIONS --------------------------------
		$rootID = ConfigurationReader::getConfigurationValue($confPack, 'root.id', null);
		if($rootID == null) {
			$this->msg = "No News Root-ID configured in " . $confPack . "/root.id";
			return AUTOJOB_FAILURE;
		}
		
		$archiveID = ConfigurationReader::getConfigurationValue($confPack, 'archive.id', null);
		if($archiveID == null) {
			$this->msg = "No Archive-ID configured in " . $confPack . "/archive.id";
			return AUTOJOB_FAILURE;
		}
		
		$archiveYearTemplate = ConfigurationReader::getConfigurationValue($confPack, 'template.archive.year', null);
		if($archiveYearTemplate == null) {
			$this->msg = "No Template for News Archive Year configured in " . $confPack . "/template.archive.year";
			return AUTOJOB_FAILURE;
		}
		
		$archiveMonthTemplate = ConfigurationReader::getConfigurationValue($confPack, 'template.archive.month', null);
		if($archiveMonthTemplate == null) {
			$this->msg = "No Template for News Archive Month configured in " . $confPack . "/template.archive.month";
			return AUTOJOB_FAILURE;
		}

		$archiveAgeDays = ConfigurationReader::getConfigurationValue($confPack, 'archive.min.age.days', null);
		if($archiveAgeDays == null) {
			$this->msg = "No age (days) for archive configured in " . $confPack . "/archive.min.age.days. Set 0 if you use the 'month' setting instead.";
			return AUTOJOB_FAILURE;
		}
		
		$archiveAgeMonth = ConfigurationReader::getConfigurationValue($confPack, 'archive.min.age.month', null);
		if($archiveAgeMonth == null) {
			$this->msg = "No age (month) for archive configured in " . $confPack . "/archive.min.age.month. Set 0 if you use the 'days' setting instead.";
			return AUTOJOB_FAILURE;
		}
		// --------------------------------------------------------------------------------
		
		
		// find items to be moved
		$ms = new MenuService();

		// all items that must be moved to the archive 
		$itemsToMove = array();
		
		$root = $ms->getMenu($rootID);
		$archive = $ms->getMenu($archiveID);

		$treeReq = new ItemRequest(_BIGACE_ITEM_MENU, $rootID);
		$treeReq->setTreetype(ITEM_LOAD_LIGHT);
		$treeReq->setReturnType('News');
		$tree = new SimpleItemTreeWalker($treeReq);
//		$tree = $ms->getLightTree($rootID);

		// check which items should be moved
		while(($child = $tree->next()) !== FALSE) {
			$ts = $child->getDate();
			
			// TODO add different configurable methods
			$minTime = mktime(0,0,0,date("m")-$archiveAgeMonth,date("d")-$archiveAgeDays,date("Y"));
			$GLOBALS['LOGGER']->logDebug("News Archiver: check item " . $child->getID() . ' ('.$ts.'/'.$minTime.')');
			
			if($ts < $minTime) {
				$itemsToMove[] = $child;
			}
		}
		
		// there are items to move, so do it...
		if(count($itemsToMove) > 0)
		{
			// needed for further manipulation
			$menuAdmin = new MenuAdminService();
			
			$tempIDs = array();
			
			$yearsReq = new ItemRequest(_BIGACE_ITEM_MENU, $archiveID);
			$yearsReq->setFlagToExclude($yearsReq->FLAG_ALL_EXCEPT_TRASH);
			$yearsReq->setTreetype(ITEM_LOAD_LIGHT);
			
			$years = new SimpleItemTreeWalker($yearsReq);
			//$years = $ms->getLightTree($archiveID);
			
			while(($year = $years->next()) !== FALSE) {
				$tempIDs[$year->getName()] = $year->getID(); 
			}
			
			$result = true;
			
			foreach($itemsToMove AS $item) 
			{
				$ts = $item->getDate();

				$year = date("Y", $ts);
				$yearID = (isset($tempIDs[$year]) ? $tempIDs[$year] : null);

				// if year not alreay exists, create it and remember ID
				if($yearID == null) {
					// lege jahres verzeichniss an
					$data = $menuAdmin->createDataArrayForParent($archive);
					$data['name'] = $year;
					$data['description'] = 'News Archive for Year ' . $year;
					$data['text_4'] = $archiveYearTemplate;

					$yearID = $menuAdmin->createMenu($data);
					$tempIDs[$year] = $yearID; 
				}
				
				// the month we are looking for
				$monthID = (isset($tempIDs[date("Y:m", $ts)]) ? $tempIDs[date("Y:m", $ts)] : null);
				
				// find month if not already set
				if($monthID == null) 
				{
					$monthsReq = new ItemRequest(_BIGACE_ITEM_MENU, $yearID);
					$monthsReq->setFlagToExclude($yearsReq->FLAG_ALL_EXCEPT_TRASH);
					$monthsReq->setTreetype(ITEM_LOAD_LIGHT);
					
					$months = new SimpleItemTreeWalker($monthsReq);
					//$months = $ms->getLightTree($yearID);
					
					while(($month = $months->next()) !== FALSE) {
						$tempIDs[date("Y:m", $ts)] = $month->getID(); 
					}

					// retry if month exists
					$monthID = (isset($tempIDs[date("Y:m", $ts)]) ? $tempIDs[date("Y:m", $ts)] : null);
					
					// double check, if not found, create it!
					if($monthID == null) {
						$yearObj = $ms->getMenu($yearID);
						$data = $menuAdmin->createDataArrayForParent($yearObj);
						$data['name'] = date("m", $ts);
						$data['description'] = 'News Archive for Month ' . date("F");
						$data['text_4'] = $archiveMonthTemplate;
						
						$monthID = $menuAdmin->createMenu($data);
						$tempIDs[date("Y:m", $ts)] = $monthID; 
					}
					
				}
				
				if(!$menuAdmin->moveItem($item->getID(), $monthID)) {
					$result = FALSE;
					$this->msg = "Failed moving Item " . $item->getID() . " to monthly Archive " . $monthID;
				} 
				else {
					$GLOBALS['LOGGER']->logDebug("News Archiver: moved item " . $item->getID() . ' to ' . $monthID);
				}
			}
			
			if($result) {
				$this->msg = "moved " . count($itemsToMove) . " entr".(count($itemsToMove) == 1 ? "y" : "ies")." to archive";
				return AUTOJOB_RUNNING;
			}
			else {
				return AUTOJOB_FAILURE;
			}
		}
		
	    return AUTOJOB_RUNNING;
   	}
   	
	/**
	 * Return a log message at least if the execution task failed.
	 * This log message will be stored for later accessability.
	 */
	function getMessage() {
		return $this->msg;
	}
}
