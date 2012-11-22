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
 * @package bigace.administration
 */

check_admin_login();
admin_header();

import('classes.configuration.IniHelper');
import('classes.util.IOHelper');
import('classes.updates.UpdateManager');
import('classes.updates.UpdateModul');

require_once(_ADMIN_INCLUDE_DIRECTORY.'updates_functions.php');
include_once(_BIGACE_DIR_ADDON.'zip/SimpleUnzip.php');

// -------------------------------------------------------------
define('SQL_DELIMITER',     ';');
define('CID_REPLACER',      '{CID}');
define('JOB_SYSTEM',       	'AbstractSystemUpdate');
define('JOB_CONSUMER',      'AbstractConsumerUpdate');
define('UPDATE_PATH',       _BIGACE_DIR_ROOT . '/misc/updates/');
// -------------------------------------------------------------

define('PARAM_DIRECTORY',   'dirToUpdate');
define('PARAM_MODE',        'modeToExecute');
define('PARAM_SEEN_README', 'seenReadme');

define('MODE_INDEX',        'index');
define('MODE_INSTALL',      'install');
define('MODE_FTP',      	'ftpChmod');
define('MODE_UPLOAD',      	'uploadZip');

define('UPDATE_CONFIG',     'update.ini');

$ignoreList = array('CVS', '.', '..', UPDATE_CONFIG);

$MODE = extractVar(PARAM_MODE, MODE_INDEX);
$DIR = extractVar(PARAM_DIRECTORY, '');

// make sure the directory exists
if(!file_exists(BIGACE_PLUGINS)) {
    IOHelper::createDirectory(BIGACE_PLUGINS);
}

