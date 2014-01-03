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
 * @package bigace.installation
 */


// exit if we are not included in the main installation script
if(!defined('_BIGACE_INSTALL_PARENT_')) {
    die('Not runnable alone, go to '.dirname(__FILE__).'/index.php');
}

// some needed parameter
define('ACTIVE_MOD_REWRITE', 'true');
define('NO_MOD_REWRITE', 'false');

define('ACTIVE_HTACCESS_SECURITY', 'true');
define('DEACTIVE_HTACCESS_SECURITY', 'false');

define('INSTALL_DB_OK', 'ok');
define('INSTALL_DB_ERROR_SQL', 'sqlError');
define('INSTALL_DB_ERROR_CONNECTION', 'connectError');
define('INSTALL_DB_ERROR_DB', 'dbError');
define('INSTALL_DB_ERROR_UNKNOWN', 'unknownError');
define('INSTALL_DB_ERROR_FILE', 'fileNotFound');

// DB Structure for BIGACE
define('FILE_DATABASE_STRUCTURE', $_INSTALL['FILE']['adodb_xml']);

// load environment
loadClass('consumer', 'ConsumerInstallHelper');
$installHelper = new ConsumerInstallHelper();

require_once(_DIR_INSTALL_LIBS.'database.inc.php');
require_once(_DIR_INSTALL_ADODB.'adodb.inc.php');
require_once(_DIR_INSTALL_ADODB.'adodb-xmlschema03.inc.php');

// check if the database is already proper installed
// ...then skip this installation
$res = checkDatabaseInstallation($GLOBALS['_BIGACE']['db']);
if(isset($res['status']) && $res['status'] == _STATUS_DB_OK)
{
	show_install_header($MENU);
	echo '<h1>' . getTranslation('menu_step_'.MENU_STEP_CORE) . '</h1>';
	displayMessage( getTranslation('db_already_exists') );
    displayNextButton(MENU_STEP_COMMUNITY, 'next');
    show_install_footer($MENU);
    return;
}

$outerError = array();
//  ########################################## DECIDE ABOUT MODUS ##########################################
$showInput = true;

$MODE = extractVar('mode' , '');
if ($MODE == '1')
{
    if( isset($DATA['db']) && strlen(trim($DATA['db'])) > 0 && isset($DATA['host']) && strlen(trim($DATA['host'])) > 0 &&
        isset($DATA['user']) && strlen(trim($DATA['user'])) > 0 && isset($DATA['pass']) )
    {
	    $preError = array();
	    
        if(isset($DATA['mod_rewrite']) && $DATA['mod_rewrite'] == ACTIVE_MOD_REWRITE) {
            $preError = array_merge($preError, installModRewrite());
        }

        if(isset($DATA['security']) && $DATA['security'] == ACTIVE_HTACCESS_SECURITY) {
            $preError = array_merge($preError, installHtaccessFiles());
        }
        
        if(count($preError) > 0) 
        {
	        show_install_header($MENU);
	        // ???
            echo '<h1>' . getTranslation('menu_step_'.MENU_STEP_CORE) . '</h1>';
	        
	        foreach($preError AS $errMsg) {
        		displayError($errMsg);
	        }
        	
		    // critical error, get the connections values again
            showInstallCoreFormular($DATA);
            show_install_footer($MENU);
            return;
        }

        // write security values only once for an installation
        // random values are best to provide good security
        mt_srand(time());
        $DATA['salt'] = md5(uniqid(mt_rand(), true));
        $DATA['saltsize'] = mt_rand(1,31);
        
        installDB($DATA, $MENU);
        $showInput = false;
    }
    else
    {
        $outerError[] = 'Missing or incorrect values, please check your inputs.';
    }
}

if($showInput)
{
    if( canStartInstallation() )
    {
		
		show_install_header($MENU);
        echo '<h1>' . getTranslation('menu_step_'.MENU_STEP_CORE) . '</h1>';
        if(count($outerError)>0) {
	        foreach($outerError AS $oe) {
	        	displayError($oe);
	        }
        }
		showInstallCoreFormular();
        show_install_footer($MENU);
    }
}
//  ########################################## END DECIDE ABOUT MODUS ##########################################


