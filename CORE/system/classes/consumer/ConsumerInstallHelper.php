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
import('classes.util.IOHelper');
import('classes.sql.AdoDBConnection');
import('classes.parser.XmlToSqlParser');

define('CID_REPLACER',               '{CID}');
define('CID_MAIN_DIR',               _BIGACE_DIR_ROOT . '/consumer/');
define('CID_PUBLIC_DIR',             _BIGACE_DIR_ROOT . '/public/');
define('CID_TEMPLATE_DIR',           CID_MAIN_DIR . 'cid' . CID_REPLACER . '/');

define('CID_CONFIG_DIR',             CID_TEMPLATE_DIR . 'config/');
define('CID_HTML_DIR',               CID_TEMPLATE_DIR . 'items/html/');
define('CID_INSTALL_DIR',        	 CID_TEMPLATE_DIR . 'install/');
define('CID_UNINSTALL_DIR', 	 	 CID_TEMPLATE_DIR . 'uninstall/');

define('SQL_FILE_EXTENSION',         'sql');
define('XML_FILE_EXTENSION',         'xml');

define('SQL_FILE_DELETE',            'delete_data_cid.sql');
//define('SQL_FILE_CREATE',            'db_data_cid.sql');
//define('XML_FILE_CREATE',            'community.xml');
define('XML_FILE_CREATE',            _BIGACE_DIR_ROOT . '/system/sql/community.xml');

/**
 * The ConsumerInstallHelper defines general methods that can be used for creating 
 * Installation mechansim for Consumer.
 * It is used both, in Administration and Installation console.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage consumer
 */
class ConsumerInstallHelper
{
    private $errors = array();
    private $infos  = array();


    /**
     * @access private
     */
    function getNextConsumerID() 
    {
        $consumerHelper = new ConsumerHelper();
        $consumerIDs = $consumerHelper->getAllConsumerIDs();
        $highest = 0;
        
        foreach ($consumerIDs AS $cid) {
            $id = (int)$cid;
            if ($id > $highest) {
                $highest = $id;
            }
        }
        
        return ++$highest;
    }

    /**
     * @access private
     */
    function getUninstallSQLFiles($cid)
    {
    	return $this->getSQLFilesFromDirectory($cid, CID_UNINSTALL_DIR);
    }

    /**
     * @access private
     */
    function getInstallSQLFiles($cid)
    {
    	return $this->getSQLFilesFromDirectory($cid, CID_INSTALL_DIR);
    }

    /**
     * Returns all XML Files to be executed by the XmlToSqlParser.
     * @access private 
     */
    function getInstallXMLFiles($cid) {
    	return IOHelper::getFilesFromDirectory( $this->parseConsumerString(CID_INSTALL_DIR, $cid), XML_FILE_EXTENSION);
    }
    
    /**
     * @access private
     */
    function getAllSQLExtensionFiles($cid)
    {
    	$all = $this->getInstallSQLFiles($cid);
    	return array_merge($all, $this->getUninstallSQLFiles($cid));
    }

    /**
     * @access private
     */
    function getSQLFilesFromDirectory($cid, $directory)
    {
        $files = IOHelper::getFilesFromDirectory( $this->parseConsumerString($directory, $cid), SQL_FILE_EXTENSION);
        $sqlExt = array();
        foreach($files AS $name) 
        {
            if (strpos($name, SQL_FILE_DELETE) === false)
                $sqlExt[] = $name;
        }
        return $sqlExt;
    }
    
    /**
     * @access private
     */
    function logError($msg) {
        $this->errors[] = $msg;
    }
    
    /**
     * @access private
     */
    function logInfo($msg) {
        $this->infos[] = $msg;
    }
    
    function cleanError() {
        $this->errors = array();
    }

    function cleanInfos() {
        $this->infos = array();
    }

    function getError() {
        return $this->errors;
    }
    
    function getInfo() {
        return $this->infos;
    }

    // -----------------------------------------------------------
    // ------------------------------------------------------------
    
    /**
     * @access private
     */
    function executeStatementsFromFile($filename, $values = array())
    {
        if (!file_exists($filename)) {
            $this->logError('SQL File is missing: ' . $filename);
        }
        else {
            //$this->logInfo('Executing SQLs from file: ' . $filename);
            $sql = $this->getStatementsFromFile($filename);
            if ($sql === FALSE) {
                $this->logError('Could not fetch SQL statements from file: '.$filename);
            }
            else {
            	$error = $this->executeStatements($sql,$values);	
		        if ($error == 0) {
		            $this->logInfo('Successful executed statements from file: ' . $filename);
		            $result = true;
		        } else {
		            $this->logError('Problems when performing SQLs from file: ' . $filename);
		        }
            }
        }        
    }
    