// perform upload and show index afterwards
if($MODE == MODE_UPLOAD) {
    if(!is_writable(UPDATE_PATH)) {
        displayError( sprintf( getTranslation('upload_path_not_writable'), UPDATE_PATH) );
    } else {
        $error = false;
        if(isset($_FILES['newUpdateZip']) && is_uploaded_file($_FILES['newUpdateZip']['tmp_name'])) {
            $newFileName = UPDATE_PATH.$_FILES['newUpdateZip']['name'];
            if(!move_uploaded_file($_FILES['newUpdateZip']['tmp_name'],$newFileName)) {
                $error = true;
            } else {
                if(!extractUpdateFromZip($_FILES['newUpdateZip']['name'],false))
                    $error = true;
                unlink($newFileName);
            }
        } else {
            $error = true;
        }
    }

    $MODE = MODE_INDEX;
    if($error) {
        displayError( getTranslation('upload_failure') );
    } else {
        if(isset($_POST['newUpdateInstall']) && strtolower($_POST['newUpdateInstall']) == 'on') {
            $DIR = getUpdateNameFromZip($_FILES['newUpdateZip']['name']);
            $MODE = MODE_INSTALL;
        } else {
            $module = getUpdateModul( getUpdateNameFromZip($_FILES['newUpdateZip']['name']) );
            displayMessage( getTranslation('upload_success') . ' <a href="'.$module->getSetting('installURL').'">' . getTranslation("update_perform") . ' <img border="0" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'update_perform.png" alt="'.getTranslation("update_perform").'" title="'.getTranslation("update_perform").'"></a>');
        }
    }
}

// check the update modes!
if($MODE == MODE_INSTALL && strlen(trim($DIR)) > 0)
{
    $UPDATE_DIR = UPDATE_PATH . $DIR . '/';
    if (!file_exists($UPDATE_DIR) || !is_dir($UPDATE_DIR))
    {
        if (!file_exists($UPDATE_DIR)) {
            echo createBackLink($GLOBALS['MENU']->getID());
            displayError(getTranslation('error_update_not_exist')); // Update scheint nicht zu existieren
        }
        else if (!is_dir($UPDATE_DIR)) {
            echo createBackLink($GLOBALS['MENU']->getID());
            displayError(getTranslation('error_update_no_dir')); // Argument ist kein Verzeichniss
        }
        else {
            showIndexScreen($ignoreList); // TODO: SHOW ERROR MESSAGE
        }
    }
    else
    {
		$modul = getUpdateModul($DIR);
		if($modul->hasReadme() && extractVar(PARAM_SEEN_README, '0') == '0')
		{
        	// ------------------------
        	// Show Readme
        	// ------------------------
            showReadme($modul);
		}
		else
		{
			// ------------------------
			// Perform Update
			// ------------------------
	        $UpdateManager = new UpdateManager(_CID_);

            $modul = getUpdateModul($DIR);
            $disallowedFiles = $UpdateManager->checkFileRights($modul, $ignoreList);
            if(count($disallowedFiles) > 0)
            {
                showFixFileRights($modul, $disallowedFiles);
            }
            else
            {
    	        $UpdateManager->performUpdate($modul, $ignoreList);
    	    	$results = $UpdateManager->getResults();
                $hadError = false;
                foreach ($results AS $result) {
                    if(!$result->isSuccess())
                       $hadError = true;
                }

                if($hadError) {
        	        showUpdateError($modul, $results);
                } else {
        	        showUpdateSuccess($modul, $results);
        	        showIndexScreen($ignoreList);
                }
     	    }
		}
    }
}
else if($MODE == 'activate' || $MODE == 'deactivate')
{
    // perform de-/activate and show index afterwards

    $name = $_GET['name'];
    //TODO sanitize filename better
    while(($pos = stripos($name, '//')) !== false)
        $name = str_replace('//', '', $name);

    while(($pos = stripos($name, '..')) !== false)
        $name = str_replace('..', '.', $name);

    $name = str_replace('./', '', $name);
    $name = str_replace('/.', '', $name);

    do{
        $pos1 = stripos($name, '/');
        $pos2 = stripos($name, '.');

        if($pos1 !== false && $pos1 == 0)
            $name = substr($name, 1);

        if($pos2 !== false && $pos2 == 0)
            $name = substr($name, 1);

        $pos1 = stripos($name, '/');
        $pos2 = stripos($name, '.');

    } while(($pos1 !== false && $pos1 == 0) || ($pos2 !== false && $pos2 == 0));

    $data = get_plugin_data($name);

    if($MODE == 'activate') {
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement("INSERT INTO {DB_PREFIX}plugins (cid,name,version) VALUES ({CID},{NAME},{VERSION})", array('NAME' => $name, 'VERSION' => $data['version']), true);
	    $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }
    else if($MODE == 'deactivate') {
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement("DELETE FROM {DB_PREFIX}plugins WHERE cid = {CID} AND name = {NAME}", array('NAME' => $name), true);
	    $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    showIndexScreen($ignoreList, array(), 'plugins');
}
else if($MODE == MODE_FTP && strlen(trim($DIR)) > 0 && function_exists('ftp_connect') && function_exists('ftp_chmod'))
{
    $UPDATE_DIR = UPDATE_PATH . $DIR . '/';

	$ba_ftp_host = extractVar('ftp_host');
	$ba_ftp_uid = extractVar('ftp_uid');
	$ba_ftp_pass = extractVar('ftp_pwd');
	$ba_ftp_dir = extractVar('ftp_dir');
	if($ba_ftp_dir != '') {
		$ba_ftp_dir = trim($ba_ftp_dir);

		// if none is available add slashes at start and beginning
		if(strpos($ba_ftp_dir, '/') === false)
			$ba_ftp_dir = '/' . $ba_ftp_dir . '/';

		// if the first character is not a slash add it
		if(strpos($ba_ftp_dir, '/') > 1)
			$ba_ftp_dir = '/' . $ba_ftp_dir;

		// if the last character is not a slash add it
		if(strrpos($ba_ftp_dir, '/') != strlen($ba_ftp_dir)-1)
			$ba_ftp_dir = $ba_ftp_dir . '/';
	}

	$ba_ftp_conn = ftp_connect($ba_ftp_host);

	if(ftp_login($ba_ftp_conn, $ba_ftp_uid, $ba_ftp_pass)) {
		displayMessage('Connected to '.$ba_ftp_uid.'@'.$ba_ftp_host.':'.$ba_ftp_dir);
        $UpdateManager = new UpdateManager(_CID_);
        $modul = getUpdateModul($DIR);
        $disallowedFiles = $UpdateManager->checkFileRights($modul, $ignoreList);
        if(count($disallowedFiles) > 0) {
        	foreach($disallowedFiles AS $ba_disallowed_file) {
				$fileToChmod = $ba_disallowed_file;
				if($ba_ftp_dir == '') {
					$fileToChmod = '/' . $ba_disallowed_file;
        		} else {
					$fileToChmod = $ba_ftp_dir . $ba_disallowed_file;
        		}
        		if(!ftp_chmod($ba_ftp_conn,_BIGACE_DEFAULT_RIGHT_DIRECTORY,$fileToChmod)) {
        			displayError('Could not change mode for: ' . $fileToChmod);
        		} else {
        			displayMessage('Repaired rights for: ' . $fileToChmod);
        		}
        	}

        }
        else {
        	displayMessage('Update ' . $DIR . ' has no Files to correct.');
        }
	}
	else {
		displayError('Could not connect to FTP!');
	}
	if(!ftp_close($ba_ftp_conn)) {
		displayError('Could not close FTP Stream!');
	}

    displayStyledButton(createAdminLink($GLOBALS['MENU']->getID(), array( PARAM_DIRECTORY => urlencode($modul->getName()), PARAM_MODE => MODE_INSTALL, PARAM_SEEN_README => 'true' )), getTranslation('update_execute'));
}
else if($MODE == 'searchExtensions')
{
    // webservice call and search for extensions
    require_once(_BIGACE_DIR_ADDON.'nusoap/class.nusoap_base.php');
    require_once(_BIGACE_DIR_ADDON.'nusoap/class.soap_val.php');
    require_once(_BIGACE_DIR_ADDON.'nusoap/class.wsdl.php');
    require_once(_BIGACE_DIR_ADDON.'nusoap/class.xmlschema.php');
    require_once(_BIGACE_DIR_ADDON.'nusoap/class.soap_transport_http.php');
    require_once(_BIGACE_DIR_ADDON.'nusoap/class.soap_parser.php');
    require_once(_BIGACE_DIR_ADDON.'nusoap/class.soap_fault.php');
    require_once(_BIGACE_DIR_ADDON.'nusoap/class.nu_soapclient.php');

    $l_oClient  = new nu_soapclient('http://www.bigace.de/soap/version2/');

    // TODO: search for all extensions
    $l_stResult = $l_oClient->call('getExtensions', array(_BIGACE_ID));

    // check for errors
    if ($l_oClient->getError()) {
       displayError(getTranslation('find_error')."<br>".$l_oClient->getError());
       $l_stResult = array();
    }

    showIndexScreen($ignoreList, $l_stResult);
}
else
{
    showIndexScreen($ignoreList);
}


function showUpdateSuccess($modul, $results)
{
        $smarty = getAdminSmarty();
        $smarty->assign('UPDATE_NAME', $modul->getTitle()) ;
        $smarty->assign('UPDATE_DIRECTORY', $modul->getName()) ;
        $smarty->assign('UPDATE_VERSION', $modul->getVersion()) ;
        $smarty->assign('UPDATE_DESCRIPTION', $modul->getDescription()) ;
        $smarty->display('AdminUpdateInstalled.tpl');
        unset($smarty);
}

function showUpdateError($modul, $results)
{
	$yes = getTranslation('update_state_yes');
	$no = getTranslation('update_state_no');

    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("UpdatePerformed.tpl.htm", false, true);

    $tpl->setVariable('UPDATE_NAME', $modul->getTitle()) ;
    $tpl->setVariable('UPDATE_DIRECTORY', $modul->getName()) ;
    $tpl->setVariable('UPDATE_VERSION', $modul->getVersion()) ;
    $tpl->setVariable('UPDATE_DESCRIPTION', $modul->getDescription()) ;
    $tpl->setVariable('BACK_LINK', createBackLink($GLOBALS['MENU']->getID())) ;

    $i = 1;

    foreach ($results AS $result) {
        if($result->isSeparator())
        {
            $tpl->setCurrentBlock("separator");
            $tpl->setVariable('SEPARATOR_NAME', $result->getMessage());
            $tpl->setVariable('CSS_CLASS', 'row1');
            $tpl->parseCurrentBlock("separator");
        }
        else if(!$result->isSuccess())
        {
            $tpl->setCurrentBlock("row");
            $tpl->setVariable('CSS_CLASS', (($result->isSuccess()) ? 'row2' : 'rowError'));
            $tpl->setVariable('UPDATE_STEP', $i++);
            $tpl->setVariable('UPDATE_STATE', (($result->isSuccess()) ? $yes : $no));
            $tpl->setVariable('UPDATE_MESSAGE', $result->getMessage());
            $tpl->parseCurrentBlock("row");
        }
        $tpl->setCurrentBlock("outer");
        $tpl->parseCurrentBlock("outer");
    }
    $tpl->show();
}

/**
 * Displays the Screen with all information how to fix the false File Rights.
 */
function showFixFileRights($modul, $disallowedFiles)
{
    echo createBackLink($GLOBALS['MENU']->getID());

    displayMessage( getTranslation('error_fix_file_rights') );

    foreach($disallowedFiles AS $filename) {
        displayError($filename);
    }

    if(function_exists('ftp_connect') && function_exists('ftp_chmod'))
    ?>
    <br />
    <?php
    echo getTranslation('ftp_info')
    ?>

    <br />
    <form action="<?php echo createAdminLink($GLOBALS['MENU']->getID(), array( PARAM_DIRECTORY => urlencode($modul->getName()), PARAM_MODE => MODE_FTP, PARAM_SEEN_README => 'true' )); ?>" method="post">
    	<table border="0">
    	<tr>
    		<td>FTP Host:</td><td><input type="text" name="ftp_host"></td>get_plugin_data
    	</tr>
    	<tr>
	    	<td>FTP User:</td><td><input type="text" name="ftp_uid"></td>
    	</tr>
    	<tr>
	    	<td>FTP Password: </td><td><input type="password" name="ftp_pwd"></td>
    	</tr>
    	<tr>
	    	<td>FTP Remote directory: </td><td><input type="text" name="ftp_dir"></td>
    	</tr>
    	<tr>
	    	<td colspan="2" align="right"><input type="submit" value="<?php echo getTranslation('ftp_button'); ?>"></td>
    	</tr>
    	</table>
    </form>
    <?php

    displayStyledButton(createAdminLink($GLOBALS['MENU']->getID(), array( PARAM_DIRECTORY => urlencode($modul->getName()), PARAM_MODE => MODE_INSTALL, PARAM_SEEN_README => 'true' )), getTranslation('update_execute'));
}

/**
 * Displays the Readme file.
 */
function showReadme($modul)
{
	$readmeFileName = $modul->getFullPath() . $modul->getReadmeFilename();
    if (file_exists($readmeFileName))
    {
        echo createBackLink($GLOBALS['MENU']->getID());
        ?>
        <h2>&gt;&gt; <?php echo $modul->getTitle(); ?> &lt;&lt;</h2>
        <p>
            <b><u><?php echo getTranslation('update_attention'); ?></u></b>
            <br>
            <?php echo getTranslation('update_read_carefully'); ?>
        </p>
        <br />
        <div style="background-color:#ffffff;color:#000000;padding:10px;width:100%;height:auto;border:1px solid #000000;">
        <pre><?php
            readfile($readmeFileName);
        ?></pre>
        </div>
        <br><br><br>
        <?php
    }
    else
    {
        $GLOBALS['LOGGER']->logError( getTranslation('error_update_no_readme') . ': ' . $readmeFileName . '!' );
        displayError( getTranslation('error_update_no_readme') . ':<br/>' . $readmeFileName);
    }

    displayStyledButton(createAdminLink($GLOBALS['MENU']->getID(), array( PARAM_DIRECTORY => urlencode($modul->getName()), PARAM_MODE => MODE_INSTALL, PARAM_SEEN_README => 'true' )), getTranslation('update_execute'));
}

function displayStyledButton($href, $title)
{
	echo '<div align="right" id="darkBackground">';
	echo '<a class="textLink" href="'.$href.'">';
	echo '<img style="border-width:0px;" valign="middle" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'update_perform.png" alt="'.$title.'" title="'.$title.'"> ';
	echo $title;
	echo '</a>';
	echo '</div>';
}

/**
 * Dispays the Index Screen with the Update Categories and all their updates.
 */
function showIndexScreen($ignoreList = array(), $remoteExtensions = array(), $mode = MODE_INDEX)
{

    if(!is_writable(UPDATE_PATH)) {
        displayError( sprintf( getTranslation('upload_path_not_writable'), UPDATE_PATH) );
    }

    //$mods = getAllUpdateModules($ignoreList);
    $mods = getGroupedUpdateModules($ignoreList);

    // find all plugins

    $all_plugins = get_plugins();
    $all_active = get_active_plugins();

    $deactive = array();
    $active = array();
    foreach($all_plugins AS $pname => $pdata) {
        if(!array_key_exists($pname,$all_active))
            $deactive[$pname] = $pdata;
        else
            $active[$pname] = $pdata;
    }

    $smarty = getAdminSmarty();
    $smarty->assign('DEACTIVATE_URL', createAdminLink($GLOBALS['MENU']->getID(), array(PARAM_MODE => 'deactivate')));
    $smarty->assign('ACTIVATE_URL', createAdminLink($GLOBALS['MENU']->getID(), array(PARAM_MODE => 'activate')));
    $smarty->assign('SEARCH_URL', createAdminLink($GLOBALS['MENU']->getID()));
    $smarty->assign('UPLOAD_URL', createAdminLink($GLOBALS['MENU']->getID()));
    $smarty->assign('PLUGINS_DEACTIVE', $deactive);
    $smarty->assign('PLUGINS_ACTIVE', $active);
    $smarty->assign('MODULES', $mods);
    $smarty->assign('PARAM_MODE', PARAM_MODE);
    $smarty->assign('REMOTE_EXTENSIONS', $remoteExtensions);
    $smarty->assign('ACTION', $mode);
    $smarty->display('AdminUpdates.tpl');
    unset($smarty);
}

function get_active_plugins() {
    $all_plugins = array();
    // load all configured plugins
    $sqlString = "SELECT name FROM {DB_PREFIX}plugins WHERE cid = {CID}";
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array(), true);
    $plugins = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    if($plugins->count() > 0)
    {
	    for($pi = 0; $pi < $plugins->count(); $pi++)
	    {
	        $plugin = $plugins->next();
	        if(file_exists(BIGACE_PLUGINS.$plugin['name'])) {
		        $all_plugins[$plugin['name']] = $plugin['name'];
	        }
	    }
    }
    return $all_plugins;
}

// taken from wordpress/wp-admin/includes/plugins.php and modified
function get_plugins($plugin_folder = '') {

	$plugins = array ();

	$plugin_files = array();
	// Files in wp-content/plugins directory
	$plugins_dir = @ opendir( BIGACE_PLUGINS );
	if ( $plugins_dir ) {
		while (($file = readdir( $plugins_dir ) ) !== false ) {
			if ( substr($file, 0, 1) == '.' )
				continue;
			if ( is_dir( BIGACE_PLUGINS.$file ) ) {
				$plugins_subdir = @ opendir( BIGACE_PLUGINS.$file );
				if ( $plugins_subdir ) {
					while (($subfile = readdir( $plugins_subdir ) ) !== false ) {
						if ( substr($subfile, 0, 1) == '.' )
							continue;
						if ( substr($subfile, -4) == '.php' )
							$plugin_files[] = "$file/$subfile";
					}
				}
                @closedir( $plugins_subdir );
			} else {
				if ( substr($file, -4) == '.php' )
					$plugin_files[] = $file;
			}
		}
	}
	@closedir( $plugins_dir );

	if ( !$plugins_dir || count($plugin_files) == 0 )
		return $plugins;

	foreach ( $plugin_files as $plugin_file ) {
		if ( !is_readable( BIGACE_PLUGINS.$plugin_file ) )
			continue;

		$plugin_data = get_plugin_data( $plugin_file );

		if ( is_null($plugin_data) || empty ( $plugin_data['name'] ) )
			continue;

		$plugins[$plugin_file] = $plugin_data;
	}

	uasort( $plugins, create_function( '$a, $b', 'return strnatcasecmp( $a["name"], $b["name"] );' ));

	return $plugins;
}


// taken from wordpress/wp-admin/includes/plugins.php and modified
function get_plugin_data( $plugin_file )
{
	$plugin_data = implode( '', file( BIGACE_PLUGINS.$plugin_file ));
	preg_match( '|Plugin Name:(.*)$|mi', $plugin_data, $plugin_name );
	preg_match( '|Plugin URI:(.*)$|mi', $plugin_data, $plugin_uri );
	preg_match( '|Description:(.*)$|mi', $plugin_data, $description );
	preg_match( '|Author:(.*)$|mi', $plugin_data, $author_name );
	preg_match( '|Author URI:(.*)$|mi', $plugin_data, $author_uri );

	if(empty ( $plugin_name ))
	    return null;

	if ( preg_match( "|Version:(.*)|i", $plugin_data, $version ))
		$version = trim( $version[1] );
	else
		$version = '';

	$description = trim( $description[1] );

	$name = $plugin_name[1];
	$name = trim( $name );
	$plugin = $name;
	if ('' != trim($plugin_uri[1]) && '' != $name ) {
		$plugin = '<a href="' . trim( $plugin_uri[1] ) . '" target="_blank" title="'.getTranslation('find_web_head').'">'.$plugin.'</a>';
	}

	if ('' == $author_uri[1] ) {
		$author = trim( $author_name[1] );
	} else {
		$author = '<a href="' . trim( $author_uri[1] ) . '" target="_blank" title="'.getTranslation('find_web_head').'">' . trim( $author_name[1] ) . '</a>';
	}

	return array('name' => $name, 'title' => $plugin, 'description' => $description, 'author' => $author, 'version' => $version, 'id' => $plugin_file);
}

admin_footer();
