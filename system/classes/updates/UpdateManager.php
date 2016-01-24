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
 * @subpackage updates
 */
 
define('SETTING_ALLOW_ALL', 'ALL');
define('DEFAULT_VERSION_COMPARATOR', '>=');

define('RESULT_NOT_EXISTS', 'fileNotExists');
define('RESULT_NOT_WRITABLE', 'fileNotWritable');

import('classes.updates.UpdateResult');
import('classes.updates.SeparatorResult');
import('classes.util.IOHelper');
import('classes.sql.AdoDBConnection');
import('classes.parser.XmlToSqlParser');

/**
 * This class helps installing Updates. 
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage updates
 */
class UpdateManager
{
    /**
     * @access private
     */
    var $results = array();
    /**
     * @access private
     */
    var $cid = _CID_;
    /**
     * @access private
     */
    var $module;
    /**
     * @access private
     */
    var $errors = 0;
    
    function UpdateManager($cid)
    {
        $this->cid = $cid;
    }
    
    function getUpdateModul() 
    {
        return $this->module;
    }
    
    function performUpdate($modul, $ignoreList = array())
    {
        $this->module = $modul;
        $this->performUpdateForConsumer($this->cid, $this->module->getFullPath(), $ignoreList);
    }
    
    function addErrorMessage($message) 
    {
        $this->errors++;
        array_push($this->results, new UpdateResult(FALSE, $message));
    }

    function addInfoMessage($message) 
    {
        array_push($this->results, new UpdateResult(TRUE, $message));
    }

    function addSeparator($message) 
    {
        array_push($this->results, new SeparatorResult($message));
    }
    
    function getResults() 
    {
        return $this->results;
    }
    
    /**
     * Adds a bunch of <code>UpdateResult</code> messages to the internal logger.
     */
    function addResults($msgResults)
    {
    	$this->results = array_merge($this->results, $msgResults);
    }
    
    /**
     * Returns the number of occured errors.
     */
    function countErrors() 
    {
        return $this->errors;
    }
    
    function getDefaultIgnoreList() 
    {
    	return array('.', '..', UPDATE_CONFIG);
    }
    
    /**
     * Checks the File rights for the
     * @return array an array with filenames that are not writable 
     */
    function checkFileRights($modul, $ignoreList)
    {
        $disallowed = array();
        $notfound = array();
        
        // check the consumer files that will be deleted
        if ($modul->hasConsumerFilesToDelete()) {
            foreach($modul->getConsumerFilesToDelete() AS $filename) {
                $res = $this->checkFileRight($filename, $this->cid);
                if($res == RESULT_NOT_EXISTS)
                    $notfound[] = $this->parseConsumerString($filename, $this->cid);
                else if($res == RESULT_NOT_WRITABLE)
                    $disallowed[] = $this->parseConsumerString($filename, $this->cid);
            }
        }
        
        // check the system files that will be deleted
        if ($modul->hasSystemFilesToDelete()) {
            foreach($modul->getSystemFilesToDelete() AS $filename) {
                $res = $this->checkFileRight($filename, null);
                if($res == RESULT_NOT_EXISTS)
                    $notfound[] = $filename;
                else if($res == RESULT_NOT_WRITABLE)
                    $disallowed[] = $filename;
            }
        }
        
        $af = $this->getAllFilesFromUpdate($modul, $ignoreList);
        foreach($af AS $filename)
        {
            $filename = substr($this->stripRootDir($filename),1);
            $res = $this->checkFileRight($filename, null);
            if($res == RESULT_NOT_EXISTS) {
                if(!in_array($filename, $notfound))
                    $notfound[] = $filename;
            } else if($res == RESULT_NOT_WRITABLE) {
                if(!in_array($filename, $disallowed))
                    $disallowed[] = $filename;
            }
        }

        return $disallowed;
    }
    
    /**
     * @access private
     */
    function checkFileRight($filename, $consumerID = null) 
    {
        if($consumerID != null) {
            $filename = $this->parseConsumerString($filename, $consumerID);
        }
        
        $filename = _BIGACE_DIR_ROOT . '/' . $filename; 
        
        if(file_exists($filename)) {
            if(!is_writable($filename))
                return RESULT_NOT_WRITABLE;
        } else {
            return RESULT_NOT_EXISTS;
        }
        return true;
    }
    
