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
 
import('classes.consumer.ConsumerInstallHelper');

/**
 * Error definition
 */
define('CONSUMER_ERROR_WRONG_TYPE',  'wrongConsumerDefinitionType');
/**
 * Error definition
 */
define('CONSUMER_ERROR_UNDEFINED',   'notProperlyConsumerDefinitionType');
/**
 * Error definition
 */
define('CONSUMER_ERROR_CONFIG',      'problemCreatingConfig');
/**
 * Error definition
 */
define('CONSUMER_ERROR_DATABASE',    'problemCreatingDatabase');

/**
 * Return type
 */
define('CONSUMER_CREATED_DIRECTORY', 'createdConsumerDirectorySuccessful');
/**
 * Return type
 */
define('CONSUMER_CREATED_DATABASE',  'createdConsumerDatabaseSuccessful');
/**
 * Return type
 */
define('CONSUMER_CREATED_CONFIG',    'createdConsumerConfigSuccessful');
/**
 * Return type
 */
define('CONSUMER_CREATED_SUCCESS',   'createdConsumerSuccessful');

/**
 * This Class is used to create new Consumers for your CMS.
 * Call <code>createConsumer($consumerDefinition)</code> to create a new Consumer.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage consumer
 */
class ConsumerInstaller extends ConsumerInstallHelper
{

    /**
     * This function creates a new Consumer and returns one of the following keys:
     * 
     * - CONSUMER_ERROR_WRONG_TYPE (the passed ConsumerDefinition is not of proper type)
     * - CONSUMER_ERROR_UNDEFINED (the ConsumerDefinition is not correct set up - normally values missing)
     * - CONSUMER_ERROR_CONFIG (for example if Config entry could not be created)
     * - CONSUMER_ERROR_DATABASE (problems encountered during database installation)
     * - CONSUMER_CREATED_SUCCESS (everything worked fine, Consumer was created)
     * 
     * @return mixed one of the mentioned Constants
     */
    function createConsumer($consumerDefinition)
    {
        if (!$this->isConsumerDefinition($consumerDefinition))
            return CONSUMER_ERROR_WRONG_TYPE;
            
        if (!$consumerDefinition->isDefined())
            return CONSUMER_ERROR_UNDEFINED;
        
        // if no Consumer ID was passed, we calculate it, by finding the actual 
        // highest and increasing it
        if (strlen($consumerDefinition->getID()) < 1) {
            $consumerDefinition->setID( $this->getNextConsumerID() );
        }
        
        // add config entry for new consumer
        $res = $this->createNewConsumerConfig($consumerDefinition);
        
        if($res != CONSUMER_CREATED_CONFIG)
            return $res; 

        // config creation worked, perform file clone
        $res2 = $this->createNewConsumerDirectory($consumerDefinition);
        
        if($res2 != CONSUMER_CREATED_DIRECTORY)
            return $res2;
        
        $res3 = $this->createNewConsumerDatabase($consumerDefinition);
        
        // perform database installation
        if($res3 != CONSUMER_CREATED_SUCCESS)
        	return $res3;
        
        return CONSUMER_CREATED_SUCCESS;
    }
    
    /**
     * @access private
     */
    function createNewConsumerConfig($consumerDefinition) 
    {
        $cid = $consumerDefinition->getID();
        $domain = $consumerDefinition->getDomain();
        if($this->addToConsumerConfiguration($domain, $cid))
            return CONSUMER_CREATED_CONFIG;
        
        return CONSUMER_ERROR_CONFIG;
    }

