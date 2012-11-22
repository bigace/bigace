<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
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
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 */

    define('ACTUAL_VERSION', '2.6');
    define('UPGRADE_INFO_URL', 'index.php');
    
    define('METHOD_INDEX', 0);
    define('METHOD_DB_CORE', 1);
    define('METHOD_DB_COMMUNITIES', 2);
    define('METHOD_FILE_CLEANUP', 3);
    define('METHOD_FINALIZE', 10);
    define('UPGRADE_DEBUG', false);
    define('UPGRADE_ERROR_WRONG_DIRECTORY', 1);
    define('UPGRADE_ERROR_DATABASE_CONNECTION', 2);
    define('UPGRADE_ERROR_MISSING_COMMUNITY_XML', 3);
    define('UPGRADE_ERROR_MISSING_DATABASE_XML', 4);
    define('UPGRADE_ERROR_FRESH_CONFIG', 5);
    define('UPGRADE_ERROR_FRESH_COMMUNITIES', 6);
	define('UPGRADE_DEFAULT_RIGHT_DIRECTORY', 0777);

    define('_BIGACE_DIR_ROOT', realpath(dirname(__FILE__)).'/');
    define('DATABASE_XML', _BIGACE_DIR_ROOT . 'system/sql/structure.xml');
    define('COMMUNITY_XML', _BIGACE_DIR_ROOT . 'system/sql/community.xml');
    define('SYSTEM_CONFIG', _BIGACE_DIR_ROOT . 'system/config/config.system.php');
    define('COMMUNITY_CONFIG', _BIGACE_DIR_ROOT . 'system/config/consumer.ini');
    
    $communities = array();

    $UPGRADE_ERROR = null;
    if(file_exists(_BIGACE_DIR_ROOT . 'system/config/consumer.ini'))
    {
        $_BIGACE = array();
        require_once( _BIGACE_DIR_ROOT . 'addon/adodb/adodb.inc.php' );
        require_once( _BIGACE_DIR_ROOT . 'addon/adodb/adodb-xmlschema03.inc.php' );
        require_once( SYSTEM_CONFIG );
        require_once( _BIGACE_DIR_ROOT . 'system/classes/parser/XmlToSqlParser.php' );

        $conf = parse_ini_file( COMMUNITY_CONFIG, true );
        foreach($conf AS $url => $settings) {
            if($url != '*' || !isset($communities[(string)$settings['id']])) {
                if($url == '*') $url = '<i>Default Community</i>';
                $communities[(string)$settings['id']] = array('name' => $url, 'extended' => $settings);
            }
        }

		if(count($communities) == 0) 
		{
		    $UPGRADE_ERROR = UPGRADE_ERROR_FRESH_COMMUNITIES;
		}

		foreach($_BIGACE['db'] AS $k => $v)
		{
			if(strpos($v, "{CID_") !== false) {
			    $UPGRADE_ERROR = UPGRADE_ERROR_FRESH_CONFIG;
			}
		}

		if(is_null($UPGRADE_ERROR))
		{
			$dbName = (isset($_BIGACE['db']['table']) ? $_BIGACE['db']['table'] : $_BIGACE['db']['name']);
		    $dbConnection = ADONewConnection( $_BIGACE['db']['type'] );
		    @$dbConnection->Connect( $_BIGACE['db']['host'], $_BIGACE['db']['user'], $_BIGACE['db']['pass'], $dbName );
		    if(!$dbConnection->IsConnected()) {
		        $UPGRADE_ERROR = UPGRADE_ERROR_DATABASE_CONNECTION;
		    } 

		    if(!file_exists(DATABASE_XML)) {
		        $UPGRADE_ERROR = UPGRADE_ERROR_MISSING_DATABASE_XML;
		    }

		    if(!file_exists(COMMUNITY_XML)) {
		        $UPGRADE_ERROR = UPGRADE_ERROR_MISSING_COMMUNITY_XML;
		    }
		}
    } 
    else 
    {
        $UPGRADE_ERROR = UPGRADE_ERROR_WRONG_DIRECTORY;
    }

	// ------------------------------------------------------------------------
    $deleteSystemFiles = array(
    				// -------------------------------------------------------------------------------------
    				// deprecated with 2.4 (not used, replaced, removed ...)
    				"misc/install/web/installation.png",
                    "public/FCKeditor/",
                    "public/spring_flavour/",
                    "public/system/statistic.swf",
                    "public/system/javascript/plaintext_editor.js",
                    "public/system/images/bigace.gif",
                    "public/system/images/logosmall.gif",
                    "public/system/style/standard/style/welcome.css",
                    "public/system/style/standard/style/editor/image.gif",
                    "public/system/style/standard/style/editor/save.gif",
                    "system/libs/adodb/",
                    "system/editor/fckeditor/close.php",
                    "system/editor/fckeditor/infos.php",
                    "system/editor/fckeditor/readonly.php",
                    "system/editor/fckeditor/fckeditor_form.php",
                    "system/classes/portlets/TranslatedPortlet.php",
                    "system/language/de/fileUpload.properties",
                    "system/language/en/fileUpload.properties",
                    "system/language/de/imagesUpload.properties",
                    "system/language/en/imagesUpload.properties",
    				"system/admin/frame_content.php",
                    "system/admin/frame_frameset.php",
                    "system/admin/frame_header.php",
                    "system/admin/frame_navigation.php",
                    "system/admin/frame_plain.php",
                    "system/admin/templates/AdminHeader.tpl.htm",
                    "system/admin/templates/StatisticFlashTemplate.tpl.html",
                    "system/admin/plugins/category/",
                    "system/admin/plugins/communities/",
                    "system/admin/plugins/file/",
                    "system/admin/plugins/fright/",
                    "system/admin/plugins/help/",
                    "system/admin/plugins/image/",
                    "system/admin/plugins/smarty/",
                    "system/admin/plugins/system/",
                    "system/admin/plugins/user/",
                    "system/admin/plugins/menu/itemMenu/",
    				"system/admin/plugins/menu/itemMenuCreate/",
                    "system/admin/plugins/menu/menuWorkflow/",
    				// -------------------------------------------------------------------------------------
    				// deprecated with 2.5 (not used, replaced, removed ...)
    				"system/admin/plugins/menu/menuTree/", // moved files 
    				"system/admin/plugins/includes/menu_functions.php",
                    "system/classes/addon/AddOnManager.php", // unused
    				"system/classes/addon/AddOnNames.php", // unused
    				"system/classes/item/UniqueNameService.php", // moved to seo package
    				"system/classes/util/ss_zip.class.php", // moved to addon package
					"system/classes/email/EmailConstants.php",
    				"system/admin/plugins/menu/menuTree.ini", // direct linked, not dynamic any longer
    				"system/admin/plugins/menu/menuAdmin/frameset.php", // moved and renamed
    				"system/libs/php4_layer.inc.php", // only supporting php 5 now
    				"system/libs/php5_layer.inc.php", // only supporting php 5 now
                    "system/language/de/frightGroup.properties",
                    "system/language/en/frightGroup.properties",
                    "system/language/de/imagesAdmin.properties",
                    "system/language/en/imagesAdmin.properties",
                    "system/language/de/fileAdmin.properties",
                    "system/language/en/fileAdmin.properties",
                    "system/language/de/fckeditor.properties",
                    "system/language/en/fckeditor.properties",
                    "system/language/de/applications.properties",
                    "system/language/en/applications.properties",
     				"system/admin/plugins/menu/jsMenuTree.ini", 
     				"system/admin/plugins/menu/menuAttributes.ini", 
     				"system/admin/plugins/menu/menuReorder.ini", 
     				"system/admin/plugins/menu/menuTree.php", 
     				"system/admin/plugins/menu/menuAdmin/jsnavi.php", 
     				"system/admin/plugins/menu/menuAdmin/jsnavi_javascript.php", 
      				"system/admin/plugins/menu/menuAdmin/menuAttributes.php", 
     				"system/admin/plugins/menu/menuAdmin/reorder.php", 
     				"system/admin/plugins/menu/menuAdmin/", 
                    "system/admin/plugins/menu/menuAdmin/translation/de/menuTree.properties", 
    				"system/admin/plugins/menu/menuAdmin/translation/de/menuAttributes.properties",
    				"system/admin/plugins/menu/menuAdmin/translation/de/jsMenuTree.lang.php",
    				"system/admin/plugins/menu/menuAdmin/translation/de/menuReorder.lang.php",
					"system/admin/plugins/menu/menuAdmin/translation/en/menuTree.properties", 
    				"system/admin/plugins/menu/menuAdmin/translation/en/menuAttributes.properties",
    				"system/admin/plugins/menu/menuAdmin/translation/en/jsMenuTree.lang.php",
    				"system/admin/plugins/menu/menuAdmin/translation/en/menuReorder.lang.php",
    				"system/admin/plugins/menu/menuAdmin/translation/",
    				"system/admin/plugins/includes/menu_item_listing.php",
    				"system/admin/plugins/itemModul.php", // renamed
    				"system/language/de/itemModul.properties", // renamed
    				"system/language/en/itemModul.properties", // renamed
    				"system/admin/templates/ItemGroupRights.tpl.htm",
    				"system/admin/templates/AdminIndex.tpl.htm",
                    "system/admin/templates/AdminIndexSubmenu.tpl.htm",
    				"system/admin/templates/ConfigurationList.tpl.htm",
    				"system/admin/templates/AdminFunctionalGroupRights.tpl.htm",
    				"system/admin/templates/AdminFunctionalGroupsList.tpl.htm",
    				"system/admin/templates/AdminModulesList.tpl.htm",
    				"system/admin/templates/UploadFormular.tpl.htm",
    				"system/admin/templates/AdminUserSetting.tpl.htm",
                    "system/editor/askForSave.tpl",
    				"system/editor/plaintext/javascript.php", // not required - using markItUp editor
    				"public/system/style/standard/category_edit.png",
    				"public/system/webfx/sortabletable/",
    				"public/system/webfx/tabpane/",
    				"public/system/webfx/xloadtree111/",
    				"public/system/webfx/",
				    "public/system/style/standard/style/navi.js",
    				"addon/FCKeditor/editor/filemanager/browser/bigace/frmupload.html",
    				// -------------------------------------------------------------------------------------
    				// deprecated with 2.6 (not used, replaced, removed ...)
    				"system/admin/plugins/includes/jsnavi_javascript.php",
    				"consumer/cid{CID}/config/config.email.cid{CID}.inc.php",
    				"consumer/cid{CID}/install/applications.xml",
    				"consumer/cid{CID}/install/redirect.xml",
    				"consumer/cid{CID}/install/authentication.xml",
    				"consumer/cid{CID}/include/standard_htmlhead.php",
    				"system/admin/templates/AdminCredits.tpl.htm",
    				"system/admin/functions.inc.php",
    				"system/admin/menuStructure.php",
    				"addon/jquery/tablesorter/themes/bigace/style.css",
    				"addon/jquery/tablesorter/themes/bigace/asc.gif",
    				"addon/jquery/tablesorter/themes/bigace/bg.gif",
    				"addon/jquery/tablesorter/themes/bigace/desc.gif",
    				"addon/jquery/tablesorter/themes/bigace/",
    				"public/system/style/standard/toolbar.css",
    				"system/sql/user_attribute_set.sql",
    				"system/sql/user_attributes_select.sql",
    				"system/sql/user_deactivate.sql",
        			"system/sql/user_activate.sql",
        			"system/sql/user_set_password.sql",
        			"system/sql/user_change_email.sql",
    				"system/sql/user_change_language.sql",
        			"system/sql/user_delete.sql",
        			"system/sql/user_create.sql",
    				"system/sql/user_attributes_delete.sql",
        			"system/sql/user_enumeration.sql",
        			"system/sql/user_select_by_name.sql",
    				"system/sql/user_select_by_attribute.sql",
    				"system/sql/user_select_by_authentication.sql",
    				"system/sql/user_select_name_exists.sql",
    				"system/sql/user_select.sql",
    				"system/sql/user_change.sql",
    				"system/sql/search_item_fulltext.sql",
    				"system/sql/search_item_fulltext_no_rights.sql",
    				"system/admin/templates/AdminTemplates.tpl.htm",
    				"system/admin/templates/DesignEditor.tpl.htm",
    				"system/admin/templates/AdminStylesheet.tpl.htm",
    				"system/admin/templates/AdminContentHeader.tpl.html",
    );
    $copyCommunityFiles = array(
                    "public/cid{CID}/css/search.css",
    );
    $deleteCommunityFiles = array(
    				// -------------------------------------------------------------------------------------
    				// deprecated with 2.4 (not used, replaced, removed ...)
    				"consumer/cid{CID}/install/blix_design.sql",
                    "consumer/cid{CID}/install/db_data_cid.sql",
                    "consumer/cid{CID}/adodb/",
                    "consumer/cid{CID}/config/config.statistic.cid{CID}.inc.php",
    				"consumer/cid{CID}/config/config.system.cid{CID}.inc.php",
                    "consumer/cid{CID}/config/config.select.inc.php",
    				"consumer/cid{CID}/presentation/definition/BLIX.php",
                    "consumer/cid{CID}/presentation/layout/spring_flavour/",
    				// -------------------------------------------------------------------------------------
    				// deprecated with 2.5 (not used, replaced, removed ...)
				    "consumer/cid{CID}/modul/displayContent/translation/en/modul.lang.php",
                    "consumer/cid{CID}/modul/displayContent/translation/en/",
    				"consumer/cid{CID}/modul/displayContent/translation/modul.lang.php",
    				"consumer/cid{CID}/modul/displayContent/translation/",
    				// -------------------------------------------------------------------------------------
    				// deprecated with 2.6 (not used, replaced, removed ...)
    				"consumer/cid{CID}/config/config.email.cid{CID}.inc.php",
    				"consumer/cid{CID}/install/applications.xml",
    				"consumer/cid{CID}/install/redirect.xml",
    				"consumer/cid{CID}/install/authentication.xml",
    				"consumer/cid{CID}/include/standard_htmlhead.php",
    );
    $createCommunityDirs = array(
                    "consumer/cid{CID}/cache/",
                    "consumer/cid{CID}/install/",
                    "consumer/cid{CID}/smarty/",
                    "consumer/cid{CID}/smarty/cache/",
                    "consumer/cid{CID}/smarty/configs/",
                    "consumer/cid{CID}/smarty/templates_c/",
    );
    $createSystemDirs = array(
                    "addon/smarty/",
                    "addon/smarty/",
    				"plugins/",
                    "addon/smarty/configs/",
                    "addon/smarty/templates/",
                    "addon/smarty/templates_c/",
				    "system/admin/smarty/",
                    "system/admin/smarty/cache/",
                    "system/admin/smarty/configs/",
                    "system/admin/smarty/templates/",
                    "system/admin/smarty/templates_c/",
    );
    $communityXmlFiles = array(
                    COMMUNITY_XML,
                    _BIGACE_DIR_ROOT."consumer/cid{CID}/install/blix_design.xml",
    );
    $systemSqlToExecute = array(
    	// 2.5
	    //'UPDATE '.$_BIGACE['db']['prefix'].'statistics SET date = from_unixtime(timestamp) WHERE date = "0000-00-00"',
	    //'UPDATE '.$_BIGACE['db']['prefix'].'configuration SET type = "editor" WHERE package = "editor" and name="default.editor"',
		//'ALTER TABLE '.$_BIGACE['db']['prefix'].'design_contents DROP PRIMARY KEY, ADD PRIMARY KEY (cid, design, name)',
		// 2.6
		'DELETE FROM `'.$_BIGACE['db']['prefix'].'configuration` WHERE `package` = "blix.design" AND name = "show.bookmarking"',
		'DELETE FROM `'.$_BIGACE['db']['prefix'].'template` WHERE `name` = "AUTH-WELCOME-EMAIL"',
		'DELETE FROM `'.$_BIGACE['db']['prefix'].'configuration` WHERE `package` = "templates" AND `name` = "auth.email.welcome"',
		'DELETE FROM `'.$_BIGACE['db']['prefix'].'configuration` WHERE `package` = "admin.menu" AND `name` = "focus.on.select"'
		);
    $communitySqlToExecute = array(
	    // 2.5
    	//'REPLACE INTO '.$_BIGACE['db']['prefix'].'id_gen SELECT "{CID}", "item_1", MAX(id) FROM '.$_BIGACE['db']['prefix'].'item_1',
    	//'REPLACE INTO '.$_BIGACE['db']['prefix'].'id_gen SELECT "{CID}", "item_4", MAX(id) FROM '.$_BIGACE['db']['prefix'].'item_4',
    	//'REPLACE INTO '.$_BIGACE['db']['prefix'].'id_gen SELECT "{CID}", "item_5", MAX(id) FROM '.$_BIGACE['db']['prefix'].'item_5',
    	//'DELETE FROM '.$_BIGACE['db']['prefix'].'configuration WHERE cid = "{CID}" AND package = "blix.design" AND name = "copyright.footer"',
    );
	// ------------------------------------------------------------------------
	

    $method = isset($_POST['method']) ? 0+$_POST['method'] : (isset($_GET['method']) ? 0+$_GET['method'] :METHOD_INDEX);


    function stderr($msg, $title = '') {
        echo '<br><span style="color:red;"><b>'.$title.' =&gt; ' . $msg.' !!!</b></span>';
    }

    function stdinfo($msg, $title = '') {
        echo '<br/><b>' .($title == '' ? '' : '<span style="color:green;">'.$title.'</span>: ') . $msg . '</b>';
    }

    function environmentError($id) {
        $title = 'Critical error';
        $msg = 'An unknown critical error occured during Upgrade. Please visit <a href="http:/www.bigace.de/forum/" target="_blank">http:/www.bigace.de/forum/</a>!';
        if($id == UPGRADE_ERROR_WRONG_DIRECTORY) {
            $title = 'Wrong directory';
            $msg = 'This file must be placed into the BIGACE Root Directory! <br/> Cannot find the File /system/config/config.system.php';
        }
        else if($id == UPGRADE_ERROR_DATABASE_CONNECTION) {
            $title = 'Problem connecting to Database';
            $msg = 'A connection to your Database could not be established.  <br/> Values are fetched from /system/config/config.system.php. Please check your configuration.';
        }
        else if($id == UPGRADE_ERROR_MISSING_DATABASE_XML) {
            $title = 'File missing';
            $msg = 'The File '.DATABASE_XML.' is missing.  <br/> If you do not have it, download the latest release and copy it to the shown directory.';
        }
        else if($id == UPGRADE_ERROR_MISSING_COMMUNITY_XML) {
            $title = 'File missing';
            $msg = 'The File '.COMMUNITY_XML.' is missing.  <br/> If you do not have it, download the latest release and copy it to the shown directory.';
        }
        else if($id == UPGRADE_ERROR_FRESH_CONFIG) {
            $title = 'Configuration failure';
            $msg = 'Your system configuration '.SYSTEM_CONFIG.' is in original state.<br>Did you re-insert your backup before starting the Upgrade?<br/>Please check your config file <b>'.SYSTEM_CONFIG.'</b> and reload this page again!';
        }
        else if($id == UPGRADE_ERROR_FRESH_COMMUNITIES) {
            $title = 'Community failure';
            $msg = 'No community configured in '.COMMUNITY_CONFIG.'! The file seems to be in original state.<br>Did you re-insert your backup before starting the Upgrade?<br/>Please check your config file <b>'.COMMUNITY_CONFIG.'</b> and reload this page again!';
        }

        echo '<div class="updateBox" style="color:red;">' . "\n";
        echo '<h2>'.$title.'</h2>' . "\n";
        echo $msg . "\n";
        echo '<br/><br/>&gt;&gt; In order to complete the Update, you have to fix the problem before. &lt;&lt;';
        echo '</div>' . "\n";
    }
    
    function nextStepButton($mode) {
        echo '
                        <form name="" action="" method="POST">
                            <input type="hidden" name="method" value="'. $mode .'">
                            <br/>Hit the button to perform the next Upgrade Step. <input type="submit" value="Next...">
                        </form>
        ';
    }

    function deleteFile($filename) {
        $fullName = dirname(__FILE__).'/'.$filename;
        if(file_exists($fullName)) {
            if(is_file($fullName)) {
                if(!is_writable($fullName)) {
                    stderr('File is protected: ' . $filename, 'Cannot delete File!');
                } else {
                    if(!@unlink($fullName))
                        stderr('Failed deleting: ' . $filename, 'Cannot delete File!');
                }
            }
            else if(is_dir($fullName)) {
	        	$handle=opendir($fullName);
	        	while (false !== ($file = readdir ($handle))) {
	        		if($file != "." && $file != "..") {
                        //stderr('Found: ' . str_replace('//','/',$filename.'/'.$file));
                        deleteFile(str_replace('//','/',$filename.'/'.$file));
                    }
                }
      		    closedir($handle); 
                if(!is_writable($fullName)) {
                    stderr('Directory is protected: ' . $filename, 'Cannot delete Directory!');
                } else{
                    if(!@rmdir($fullName))
                        stderr('Failed deleting: ' . $filename, 'Cannot delete Directory!');
                }
            }
        }
    }
    