    function executeXmlSchema($filename, $consumerDefinition) 
    {
        $error = false;
        if (!file_exists($filename)) {
            $this->logError('XML File is missing: ' . $filename);
        }
        else {
	    	$cid = $consumerDefinition->getID();	
	
	      	// parse XML File to Community Directory
	        $data = $this->getReplacerFromConsumerDefinition($consumerDefinition);
	        $xmlContent = IOHelper::get_file_contents($filename);
	        $xmlContent = $this->parseForReplacer($xmlContent, $data);
	    	$ado = new AdoDBConnection();
	    	$dbConnection = $ado->getADONewConnection();	
			$dbConnection->Execute("SET NAMES utf8");
			$dbConnection->Execute("SET CHARACTER SET utf8");
			
	        $myParser = new XmlToSqlParser();
		    $myParser->setAdoDBConnection($dbConnection);
		    $myParser->setIgnoreVersionConflict(true);
		    $myParser->setTablePrefix($GLOBALS['_BIGACE']['db']['prefix']);
		    $myParser->setReplacer(array('{CID}' => $cid));
		    $myParser->setMode(XML_SQL_MODE_INSTALL);
		    $myParser->parseStructure($xmlContent);
	
		    $errors = $myParser->getError();
		    if(count($errors) >0) {
		        $this->logError('Some errors occured during XML Parsing for Community '.$cid.', please correct them before continuing');
		        //if(isset($_GET['errors'])) {
		            foreach($errors AS $error) {
		                $this->logError($error);
		            }
		        //}
		    } else {
		        $sqls = $myParser->getSqlArray();
		        foreach($sqls AS $statement) {
		        	$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute( $statement );
	                if ($res->isError()) 
	                {
    	                $err = $GLOBALS['_BIGACE']['SQL_HELPER']->getError();
    	                if(!is_null($err))
    	                    $statement .= "<br/>[" . $err->getNumber() . "] " . $err->getMessage();
		                $this->logError('Problem executing SQL statement: ' . $statement);
		                $error = true;
		            }
		        }
		    }
			// --------------------------------------------------------------------
        }
        return !$error;
    }
    
    /**
     * @access private
     */
    function executeStatements($statementsArray,$values=array())
    {
        $error = 0;

        foreach($statementsArray AS $key) 
        {
            if (trim($key) != '') 
            {
                $statement = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($key,$values); 
                $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute( $statement );
                if ($res->isError()) 
                {
	                $err = $GLOBALS['_BIGACE']['SQL_HELPER']->getError();
	                if(!is_null($err))
	                    $statement .= "<br/>[" . $err->getNumber() . "] " . $err->getMessage();
                    $this->logError('Problems executing SQL statement: '.$statement);
                    $error++;
                }
            }
        }
        return $error;
    }


    /**
     * Adds the given Configuration to the Consumer Configuration File.
     * @access private
     */
    function addToConsumerConfiguration($newDomain, $newCid)
    {
        $consumerHelper = new ConsumerHelper();
        $id = $consumerHelper->getConsumerIdForDomain($newDomain);
        
        if($id < 0) 
        {
            // Create the Consumer ID config
            $cidValues = array('id' => $newCid);
            if ($consumerHelper->addConsumerConfig($newDomain, $cidValues))
            {
                $this->logInfo('Added Community configuration for domain: '.$newDomain);
                return true;
            }
        }
        
        return false;
    }

    /**
     * @access private
     */
    function parseFileForReplacer($filename, $data, $target = null)
    {
        if($target == null)
            $target = $filename;
            
        if ($file = fopen($filename, 'r')) {
            $content = fread( $file, filesize($filename) );
            // replace all {CID_REPLACER}
            $content = $this->parseForReplacer($content, $data);
            
            if (fclose($file)) 
            {
                if ($file = fopen($target, 'w+')) 
                {
                    fwrite($file, $content);
                    $this->logInfo('Parsed file: '.$filename.' and saved as: '.$target);
                    
                    if (!fclose($file)) {
                        $this->logError('Could not close file: ' . $target);
                    }
                    return true;
                }
            }
        } 
        else {
            $this->logError('Could not open file: '.$filename);
        }
        
       return false;
    }
    
    /**
     * Parses the given Content and replaces all XXX in content with values from data array.
     */
    function parseForReplacer($content, $data) 
    {
        foreach ($this->getReplacerArray() AS $key => $val)
        {
            if (isset($data[$key])) {
            	/*
            	 * Removed - CDATA and UTF-8 handle this "problem"
            	if($key == 'sitename') {
            		$data[$key] = htmlspecialchars($data[$key]);
            	}
				*/
            	$content = preg_replace('/'.$val.'/i', $data[$key], $content);
            }
        }
        return $content;
    }
    