/**
* The Start Page for the BIGACE Database Installation
*/
function showInstallCoreFormular($data = array())
{
	
    $dbHost     = isset($data['host'])      ? $data['host']     : 'localhost';
    $dbPrefix   = isset($data['prefix'])    ? $data['prefix']   : 'cms_';
    $dbName     = isset($data['db'])        ? $data['db']       : '';
    $dbUser     = isset($data['user'])      ? $data['user']     : 'root';
    $base_dir   = ''; // will be calculated later

    $mod_rewrite = '<input id="rewrite_yes" type="radio" name="data[mod_rewrite]" value="'.ACTIVE_MOD_REWRITE.'" /> <a href="#" onclick="toogleRadionButton(\'rewrite_yes\');return false;" class="noLink">' . getTranslation('mod_rewrite_yes').'</a>'.
	'<br /><input id="rewrite_no" type="radio" name="data[mod_rewrite]" value="'.NO_MOD_REWRITE.'" checked="checked" /> <a href="#" onclick="toogleRadionButton(\'rewrite_no\');return false;" class="noLink">' . getTranslation('mod_rewrite_no').'</a>';

    $htaccess_security = '<input id="secure_yes" type="radio" name="data[security]" value="'.ACTIVE_HTACCESS_SECURITY.'" /> <a href="#" onclick="toogleRadionButton(\'secure_yes\');return false;" class="noLink">' . getTranslation('htaccess_security_yes') . '</a>' .
	'<br /><input id="secure_no" type="radio" name="data[security]" value="'.DEACTIVE_HTACCESS_SECURITY.'" checked="checked" /> <a href="#" onclick="toogleRadionButton(\'secure_no\');return false;" class="noLink">' . getTranslation('htaccess_security_no') . '</a>';

    // list of drivers from the AdoDB drivers Directory
    $databaseTypes = array(
                        'access' => 'access',
                        'ado' => 'ado',
                        'ado5' => 'ado5',
                        'ado_access' => 'ado_access',
                        'ado_mssql' => 'ado_mssql',
                        'borland_ibase' => 'borland_ibase',
                        'csv' => 'csv',
                        'db2' => 'db2',
                        'fbsql' => 'fbsql',
                        'firebird' => 'firebird',
                        'ibase' => 'ibase',
                        'informix' => 'informix',
                        'informix72' => 'informix72',
                        'ldap' => 'ldap',
                        'mssql' => 'mssql',
                        'mssqlpo' => 'mssqlpo',
                        'mysql' => 'mysql',
                        'mysqli' => 'mysqli',
                        'mysqlt' => 'mysqlt',
                        'netezza' => 'netezza',
                        'oci8' => 'oci8',
                        'oci805' => 'oci805',
                        'oci8po' => 'oci8po',
                        'odbc' => 'odbc',
                        'odbc_db2' => 'odbc_db2',
                        'odbc_mssql' => 'odbc_mssql',
                        'odbc_oracle' => 'odbc_oracle',
                        'odbtp' => 'odbtp',
                        'odbtp_unicode' => 'odbtp_unicode',
                        'oracle' => 'oracle',
                        'pdo' => 'pdo',
                        'pdo_mssql' => 'pdo_mssql',
                        'pdo_mysql' => 'pdo_mysql',
                        'pdo_oci' => 'pdo_oci',
                        'pdo_pgsql' => 'pdo_pgsql',
                        'postgres' => 'postgres',
                        'postgres64' => 'postgres64',
                        'postgres7' => 'postgres7',
                        'postgres8' => 'postgres8',
                        'proxy' => 'proxy',
                        'sapdb' => 'sapdb',
                        'sqlanywhere' => 'sqlanywhere',
                        'sqlite' => 'sqlite',
                        'sqlitepo' => 'sqlitepo',
                        'sybase' => 'sybase',
                        'sybase_ase' => 'sybase_ase'
                     );

    // ------ calculate the base dir ------
    
    if (isset($data['base_dir']))
    {
        $base_dir = $data['base_dir'];
    }
    else
    {
        $dirname = '';
        
        if(isset($_SERVER['REQUEST_URI'])) {
            $path_parts = pathinfo($_SERVER['REQUEST_URI']);
            $dirname = $path_parts["dirname"];
        }
        
        $base_dir = str_replace( "/misc/install", "", $dirname);
        // if we are not in bigace root dir
        if (strlen($base_dir) > 0)
        {
            // remove starting slash(-es) - added while for misspelled path like /cms//bigace/misc/
            while(substr($base_dir,0,1) == '/') {
                $base_dir = substr($base_dir,1,strlen($base_dir));
            }

            // add ending slash
            if(substr($base_dir,strlen($base_dir)-1,strlen($base_dir)) != '/') {
                $base_dir .= '/';
            }
        }
    }
    // ------------------------------------

    echo '<form action="'.createInstallLink(MENU_STEP_CORE, array('mode' => '1')).'" method="POST">' . "\n";
    echo '<input type="hidden" name="data[type]" value="mysql">' . "\n";

    installTableStart( getTranslation('db_value_title') );
    //installRow('db_type', createSelectBox('type', $databaseTypes, 'mysql'));
    installRowTextInput('db_host', 'host',$dbHost);
    installRowTextInput('db_database', 'db',$dbName);
    installRowTextInput('db_user', 'user',$dbUser);
    installRowPasswordField('db_password', 'pass', '');
    installRowTextInput('db_prefix', 'prefix', $dbPrefix);
    installTableEnd();

    installTableStart( getTranslation('ext_value_title') );
    //installRow('def_language', getDefaultLanguageChooser());
    installRow('mod_rewrite', $mod_rewrite);
    installRow('htaccess_security', $htaccess_security);
    installRowTextInput('base_dir', 'base_dir', $base_dir);
    installTableEnd();

    echo'<div align="right"><button class="buttonLink" type="submit">'.getTranslation('next').' &gt;&gt;</button></div>';
    echo '</form>';
}


