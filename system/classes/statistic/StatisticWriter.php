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
 * @subpackage statistic
 */

/**
 * This class writes Statistics.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage statistic
 */
class StatisticWriter
{

	function writeStatistic($command, $itemid, $ip, $browser, $referer, $session_id, $userid, $cid = _CID_)
	{
		if($command == 'admin' || strcmp(((int)$itemid),$itemid) != 0)
			return false;
			
	    import('classes.statistic.StatisticConnection');
	    $conn = new StatisticConnection();

	    $values = array( 'COMMAND'	    => $command,
	                     'ITEMID'	    => $itemid,
	                     'IP'     	 	=> $ip,
	                     'BROWSER'   	=> $browser,
	                     'REFERER'		=> $referer,
	                     'SESSION'		=> $session_id,
	                     'USERID'	    => $userid,
	                     'CID'		 	=> $cid );
	    
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('stats_insert');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
	    
	    if ($conn->isConnected()) {
	        $id = $conn->insert($sqlString);
	        $conn->close();
	        unset($conn);
	        return $id;
	    } else {
	        $GLOBALS['LOGGER']->logError("Could not connect to Statistic Database, check Config File!");
    	    return FALSE;
	    }
	}
	
}

?>