?>
<html>
<head>
    <title>BIGACE UPGRADE</title>
    <style type="text/css">
    body, td, p, th { font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size:12px; }
    body { margin:0px; background-color:#eeeeee; }
    .outerTable { margin-top:10px; background-color:#eeeeee; border:0px; width:100%;}
	.header
	{
		background-image: url(http://www.bigace.de/public/bigace/bg2.jpg);
		background-repeat: repeat-x;
		background-color: #7bb000;
		padding: 0px 0px 0px 20px;
        margin-bottom:0px;
		font-size: 18px;
		border-bottom: 1px solid black;
        height:60px;
	}
    .name {font-weight:bold;font-size:22px; }
    .upgradecolumn { padding-left:20px;padding-right:20px;border:0px;padding-bottom:15px;  }
    .updateBox { background-color:#ffffff;border:1px solid #999999;padding:5px; }
    h1 { margin:0px; }
    h2 { margin:0px; }
    </style>
</head>
<body>
		<div class="header"><img src="public/system/images/bigace_logo.jpg" border="0"></div>
        <table class="outerTable" cellspacing="0" cellpadding="0" align="center">
        <tr><td valign="top" class="upgradecolumn">
            <div class="updateBox">
            <h1>UPGRADE TOOLKIT</h1>
            This tool will upgrade your BIGACE CMS to version <?php echo ACTUAL_VERSION; ?>.
            <br/><br/>
            But before you start the upgrade, remember to <b>BACKUP, BACKUP, BACKUP</b>!!!!!
            </div>
        </td></tr>
<?php
if($UPGRADE_ERROR != null)
{
    echo '<tr><td valign="top" class="upgradecolumn">' . "\n";
    environmentError($UPGRADE_ERROR);
    echo '</td></tr>' . "\n";
} 
else
{
    ?>
        <tr><td valign="top" class="upgradecolumn">
            <div class="updateBox">
            <h2>Step <?php echo METHOD_DB_CORE; ?>: Core Upgrade</h2>
            In the first step, the Database and Core System will be updated. Then all community data will be upgraded, 
            to be compatible with <?php echo ACTUAL_VERSION; ?>.
            <br/>
            <?php 
        if($method == METHOD_DB_CORE) 
        {
			// create new system directorys
            if(count($createSystemDirs) > 0) {
				foreach($createSystemDirs AS $newDir) {
					$newDir2 = dirname(__FILE__).'/'.$newDir;
					if(!file_exists($newDir2)) {
						if (!@mkdir($newDir2, UPGRADE_DEFAULT_RIGHT_DIRECTORY)) {
        					stderr( 'Failed creating Directory ' . $newDir, "Error creating Directory" );
                        	$error = true;
						}
					}
				}

                if(!$error)
                    stdinfo('Created System Directorys', 'Success');
            }   
            
            if(!file_exists(DATABASE_XML)) {
                stderr('XML Structure File missing: ' . DATABASE_XML, 'File missing');
            }
            else {
                    $schema = new adoSchema($dbConnection);
        			$schema->ExistingData(XMLS_MODE_UPDATE);
                    $schema->SetUpgradeMethod('ALTER');
                    $schema->SetPrefix($_BIGACE['db']['prefix'], FALSE);
                    $sql = @$schema->ParseSchema(DATABASE_XML);
                    if($sql === FALSE) {
                        stderr('Could not parse Database Structure File !', 'XML Error');
                    }
                    else {
                        $sql = array_merge($sql, $systemSqlToExecute);
                        // for debugging
                        if(UPGRADE_DEBUG)
        	    	        foreach($sql AS $statement) 
                                echo $statement.'<br>';
                        $result = $schema->ExecuteSchema($sql, true);
                        if($result == 0) {
                            stderr('Could not install DB: ' . $result, 'DB Error');
                        } else {
                            stdinfo('Upgraded your database!', 'Success');

                            nextStepButton(METHOD_DB_COMMUNITIES);
                        }

                    }
                    unset ($sql);            
            }
	
        } else if ($method > METHOD_DB_CORE) {
            stdinfo('Core upgrade done!');
        }
            ?>
            </div>
        </td></tr>
        <tr><td valign="top" class="upgradecolumn">
            <div class="updateBox">
            <h2>Step <?php echo METHOD_DB_COMMUNITIES; ?>: Community Upgrade</h2>
            This step updates all your Communities, Database and Filesystem.
            <br/>
            <?php 
            if($method < METHOD_DB_COMMUNITIES) 
            {
            ?>
            Following Communities will be upgraded: <br/>
            <ul>
                <?php
                    foreach($communities AS $id => $settings) {
                        echo '<li>'.$settings['name'].' (ID: '.$id.')</li>';
                    }
                ?>
            </ul>
            <?php
            }
            else if($method == METHOD_DB_COMMUNITIES) 
            {
                foreach($communities AS $id => $settings) { // alle communities

					// copy all new files from template community template directory to existing communities
                	foreach($copyCommunityFiles AS $copyFileName) 
					{
                        $error = false;
						$newFile1 = $copyFileName;
						$newFile2 = str_replace('{CID}',$id,$newFile1);

						if(!copy(dirname(__FILE__).'/'.$newFile1, dirname(__FILE__).'/'.$newFile2)) {
        					stderr( 'Failed copying File ' . $newFile1 . ' for Community '.$settings['name'].' (ID: '.$id.')', "Error creating Directory" );
                        	$error = true;
						}
                        if(!$error)
                            stdinfo('Fixed Template Files for Community '.$settings['name'].' (ID: '.$id.')', 'Success');
					}
					

                	foreach($communityXmlFiles AS $filenameXml) {	// alle xml-sql dateien
                		$replacer = array('{CID}' => $id, '{DEFAULT_LANGUAGE}' => $_BIGACE['DEFAULT_LANGUAGE']);
			            $myParser = new XmlToSqlParser();
			            $myParser->setAdoDBConnection($dbConnection);
			            $myParser->setIgnoreVersionConflict(false);
			            $myParser->setTablePrefix($_BIGACE['db']['prefix']);
			            $myParser->setReplacer($replacer);
			            $myParser->setMode(XML_SQL_MODE_UPDATE);
			            $myParser->parseFile($filenameXml);
			            $errors = $myParser->getError();
			            if(count($errors) >0) {
			                stderr('Some errors occured during XML Parsing for Community '.$id.', please correct them before continuing (<a href="?method='.METHOD_DB_COMMUNITIES.'&errors=true">show error</a>)', 'Error parsing: ' . $filenameXml);
			                if(isset($_GET['errors'])) {
			                    foreach($errors AS $error) {
			                        stderr($error);
			                    }
			                }
			            } else {
			                $error = false;
			                $sqls = $myParser->getSqlArray();
			                foreach($communitySqlToExecute AS $tempSQL) {
	    						foreach($replacer AS $search => $replace) {
			                		$tempSQL = str_replace($search,$replace,$tempSQL);
	    						}
			                	$sqls[] = $tempSQL;
			                }
			                foreach($sqls AS $statement) {
                                $res = $dbConnection->Execute($statement);
			                    if($res === FALSE) {
			                        stderr('A problem occured when executing the Statement: ' . $statement, 'Error executing SQL');
			                        $error = true;
			                    }
			                }
			                if(!$error)
			                    stdinfo('Upgraded Community Database '.$settings['name'].' (ID: '.$id.') with: ' . $filenameXml, 'Success');
			            }    	
                	}
				}
                
                if(count($createCommunityDirs) > 0) {
                    foreach($communities AS $id => $settings) {
                        $error = false;
						foreach($createCommunityDirs AS $newDir) {
							$newDir1 = str_replace('{CID}',$id,$newDir);
							$newDir2 = dirname(__FILE__).'/'.$newDir1;
							if(!file_exists($newDir2)) {
								if (!@mkdir($newDir2, UPGRADE_DEFAULT_RIGHT_DIRECTORY)) {
                					stderr( 'Failed creating Directory ' . $newDir1 . ' for Community '.$settings['name'].' (ID: '.$id.')', "Error creating Directory" );
                                	$error = true;
        						}
							}
						}
                        if(!$error)
                            stdinfo('Created Directorys for Community '.$settings['name'].' (ID: '.$id.')', 'Success');
                	}                 
                }   
                nextStepButton(METHOD_FILE_CLEANUP);
            } 
            else if ($method > METHOD_DB_COMMUNITIES) {
                stdinfo('Community upgrade done!');
            }
            ?>
            </div>
        </td></tr>
        <tr><td valign="top" class="upgradecolumn">
            <div class="updateBox">
            <h2>Step <?php echo METHOD_FILE_CLEANUP; ?>: Cleanup</h2>
            This step cleans up your filesystem (remove no longer used files). <br/>
            <?php 
            if($method == METHOD_FILE_CLEANUP) 
            {
                // remove system files
                if(count($deleteSystemFiles) > 0) {
                    foreach($deleteSystemFiles AS $filename) {
                        deleteFile($filename);
                    }
                    stdinfo('Removed deprecated System Files!');
                }

                // remove community files
                if(count($deleteCommunityFiles) > 0) {
                    foreach($communities AS $id => $settings) {
                        foreach($deleteCommunityFiles AS $filename) {
                            deleteFile(str_replace('{CID}',$id,$filename));
                        }
                        stdinfo('Removed deprecated Community Files for '.$settings['name'].' (ID: '.$id.')');
                    }
                }

                nextStepButton(METHOD_FINALIZE);
            } 
            else if ($method > METHOD_FILE_CLEANUP) {
                stdinfo('Cleanup done!');
            }
            ?>
            </div>
        </td></tr>
        <?php 
        if($method == METHOD_FINALIZE) {
        ?>
        <tr><td valign="top" class="upgradecolumn">
            <div class="updateBox">
            <h2>Upgrade complete!</h2>
            <form action="<?php echo UPGRADE_INFO_URL; ?>" method="GET">
                The core upgrade is complete, REMEMBER TO DELETE THIS SCRIPT!<br/>
                <hr>
                <font color="red"><b>Hit the button, to be redirected to your upgraded website!</b></font>
                <hr>
                <input type="submit" value="Last Step...">
            </form>
            </div>
        </td></tr>
        <?php
        }
        else if($method == METHOD_INDEX) {
        ?>
        <tr><td valign="top" class="upgradecolumn">
            <div class="updateBox">
            <h2>Start Upgrade</h2>
            <form name="" action="" method="POST">
                <input type="hidden" name="method" value="<?php echo METHOD_DB_CORE; ?>">
                Hit the button to start the Upgrade. Make sure, you have a working backup! <input type="submit" value="Start...">
            </form>
            </div>
        </td></tr>
        <?php
        }
}
        ?>
        </table>
</body>
</html>