/**
 * Installs the BIGACE Database structure!
 * @return whether an critical error occured or not (display the input formular again or a next button)
 */


function installDB($data, $MENU)
{
	$errors = array();
    $status = INSTALL_DB_ERROR_UNKNOWN;

    $db = ADONewConnection( $data['type'] );
    @$db->Connect( $data['host'], $data['user'], $data['pass'] );

    if($db->IsConnected())
    {
        if (file_exists(FILE_DATABASE_STRUCTURE))
        {
            $dict = NewDataDictionary($db);
            $ttt = $dict->CreateDatabase($data['db'], 
				array("mysql" => "DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci"));
            $resCreateDB = $dict->ExecuteSQLArray($ttt);
            // FIXME change for use with other databases too, 1007 is only mysql specific!
            if ($resCreateDB != 2 && $db->ErrorNo() != 1007) {
                $errors[] = getTranslation('error_db_create');
            }

            @$db->Connect( $data['host'], $data['user'], $data['pass'], $data['db'] );
			$data['prefix'] = trim($data['prefix']);
			
            if ($db->IsConnected())
            {
                // if connection could be established, go and parse the config files
                // Parse the main config file for replacer
                $GLOBALS['installHelper']->parseFileForReplacer($GLOBALS['_INSTALL']['FILE']['config_system'],$data);

                foreach($GLOBALS['installHelper']->getError() AS $msg) {
                    $errors[] = $msg;
                }
                $GLOBALS['installHelper']->cleanError();

                // prepare the Database Installation Files
                $schema = new adoSchema( $db );
                $schema->SetPrefix($data['prefix'], FALSE);
                $sql = @$schema->ParseSchema( FILE_DATABASE_STRUCTURE );

                if($sql === FALSE)
                {
                    $errors[] = 'Could not parse Database Structure: ' . FILE_DATABASE_STRUCTURE;
                }
                else
                {
                    $result = $schema->ExecuteSchema();
                    if($result == 2) {
                        $status = INSTALL_DB_OK;
                    }
                    else {
                        $status = INSTALL_DB_ERROR_SQL;
                        $errors[] = 'Could not install DB: ' . $db->ErrorMsg();
                    }
                }
            }
            else {
                // could not select database
                $errors[] = getTranslation('error_db_select');
                $status = INSTALL_DB_ERROR_DB;
            }
            unset ($sql);
        }
        else
        {
            $errors[] =  'Could not find Database structure file: ' . FILE_DATABASE_STRUCTURE;
            $status = INSTALL_DB_ERROR_FILE;
        }
    }
    else
    {
        $errors[] = 'Could not connect, Database Host or Username/Password were incorrect, please try again: ' . $db->ErrorMsg();
        $status = INSTALL_DB_ERROR_CONNECTION;
    }

    if($status == INSTALL_DB_OK) {
    	header('Location: ' . createInstallLink(MENU_STEP_COMMUNITY));
    	exit;
    }
    else {
    //if($status == INSTALL_DB_ERROR_FILE || $status == INSTALL_DB_ERROR_UNKNOWN || $status == INSTALL_DB_ERROR_CONNECTION || $status == INSTALL_DB_ERROR_DB) {
	    show_install_header($MENU);
        echo '<h1>' . getTranslation('menu_step_'.MENU_STEP_CORE) . '</h1>';
	    
    	if(count($errors)>0) {
		    foreach($errors AS $errMsg) {
	    		displayError($errMsg);
		    }
	    }
    	
		// critical error, get the connections values again
        showInstallCoreFormular($data);

        show_install_footer($MENU);
    }
}

// checks if every table is installed
function checkDatabaseInstallation($data)
{
    $error = 0;
    $msg = '';
    
    if(strtolower($data['type']) == '{cid_db_type}') {
        return array(   'status'    => _STATUS_DB_NOT_ALL,
                        'message'   => getTranslation('state_not_all_db'),
                        'error'     => $msg,
                        'image'     => 'not_installed.png' );
    }

    $db = ADONewConnection( $data['type'] );
    @$db->Connect( $data['host'], $data['user'], $data['pass'], $data['name'] );
	$data['prefix'] = trim($data['prefix']);
	
    if ($db->IsConnected())
    {
		foreach(get_all_table_names() AS $name) {
			$res = $db->SelectLimit('SELECT * FROM '.$data['prefix'].$name, 1);

            if ($res === false || $db->ErrorNo() != 0) {
                $error++;
                $msg .= $db->ErrorMsg() . '<br>';
            }
		}
	}
    else
    {
        return array(   'status'    => _STATUS_DB_NOT_OK,
                        'message'   => getTranslation('state_no_db'),
                        'image'     => 'not_installed.png' );
    }

	// properly connected
    if ($error > 0)
    {
        return array(   'status'    => _STATUS_DB_NOT_ALL,
                        'message'   => getTranslation('state_not_all_db'),
                        'error'     => $msg,
                        'image'     => 'not_installed.png' );
    }
    else
    {
        return array(   'status'    => _STATUS_DB_OK,
                        'message'   => getTranslation('state_installed'),
                        'image'     => 'installed.png' );
    }
}


?>
