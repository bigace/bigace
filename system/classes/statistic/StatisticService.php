<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.<br>Copyright (C) Kevin Papst.
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
 * @subpackage statistic
 */

/**
 * This class server information about your Website Statistics.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage statistic
 */
class StatisticService
{
    /**
     * @access private
     */
    var $helper;

	function StatisticService() 
	{
		if (isset($GLOBALS['_BIGACE']['SQL_HELPER']))
		    $this->setSQLHelper($GLOBALS['_BIGACE']['SQL_HELPER']);
	}
	
	/**
	 * Sets the SQLHelper that should be used for receiving Statistic information.
	 * @param SQLHelper the SQLHelper to use
	 */
	function setSQLHelper($sqlhelper)
	{
        $this->helper = $sqlhelper;
	}
	
	function deleteAllStatistics($cid = _CID_) 
	{
	    $values = array( 'CID' => $cid );
	    return $this->_execute('stats_delete_all', $values);
	}

	function deleteStatisticsBefore($date, $cid = _CID_) 
	{
	    $values = array( 'DATE'     => $date,
	                     'CID'		=> $cid );
	    return $this->_execute('stats_delete_time', $values);
	}
	
	function countAllHits($cid = _CID_) 
	{
	    $values = array( 'CID' => $cid );
	    $result =  $this->_execute('stats_count_all_hits', $values);
	    $result = $result->next();
	    return $result["cnt"];
	}

	function countTotalBrowser($names, $cid = _CID_) 
	{
		$browserNames = $this->_prepareSearchString('browser',$names);
	    $values = array( 'BROWSER' 	=> $browserNames,
	                     'CID'		=> $cid );
	    $result =  $this->_execute('stats_count_browser', $values);
	    $result = $result->next();
	    return $result["cnt"];
	}

	function countTopVisitors($limit, $cid = _CID_) 
	{
	    $values = array( 'LIMIT' => $limit,
	                     'CID'	 => $cid );
	    return $this->_execute('stats_top_visitors', $values);
	}

	function countOperatingSystem($names, $cid = _CID_) 
	{
		$osNames = $this->_prepareSearchString('browser',$names);
	    $values = array( 'OS' 	=> $osNames,
	                     'CID'	=> $cid );
	    $result =  $this->_execute('stats_count_os', $values);
	    $result = $result->next();
	    return $result["cnt"];
	}

	function countTopReferer($limit, $filter, $cid = _CID_) 
	{
		$ext = '';
		foreach ($filter AS $filterString) {
			$ext .= " referer NOT LIKE '%$filterString%' AND ";
		}

	    $values = array( 'LIMIT'     => $limit,
	                     'CID'	     => $cid,
	                     'EXTENSION' => $ext );
	    return $this->_execute('stats_top_referer', $values);
	}

	function getTopURLs($limit, $commands = array(), $cid = _CID_) 
	{
	    $ext = '';
	    foreach($commands AS $cmd) {
	        $ext .= " AND command <> '".$cmd."'";
	    }
	    $values = array( 'LIMIT' 	 => $limit,
	                     'CID'	 	 => $cid,
	                     'EXTENSION' => $ext);
	    return $this->_execute('stats_top_urls', $values);
	}
	
	function countSessions($cid = _CID_) 
	{
	    $values = array( 'CID' => $cid );
	    $result = $this->_execute('stats_count_sessions', $values);
	    return $result->count();
	}
	
	function getMinTimestamp($cid = _CID_) 
	{
	    $min = $this->_getEntry('', 'min(timestamp) as timestamp', $cid);
	    $min = $min->next();
	    return $min["timestamp"];
	}

	function getMaxTimestamp($cid = _CID_) 
	{
	    $max = $this->_getEntry('', 'max(timestamp) as timestamp', $cid);
	    $max = $max->next();
	    return $max["timestamp"];
	}
	
	/**
	 * @access private
	 */
	function _getEntry($where, $columns = '*', $cid = _CID_) 
	{
	    $values = array( 'CID'      => $cid,
	                     'COLUMNS'  => $columns,
	                     'WHERE'    => $where );
	    return $this->_execute('stats_select', $values);
	}

	/**
	 * @access private
	 */
	function _execute($sqlFile, $values)
	{
        $sqlString = $this->helper->loadStatement($sqlFile);
	    $sqlString = $this->helper->prepareStatement($sqlString, $values);
	    return $this->executeStatisticSQL($sqlString);
	}
	
	/**
	 * Executes a SQL against the Statistic Database.
	 * @param String the SQL Command to execute
	 */
	function executeStatisticSQL($sqlString) 
	{
		$sqlString = $this->helper->prepareStatement($sqlString);
	    return $this->helper->execute($sqlString);
	}
	
	/**
	 * @access private
	 */
	function _prepareSearchString($column, $names)
	{
		if (!is_array($names))
			$names = array($names);

		$osNames = "'%".$names[0]."%'";
		for($i=1; $i < count($names); $i++) {
			$osNames .= " OR ".$column." LIKE '%".$names[$i]."%'";
		}
		
		return $osNames;
	}

}

?>