    /**
     * Returns the associative Array with all keys (used for formulars) 
     * and the Replacer mappings that are used in the installation Files. 
     */
    function getReplacerArray() 
    {
        return array(
            'cid'            => CID_REPLACER,
            'salt'           => '{AUTH_SALT}',
            'saltsize'       => '{SALT_LENGTH}',
        	'admin'          => '{CID_ADMIN}',
            'password'       => '{CID_PW}',
            'name'           => '{CID_DOMAIN}',
            'type'           => '{CID_DB_TYPE}',
            'host'           => '{CID_DB_HOST}',
            'user'           => '{CID_DB_USER}',
            'db'             => '{CID_DB_NAME}',
            'pass'           => '{CID_DB_PASS}',
            'prefix'         => '{CID_DB_PREFIX}',
            'sitename' 		 => '{SITE_NAME}', // the _ is important! otherwise the {sitename} tag would be replaced
        	'default_editor' => '{DEFAULT_EDITOR}',
            'default_style'  => '{DEFAULT_STYLE}',
        	'default_lang'   => '{DEFAULT_LANGUAGE}',
            'webmastermail'  => '{CID_WEBMASTER_EMAIL}',
            'mailserver'     => '{CID_EMAIL_SERVER}',
            'statistics'     => '{WRITE_STATISTICS}',
            'base_dir'       => '{BASE_DIR}',
            'mod_rewrite'    => '{MOD_REWRITE}',
            'dir'            => 'cid{CID}'
        );
    }
    
    /**
     * Create an associative Array from an ConsumerDefinition file.
     * The keys can be directly mapped to the keys in the Array fetched by
     * <code>getReplacerArray()</code>.
     * @param ConsumerDefinition definition the ConsumerDefinitin to fetch values from
     */
    function getReplacerFromConsumerDefinition($definition)
    {
        if(!$this->isConsumerDefinition($definition))
            return array();
            
        return array(
            'cid'            => $definition->getID(),
            'admin'          => $definition->getAdminUser(),
            'password'       => md5($definition->getAdminPassword()),
            'name'           => $definition->getDomain(),
            'host'           => $GLOBALS['_BIGACE']['db']['host'],
            'user'           => $GLOBALS['_BIGACE']['db']['user'],
            'db'             => $GLOBALS['_BIGACE']['db']['name'],
            'pass'           => $GLOBALS['_BIGACE']['db']['pass'],
            'prefix'         => $GLOBALS['_BIGACE']['db']['prefix'],
        	'sitename' 		 => $definition->getSitename(),
        	'default_editor' => $definition->getDefaultEditor(),
            'default_style'  => $definition->getAdminStyle(),
            'default_lang'   => $definition->getDefaultLanguage(),
            'webmastermail'  => $definition->getWebmasterEmail(),
            'mailserver'     => $definition->getMailServer(),
            'base_dir'       => BIGACE_DIR_PATH,
            'statistics'     => $definition->getWriteStatistics()
        );
    }
    
    /**
     * Return an array with all (absolute) Filenames, that must be parsed 
     * after the Files has copied to its new Consumer Directory.
     * Remember to call <code>parseConsumerString($filename, $cid)</code>
     * on each Filename to get the real Path!
     * @return array all Files to be parsed
     */
    function getFilesToParse() 
    {
        return array(
        	// not required, {CID] and {DB_PREFIX} will be replaced automatically!
            //CID_UNINSTALL_DIR . SQL_FILE_DELETE,
        );
    }

    /**
     * 
     */
    function getDirectoriesToCreate() {
        return array(CID_TEMPLATE_DIR . 'cache/', 
					 CID_TEMPLATE_DIR . 'install/', 
					 CID_TEMPLATE_DIR . 'smarty/', 
					 CID_TEMPLATE_DIR . 'smarty/cache/', 
					 CID_TEMPLATE_DIR . 'smarty/configs/', 
					 CID_TEMPLATE_DIR . 'smarty/templates_c/');
    }
    
    /**
     * Creates the given String and renames it to the needed Consumer declaration
     */
    function parseConsumerString($name, $cid)
    {
        return preg_replace('/'.CID_REPLACER.'/i', $cid, $name);
    }
    
    /**
     * @access private
     */
    function getStatementsFromFile($filename)
    {
        if($of = fopen($filename, 'r'))
        {
            $stats = array();
            $sql = fread($of, filesize($filename));
            $statements = preg_split('/;/i', $sql);
            if(!fclose($of)) {
                $this->logError('Could not close file: '.$filename);
            }
            
            foreach($statements AS $sql)
            {
                if (strpos(trim($sql), "#") === FALSE || strpos(trim($sql), "#") > 1)
                {
                    // only add NON Comments
                    array_push($stats, $sql);
                }
            }
            return $stats;
        }
        return FALSE;
    }    
    
    /**
     * @access private
     */
    function isConsumerDefinition($consumerDefinition) 
    {
        if (is_object($consumerDefinition) && strcasecmp(get_class($consumerDefinition), 'ConsumerDefinition') == 0)
            return true;
        
        return false;
    }     

}

