<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) 2002-2006 Kevin Papst.
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
 * For further information visit {@link http://www.kevinpapst.de www.kevinpapst.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.
 */
 
class ConsumerFileUpdate extends AbstractConsumerUpdate
{
	
	function doUpdate($cid) 
	{
		$modul = $this->getUpdateModul();
		$startDir = $modul->getFullPath() . '../../../consumer/cid' . CID_REPLACER . '/';
		$startDir2 = $modul->getFullPath() . '../../../public/cid' . CID_REPLACER . '/';
		//$startDir = _BIGACE_DIR_ROOT . '/consumer/cid' . CID_REPLACER . '/';

		$update_manager = new UpdateManager($cid);
		//$update_manager = $this->getUpdateManager();

		// ------- Build ignore File List -------------
		$ignore = $update_manager->getDefaultIgnoreList();
		$ignore2 = $update_manager->getDefaultIgnoreList();
		
		$names = array(
                    '-1_de.html',
					'-1_en.html',
                    'toplevel_{CID}.html', // for systems installed before 1.8.3
					'config.email.cid{CID}.inc.php',
					'config.system.cid{CID}.inc.php',
					'config.statistic.cid{CID}.inc.php'
				 );
				 
		foreach ($names AS $fileName)
		{
			$ignoreName = $fileName;
			array_push($ignore, $ignoreName);
		}
		// ---------------------------------------------

        $update_manager->addInfoMessage('Updating Consumer files from "' . realpath($startDir) . '"');
		$update_manager->parseDirectory($startDir, $ignore, $cid, false);
		
        $update_manager->addInfoMessage('Updating Public files from "' . realpath($startDir2) . '"');
		$update_manager->parseDirectory($startDir2, $ignore2, $cid, false);
		return $update_manager->getResults();
	}

}

?>