    /**
     * @access private
     */
    function createNewConsumerDirectory($consumerDefinition) 
    {
        $cid = $consumerDefinition->getID();
        // perform file installation
        $dirs = array( CID_MAIN_DIR, CID_PUBLIC_DIR );
        foreach($dirs AS $dirname) {
            $handle=opendir( $dirname ); 
            while ($file = readdir ($handle)) { 
                if ($file != "." && $file != "..") { 
                    $cur_file = $dirname.$file;                    
                    if (is_dir($cur_file) && preg_match('/'.CID_REPLACER.'/i', $cur_file)) 
                    {
                        $this->createDir($cur_file, $cid);
                        $this->createConsumerDirectory($cur_file, $cid);
                    }
                }
            }
        }
        
        // create all empty but required directories
        $newDirs = $this->getDirectoriesToCreate();
        foreach($newDirs AS $toCreate) {
        	$this->createDir($toCreate, $cid);
        }
        
        // get all files that has to be parsed
        $allFilesToParse = array();
        $toParse = $this->getFilesToParse();
        //jeden dateinamen parsen wegen {CID} und nachher um sql extension erweitern
        foreach($toParse AS $filename) {
            $allFilesToParse[] = $this->parseConsumerString($filename, $cid);
        }
        // append all extended sql files to be parsed
        $toParse = $this->getAllSQLExtensionFiles($cid);
        foreach($toParse AS $filename) {
            $allFilesToParse[] = $filename;
        }

        // create replacer array values
        $data = $this->getReplacerFromConsumerDefinition($consumerDefinition);

        // perform all other sql files
        foreach($allFilesToParse AS $filename) {
            $this->parseFileForReplacer($filename, $data);
        }
        return CONSUMER_CREATED_DIRECTORY;
    }

    /**
     * @access private
     */
    function createNewConsumerDatabase($consumerDefinition) 
    {
    	// TODO implement the check for problems somehow different
        $counterStart = count($this->getError());
        $this->createDatabase($consumerDefinition);
        $counterEnd = count($this->getError());
        
        if($counterEnd > $counterStart)
        	return CONSUMER_ERROR_DATABASE;
        
        return CONSUMER_CREATED_DATABASE;
    }    
        
    /**
     * @access private
     */
    function createDatabase($consumerDefinition)
    {
    	$cid = $consumerDefinition->getID();	
    
		if($this->executeXmlSchema(XML_FILE_CREATE, $consumerDefinition))
			$this->logInfo("Created Community data!");
		else 
			$this->logError("Error while creating Community data!");
        foreach($this->getInstallXMLFiles($cid) AS $filename) {
            if($this->executeXmlSchema($filename,$consumerDefinition))
				$this->logInfo("Executed Schema from " . $filename);
			else 
				$this->logError("Failed while executing schema from " . $filename);
        }

        // add all extension SQL Files from the install directory
        foreach($this->getInstallSQLFiles($cid) AS $filename) {
            $this->executeStatementsFromFile($filename);
        }
    }
    
    /**
     * Parses the given Directory recursive for all Entrys with a Name matching of 'cid{CID}'.
     * Does all functions needed to handle a new Consumer.
     * @access private
     */
    function createConsumerDirectory($dirname, $cid) 
    {
        $success = TRUE;
            
        $handle=opendir( $dirname ); 
    
        while ($file = readdir ($handle)) 
        { 
            if ($file != "." && $file != "..") 
            { 
                $name = $file;
                $cur_file = $dirname.'/'.$file;
    
                if (is_dir($cur_file)) 
                {
                    if (preg_match('/'.CID_REPLACER.'/i', $cur_file)) 
                    {
                        $temp = $this->createDir($cur_file, $cid);
                        if (!$temp) {
                            $success = $temp;
                        }
                    }
    
                    $temp = $this->createConsumerDirectory($cur_file, $cid);
                    if (!$temp) {
                        $success = $temp;
                    }
                } 
    
                if (is_file($cur_file)) 
                {
                    if (preg_match('/'.CID_REPLACER.'/i', $cur_file)) 
                    {
                        $temp = $this->createFile($file, $cur_file, $cid);
                        
                        if (!$temp) {
                            $success = $temp;
                        }
                    }
                }
                
            } 
        }
        closedir($handle); 
        return $success;
    }
    
    /**
     * Creates the given Directory for the new Consumer.
     * @access private
     */
    function createDir($name, $cid)
    {
        $new_dir_name = $this->parseConsumerString($name, $cid);
        if(!IOHelper::createDirectory($new_dir_name)) {
            $this->logError( 'Failed creating Directory: ' . $new_dir_name );
        	return false;
        }
        return true;
    }

    /**
     * Creates the given File in the new Consumer Directory.
     * Parses the name and finds the "Place where to go" automatically.
     *
     * If the File itself is a {CID} File, it will be openend, parsed and saved.
     * @access private
     */
    function createFile($filename, $name, $cid) 
    {
        $new_file_name = $this->parseConsumerString($name, $cid);
        if (!copyFile($name, $new_file_name)) 
        {
            $this->logError('Could not copy file: ' . $new_file_name);
            return false;
        } 
        return true;
    }       

}

?>