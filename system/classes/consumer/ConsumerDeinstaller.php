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
 * @package bigace.classes
 * @subpackage consumer
 */
 
import('classes.consumer.ConsumerDefinition');
import('classes.consumer.ConsumerHelper');
import('classes.consumer.ConsumerService');
import('classes.consumer.ConsumerInstallHelper');
import('classes.util.IOHelper');


/**
 * This Class is used to deinstall a Consumers from your CMS.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage consumer
 */
class ConsumerDeinstaller extends ConsumerInstallHelper
{

	/**
	 * @access public
	 */
	function removeDatabase($cid)
	{
    	$allFiles = array(
    		$this->parseConsumerString(CID_UNINSTALL_DIR . SQL_FILE_DELETE, $cid)
		);
	    	
        // add all extension SQL Files from the install directory
        foreach($this->getUninstallSQLFiles($cid) AS $enhanced)
        {
            array_push($allFiles, $enhanced);
        }
        
        foreach ($allFiles AS $filename)
        {
            $this->executeStatementsFromFile($filename, array('CID' => $cid));
        }
        
        $sqlExt = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('session_delete_consumer');
        $this->executeStatements(array($sqlExt), array('CID' => $cid));
	}
	
	/**
	 * @access private
	 */
	function removeDir($cur_file)
	{
        if (is_writable($cur_file)) {
            if ( !@rmdir($cur_file) ) {
                $this->logError(getTranslation('error_remove_dir').': ' . $cur_file); 
            }
        } else {
            $this->logError(getTranslation('error_not_writeable').': ' . $cur_file); 
        }
	}
	
	/**
	 * @access protected
	 */
	function removeDirectoryRecursive($dirname) 
	{
	    if (file_exists($dirname) && $handle=@opendir( $dirname )) {
	        while ($file = @readdir ($handle)) { 
	            if ($file != "." && $file != "..") { 
	                $name = $file;
	                $cur_file = $dirname.'/'.$file;
	    
	                if (is_dir($cur_file)) {
	                    $this->removeDirectoryRecursive($cur_file);
	                    $this->removeDir($cur_file);
	                } 
	    
	                if (is_file($cur_file)) 
	                {
                        if (is_writable($cur_file)) {
                            if ( !@unlink($cur_file) ) {
                                $this->logError(getTranslation('error_remove_file').': ' . $cur_file); 
                            }
                        } else {
                            $this->logError(getTranslation('error_not_writeable').': ' . $cur_file); 
                        }
	                }
	                
	            } 
	        }
	        @closedir($handle); 
	    } 
	    else 
	    {
	        $this->logError( getTranslation('error_read_dir').": ". $dirname ); 
	    }
	}

	/**
	 * @access public
	 */
	function removeConsumerDirectory($cid) 
	{
		$dirname = $this->parseConsumerString(CID_TEMPLATE_DIR,$cid);
		$this->removeDirectoryRecursive($dirname);
		$this->removeDir($dirname);
	}
	
	
	/**
	 * Deletes the given Consumer from the Consumer Configuration.
	 * @param int consumerID the ConsumerID to be removed (including all Alias Entrys) 
	 */
	function deleteFromConsumerConfiguration($consumerID)
	{
	    $consumerHelper = new ConsumerHelper();
	    return $consumerHelper->removeConsumerByID($consumerID);
	}

}

?>