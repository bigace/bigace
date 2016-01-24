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

import('classes.sql.SimpleMySQLConnection');

/**
 * This class opens a connection to the statistic database.
 * You can move the statistics tables to a different DB, to increase performance. 
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage statistic
 */
class StatisticConnection extends SimpleMySQLConnection
{

	function StatisticConnection()
	{
	    if(isset($GLOBALS['_BIGACE']['statistic'])) {
		    $this->connect($GLOBALS['_BIGACE']['statistic']['host'],
		    	$GLOBALS['_BIGACE']['statistic']['name'],
		    	$GLOBALS['_BIGACE']['statistic']['user'],
		    	$GLOBALS['_BIGACE']['statistic']['password'], true);
	    }
	    else {
        	$this->connect($GLOBALS['_BIGACE']['db']['host'],
        		$GLOBALS['_BIGACE']['db']['name'],
        		$GLOBALS['_BIGACE']['db']['user'],
        		$GLOBALS['_BIGACE']['db']['pass'], true);
        		// if last argument is false, everything breaks apart...
	    }
	}
	
}

?>