    // ---------------------------------------------------------------------
    // ---------------------------------------------------------------------
    
    
    // ######################### [START] BUILDING FILE IGNORE LIST #########################
    function buildIgnoreList($modul, $ignoreList = array())
    {
        // List of all Files that will not be used when performing File update
        
        if (count($ignoreList) == 0)
        {
            $ignoreList = $this->getDefaultIgnoreList();
        }

        // added configurated ignore files to ignore list
        if ($modul->hasIgnoreFiles()) 
        {
	        if (is_array($modul->getIgnoreFiles())){
	        	foreach ($modul->getIgnoreFiles() AS $desc => $ignoreTempFile) {
	        	    if (!isset($ignoreList[$ignoreTempFile]))
	        		    array_push($ignoreList, $ignoreTempFile);
	        	}
	        }
	    } 

        // Add System SQL File to Ignore List
        if ($modul->hasSystemSQLFilename()) {
            array_push($ignoreList, $modul->getSystemSQLFilename());
        }

        // Add Consumer SQL File to Ignore List
        if ($modul->hasConsumerSQLFilename()) {
            array_push($ignoreList, $modul->getConsumerSQLFilename());
        }
        
        // Add Consumer Update Jobs to Ignore List
        if ($modul->hasConsumerClassFilename()) {
            array_push($ignoreList, $modul->getConsumerClassFilename() . '.php');
        }

        // Add System Update Jobs to Ignore List
        if ($modul->hasSystemClassFilename()) {
            array_push($ignoreList, $modul->getSystemClassFilename() . '.php');
        }

        // Add System Update Jobs to Ignore List
        if ($modul->usesAdoDB()) {
            array_push($ignoreList, $modul->getAdoDBFilename());
        }
        
        return $ignoreList;
    }
    // ######################### [END] BUILDING FILE IGNORE LIST #########################

    /**
     * @param int the Consumer ID to perform the Update for
     * @param String the Update to perform, MUST end with a trainling slash!
     * @param array the List of Files to ignore when performing the Update
     */
    function performUpdateForConsumer($consumerID, $UPDATE_DIR, $ignoreList)
    {
        $modul = $this->getUpdateModul();
        
        // Start with update, if we do know which directory we shall use for the current update
        if ( !$modul->isValid()) 
        {
            $this->addErrorMessage(getTranslation('error_update_no_config') . '<br>' . $modul->getFullIniFilename());
        } 
        else 
        {
            $this->addSeparator('Starting Update');
            
            // read configuration
	    	$this->addInfoMessage('Using Config File: ' . $modul->getFullIniFilename());
            //$_UPDATE = $modul->getSettings();
    
            $ignoreList = $this->buildIgnoreList($modul, $ignoreList);
    
            if ($modul->hasIncludes()) 
            {
                $this->addSeparator('Including files');
                
                foreach ($modul->getIncludeFilenames() AS $incName => $incFile) 
                {
                    if (file_exists($UPDATE_DIR . $incFile) && is_file($UPDATE_DIR . $incFile)) {
                	    include_once($UPDATE_DIR . $incFile);
                	    $this->addInfoMessage('Included File ('.$incName.'): '.$incFile);
                	} else {
                	    $this->addErrorMessage('Failed including File ('.$incName.'): '.$incFile);
                    }
                }
                unset($incName);
                unset($incFile);
            }
    
            // delete files
            if ($modul->hasConsumerFilesToDelete()) {
                $this->deleteFiles( $modul->getConsumerFilesToDelete(), $consumerID );
            }
            else {
            	//$this->addInfoMessage('No consumer files specified to be deleted!');
            }

            if ($modul->hasSystemFilesToDelete()) {
                $this->deleteFiles( $modul->getSystemFilesToDelete(), null );
            }
            else {
            	//$this->addInfoMessage('No system files specified to be deleted!');
            }
            
            // Update System dependend Database entrys
            if ($modul->hasSystemSQLFilename()) {
                $sqlFileSystem = $UPDATE_DIR . $modul->getSystemSQLFilename();
                if (file_exists($sqlFileSystem)) {
                    $this->addSeparator('Performing System Database Update');
                    $this->performDBUpdate( SQL_DELIMITER, $sqlFileSystem );
                    $this->addInfoMessage('Done...');
                }
                else {
                    $this->addErrorMessage(getTranslation('error_update_no_sqlfile') . ' ' . $sqlFileSystem);
                }
            }
    
            // create or update any database table
            if($modul->usesAdoDB()) 
            {
                $xmlFileSystem = $UPDATE_DIR . $modul->getAdoDBFilename();
                
                if (file_exists($xmlFileSystem)) 
                {
                    $this->addSeparator('Performing AdoDB System Update');
    				include_once(_BIGACE_DIR_ADDON.'adodb/adodb.inc.php');
    				include_once(_BIGACE_DIR_ADDON.'adodb/adodb-xmlschema03.inc.php');
    				$adoConn= new AdoDBConnection();
    		        $as = new adoSchema( $adoConn->getADONewConnection() );
                    $as->SetPrefix($GLOBALS['_BIGACE']['db']['prefix'], FALSE);
        			$as->ExistingData(XMLS_MODE_UPDATE);
        			$sqlArray = $as->ParseSchemaFile($xmlFileSystem);
                    if($sqlArray === FALSE) {
                        $this->addErrorMessage('Could not parse Database Structure File !');
                    }
                    else {
        			    $newSqlArray = array();
    		            foreach($sqlArray AS $statement) {
                          	// make sure to replace the CID and DB Prefix 
    		            	$sql = $this->parseConsumerString($statement, $consumerID);
                            $newSqlArray[] = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql,array());
    		            }
    		            if(!$as->ExecuteSchema($newSqlArray, TRUE)) {
                            $this->addErrorMessage('Error executing Database Update!');
                        }
                    }
                    unset($sqlArray);
                    unset($newSqlArray);
                    unset($as);
                    unset($adoConn);
                    $this->addInfoMessage('Done...');
                }
                else
                {
                	// TODO proper translation                
                    $this->addErrorMessage(getTranslation('error_update_no_sqlfile') . ' ' . $xmlFileSystem);
                }
            }

            // Update Consumer dependend Database entrys
            if ($modul->hasConsumerSQLFilename()) 
            {
                $sqlFileCid = $UPDATE_DIR . $modul->getConsumerSQLFilename();
                
                if (file_exists($sqlFileCid)) 
                {
                    $this->addSeparator('Performing Consumer Database Update');
                    $this->performDBUpdate( SQL_DELIMITER, $sqlFileCid, $consumerID );
                    $this->addInfoMessage('Done...');
                }
                else
                {
                    $this->addErrorMessage(getTranslation('error_update_no_sqlfile') . ' ' . $sqlFileCid);
                }
            }
               
            // Update Consumer dependend Database entrys by XML Structure File
            if ($modul->hasConsumerXMLFilename()) 
            {
                $xmlFileCid = $UPDATE_DIR . $modul->getConsumerXMLFilename();
                
                if (file_exists($xmlFileCid)) 
                {
                    $this->addSeparator('Performing Consumer XML Database Update');
                    $this->performXMLUpdate($xmlFileCid, $consumerID);
                    $this->addInfoMessage('Done...');
                }
                else
                {
                	// TODO proper translation                
                    $this->addErrorMessage(getTranslation('error_update_no_sqlfile') . ' ' . $xmlFileCid);
                }
            }
            
            // ################ [START] Perform File update ################
            $this->addSeparator('Performing File Update');
            
            $this->performFileUpdate($UPDATE_DIR, $consumerID, $ignoreList);
            // ################ [END] Perform File update ################
    
    
            // ################ [START] Update Job for System ################
            if ($modul->hasSystemClassFilename()) 
            {
                $className = $modul->getSystemClassFilename();
                $this->addSeparator('Performing Update Job for SYSTEM');
				$updateJob = new $className();
            	if (is_subclass_of($updateJob, JOB_SYSTEM)) 
            	{
            		$updateJob->setDBConnection( $GLOBALS['_BIGACE']['SQL_HELPER']->getConnection() );
	            	$updateJob->setUpdateModul($modul);
	            	$updateJob->setUpdateManager($this);
            		if( !$updateJob->install() )
            			$this->addResults( $updateJob->getErrors() );
            	}
            	else 
            	{
            		$this->addErrorMessage('Class: "' . $className . '" is not of type: "'.JOB_SYSTEM.'" !');
            	}
            	unset($className);
            	unset($updateJob);
            }
            // ################ [END] Update Job for System ################
    
    
            // ################ [START] Update Job for Consumer ################
            if ($modul->hasConsumerClassFilename()) 
            {
				$className = $modul->getConsumerClassFilename();
                $this->addSeparator('Performing Update Job for Consumer');
            	$updateJob = new $className();
            	if (is_subclass_of($updateJob, JOB_CONSUMER)) 
            	{
            		$updateJob->setDBConnection( $GLOBALS['_BIGACE']['SQL_HELPER']->getConnection() );
	            	$updateJob->setUpdateManager($this);
	            	$updateJob->setUpdateModul($modul);
            		if( !$updateJob->install($consumerID) )
            			$this->addResults( $updateJob->getErrors() );
            	}
            	else 
            	{
            		$this->addErrorMessage('Class: "' . $className . '" is not of type: "'.JOB_CONSUMER.'" !');
            	}
            	unset($className);
            	unset($updateJob);
            }
            // ################ [END] Update Job for Consumer ################
        }
        return $this->getResults();
    }
    
    /**
     * Returns a list of all files that will be written when performing the update.
     */
    function getAllFilesFromUpdate($modul, $ignoreList = array())
    {
        $ignoreList = $this->buildIgnoreList($modul, $ignoreList);
    	return $this->parseDirectoryRecurse($modul->getFullPath(), $ignoreList, $this->cid, true, false);
    }


    /**
     * Performs an File Update.
     * @param String the Directory to start from
     * @param int the Consumer ID
     * @param array the List of Files that should be ignored
     */
    function performFileUpdate($startDir, $consumerID, $ignoreList = array())
    {
        return $this->parseDirectory($startDir, $ignoreList, $consumerID, true);
    }
    
    // ----------------------------------------------------------------------
    // ----------------------------------------------------------------------
    
    /**
     * Call this method instead of calling 
     */
    function parseDirectory($startDir, $ignoreList, $consumerID, $fromUpdateDirectory = true)
    {
        $listMessage = 'Defined Ignore Files: ';

        foreach ($ignoreList AS $ignoreFile) {
            $listMessage .= " '<b>".$ignoreFile."</b>' ";
        }
        $this->addInfoMessage($listMessage);
        unset($listMessage);

        $this->addInfoMessage('Starting File update from "' . realpath($startDir) . '"');
        $res = $this->parseDirectoryRecurse($startDir, $ignoreList, $consumerID, $fromUpdateDirectory, true);
        $this->addInfoMessage('Done ...');
        return $res;
    }

    /**
     * @access private
     */
    function parseDirectoryRecurse($startDir, $ignoreList, $consumerID, $fromUpdateDirectory = true, $writeFiles = true)
    {
        $filenames = array();
//        $this->addInfoMessage('Parsing Directory: ' . $startDir);
        $handle=opendir( $startDir ); 
    
        while ($file = readdir ($handle)) 
        { 
            $useFileForUpdate = TRUE;
            foreach ($ignoreList AS $ignoreFile)
            {
                if ($file == $ignoreFile) 
                {
                    $useFileForUpdate = FALSE;
                }
            }
            
            if ($useFileForUpdate) 
            { 
                $plain_file   = $file;
                $update_file  = $startDir . $file;
                
                if ($fromUpdateDirectory) {
	                //if we start copying from the update directory 
	                //calculate following filename
	                $orig_file = _BIGACE_DIR_ROOT . '/' . $update_file;
                	$orig_file = str_replace($GLOBALS['UPDATE_DIR'], '', $orig_file);
                } else {
	                //otherwise use this one 
	                $orig_file = $update_file;
                }
                
                //echo 'Original: ('.$orig_file.') - Update ('.$update_file.') - Plain ('.$plain_file.') <br>';

                if (is_dir($update_file)) 
                {
                    if (preg_match('/'.CID_REPLACER.'/i', $update_file)) 
                    {
                        $temp_orig_file = $this->parseConsumerString($orig_file, $consumerID);
                        
                        $filenames[] = $temp_orig_file;
                        
                        if ($writeFiles && !file_exists($temp_orig_file)) 
                        {
                            if (!IOHelper::createDirectory($temp_orig_file)) {
                                $this->addErrorMessage('Failed to create directory "' .  $this->stripRootDir($temp_orig_file) . '"');
                            } else {
                                $this->addInfoMessage('Created Directory "' . $this->stripRootDir($temp_orig_file) . '"');
                            }
                        }
                    }
                    
                	// Only create directorys where target is NOT a cid{CID} Directory!
                	if(strpos($orig_file, CID_REPLACER) === FALSE)
                	{
                	    $filenames[] = $orig_file;
                	    
	                    if ($writeFiles && !file_exists($orig_file)) 
	                    {
	                        if (!IOHelper::createDirectory($orig_file)) {
	                            $this->addErrorMessage('Failed creating directory "' .  $this->stripRootDir($orig_file) . '"');
	                        } else {
	                            $this->addInfoMessage('Created Directory "' . $this->stripRootDir($orig_file) . '"');
	                        }
	                    }
                	}
                	$t = $this->parseDirectoryRecurse($update_file . '/', $ignoreList, $consumerID, $fromUpdateDirectory, $writeFiles);
                    $filenames = array_merge($t, $filenames);
                } 
    
                if (is_file($update_file)) 
                {
                	// Only copy if target file is NOT a cid{CID} Directory!
                	if(strpos($update_file, CID_REPLACER) === FALSE)
                	{
                	    $filenames[] = $orig_file;
                	    if($writeFiles)
                	    {
    	                    if (!copyFile($update_file, $orig_file)) {
    	                        //$this->addErrorMessage('Failed copying file: "' . $update_file . '" to "' . $orig_file . '"');
    	                        $this->addErrorMessage('Failed creating file: "' .  $this->stripRootDir($orig_file) . '". Please check File rights!');
    	                    } else {
    	                        //$this->addInfoMessage('Copied file "' . $update_file . '" to "' . $orig_file . '"');
    	                        //$this->addInfoMessage('Created file "' . $this->stripRootDir($orig_file) . '"');
    	                    }
    	                }
                	}
    
                    if (preg_match('/'.CID_REPLACER.'/i', $update_file)) 
                    {
                        $temp_orig_file = $this->parseConsumerString($orig_file, $consumerID);
                	    
                	    $filenames[] = $temp_orig_file;
                        
                	    if($writeFiles)
                	    {
                            if (!copyFile($update_file, $temp_orig_file)) {
                                //$this->addErrorMessage('Failed copying file: "' . $update_file . '" to "' . $temp_orig_file . '"');
                                $this->addErrorMessage('Failed creating file: "' .  $this->stripRootDir($temp_orig_file) . '". Please check File rights!');
                            } else {
                                //$this->addInfoMessage('Copied file "' . $update_file . '" to "' . $temp_orig_file . '"');
                                //$this->addInfoMessage('Created file "' . $this->stripRootDir($temp_orig_file) . '"');
                            }
                        }
                    }
                }
            } 
        }
        closedir($handle);
        return $filenames;
    }
    
    // ----------------------------------------------------------------------
    // ----------------------------------------------------------------------
    
    /**
     * 
     * @param array an array with all filenames to be deleted
     * @param int the Consumer ID to execute on
     */
    function deleteFiles($files, $consumerID = null)
    {
    	if (!is_array($files)) {
	        $this->addErrorMessage('Files to be deleted MUST be an array!');
	        return;
    	}
        
        foreach ($files AS $key => $filename) 
        {
        	if ($filename != '') 
        	{
    	        $file_to_delete = _BIGACE_DIR_ROOT . '/' . $filename;
    	        
    	        if ($consumerID != null && preg_match('/'.CID_REPLACER.'/i', $file_to_delete)) 
    	        {
                    $temp = $this->parseConsumerString($filename, $consumerID);
                    $temp = _BIGACE_DIR_ROOT . '/' . $temp;
                    if (is_file($temp)) {
                        $this->deleteNamedFile($temp);
                    } else if (is_dir($temp)) {
                        $this->deleteNamedDirectory($temp);
                    }
    	        }
    	        
    	        if (file_exists($file_to_delete)) {
    	            if (is_file($file_to_delete)) {
    	                $this->deleteNamedFile($file_to_delete);
    	            } else if (is_dir($file_to_delete)) {
    	                $this->deleteNamedDirectory($file_to_delete);
    	            }
    	        }
    	    } 
    	    else
    	    {
    	    	$GLOBALS['LOGGER']->logError( 'Did not delete empty directory: ' . $key );
    	    }
            
        }
    }
    
    function performXMLUpdate($filename, $cid) 
    {
	      	// parse XML File to Community Directory
	        $xmlContent = IOHelper::get_file_contents($filename);
	    	$ado = new AdoDBConnection();
	    	$dbConnection = $ado->getADONewConnection();	
	
	        $myParser = new XmlToSqlParser();
		    $myParser->setAdoDBConnection($dbConnection);
		    $myParser->setIgnoreVersionConflict(false);
		    $myParser->setTablePrefix($GLOBALS['_BIGACE']['db']['prefix']);
		    $myParser->setReplacer(array('{CID}' => $cid));
		    $myParser->setMode(XML_SQL_MODE_INSTALL);
		    $myParser->parseStructure($xmlContent);
	
		    $errors = $myParser->getError();
		    if(count($errors) >0) {
		        $this->addErrorMessage('Some errors occured during XML Parsing for Community '.$cid.', please correct them before continuing');
		        if(isset($_GET['errors'])) {
		            foreach($errors AS $error) {
		                logError($error);
		            }
		        }
		    } else {
		        $sqls = $myParser->getSqlArray();
		        foreach($sqls AS $statement) {
		        	$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute( $statement );
	                if ($res->isError()) 
	                {
		                $this->addErrorMessage('Problem occured when executing the Statement: ' . $statement);
		                $error = true;
		            }
		        }
		    }
			// --------------------------------------------------------------------
    }
    
    /**
    * Updates Database, uses DB Connection fetched with Configuration
    * of the current BIGACE Installation.
    * 
    * ATTENTION: If you leave the third parameter empty, no parsing of the SQL Command
    * for Consumer specific data will be performed!
    *
    * @param splitter the String that is used to split Database Statements
    * @param file     the File that holds the SQL Statements
    * @param int	  the Consumer ID or an empty String
    */
    function performDBUpdate( $splitter, $file, $consumerID = '' )
    {
        if (is_file($file) && filesize($file)>0)
        {
            $of = fopen($file, 'r');
            $sql = fread( $of, filesize($file) );
            $sql = preg_split('/'.$splitter.'/i', $sql);
            
            $error = 0;
            $count = 0;
            
            foreach($sql AS $key) 
            {
                if (trim($key) != '') 
                {
                    $statement = $key;
                    if ($consumerID != '')
                    {
                    	// change Consumer replacer to current Consumer ID 
                        $statement = $this->parseConsumerString($statement, $consumerID);
                        
                    	// make sure to replace the DB Prefix 
                        $statement = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($statement,array());
                        
                        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($statement);
                        if ($res->isError()) {
                            $this->addErrorMessage('Could not perform DB Statement for Consumer('.$consumerID.'), Message:<br>' . mysql_error() . '<br>'.$statement);
                        }
                    }
                    else
                    {
                    	// make sure to replace the DB Prefix 
                        $statement = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($statement,array());

                        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($statement);
                        if ($res->isError()) {
                            $this->addErrorMessage('Could not perform DB Statement, Message:<br>' . mysql_error() . '<br>'.$statement);
                        }
                    }
                }
            }
        } // End check if is_file && filesize > 0
    }


    /**
     * @access private
     */
    function deleteNamedFile($file) 
    {
        if (is_file($file))
        {
            if (unlink($file)) {
        	    $this->addInfoMessage('Deleted File: '.$file);
            } else {
			    $oldumask = umask(_BIGACE_DEFAULT_UMASK_FILE);
			    if (unlink($file)) {
	        	    $this->addInfoMessage('Deleted File with umask('._BIGACE_DEFAULT_UMASK_FILE.'): '.$file);
			    } else {
	        	    $this->addErrorMessage('Could not delete File: ' . $file . ' - Please check File rights!');
			    }
			    umask($oldumask); 
            }
        }
    }
    
    /**
     * @access private
     */
    function deleteNamedDirectory($file) 
    {
        if (is_dir($file)) 
        {
            $handle=opendir($file); 
            while ($name = readdir ($handle)) 
            { 
                if ($name != "." && $name != "..") { 
                    $temp = $file.'/'.$name;
                    if (is_file($temp)){
                        $this->deleteNamedFile($temp);
                    } else if (is_dir($temp)) {
                        $this->deleteNamedDirectory($temp);
                    }
                } 
            }
            closedir($handle); 
            
            if (rmdir($file)) {
        	    $this->addInfoMessage('Deleted Directory: '.$file);
            } else {
			    $oldumask = umask(_BIGACE_DEFAULT_UMASK_FILE);
			    if (rmdir($file)) {
	        	    $this->addInfoMessage('Deleted Directory with umask('._BIGACE_DEFAULT_UMASK_FILE.'): '.$file);
			    } else {
	        	    $this->addErrorMessage('Could not delete Directory: ' . $file . ' - Please check File rights!');
			    }
			    umask($oldumask); 

            }
        }
    }

    function stripRootDir($filename) {
        return str_replace(_BIGACE_DIR_ROOT, '', $filename);
    }    
  

    /**
     * Creates the given String and renames it to the needed Consumer declaration
     */
    function parseConsumerString($name, $cid)
    {
        return preg_replace('/'.CID_REPLACER.'/i', $cid, $name);
    }
      
}

?>