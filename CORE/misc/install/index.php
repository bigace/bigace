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

/**
 * This is the Main BIGACE Installation script.
 */

error_reporting(-1);
ini_set('display_errors', true);

// Define General Array used in all Scripts
$_BIGACE = array();
// not sure if this is required
$GLOBALS['_BIGACE'] = &$_BIGACE;
$_INSTALL = array();
// not sure if this is required
$GLOBALS['_INSTALL'] = &$_INSTALL;
// translation array
$LANG = array();
$GLOBALS['LANG'] = &$LANG; // this is required on some PHP environments

// -------------------------------------------------------
// -  YOU SHOULD NOT NEED TO ADJUST FOLLOWING VARIABLES  -
// -------------------------------------------------------
$_INSTALL['DIR']['relative']            = 'misc/install/'; 								    // Relative Path to Install Directory from BIGACE Root
$_INSTALL['DIR']['install']             = dirname(__FILE__); 								// Full Path to Install Directory
$_INSTALL['DIR']['public']              = 'public/';                                        // Path to Public Directory relative from Install directory
$_INSTALL['DIR']['languages']           = $_INSTALL['DIR']['install'].'/languages/';        // Path to the Language Files for the Installation Console

$_INSTALL['DIR']['home']                = '../..';      									// Path to BIGACE Root Directory
$_INSTALL['DIR']['root']                = realpath( $_INSTALL['DIR']['install'] . '/' . $GLOBALS['_INSTALL']['DIR']['home'] . '/' );

define('_DIR_INSTALL_HOME', $_INSTALL['DIR']['home']);
define('_DIR_INSTALL_ROOT', $_INSTALL['DIR']['root']);

$_INSTALL['DIR']['config_relative']     = '/system/config/';                                                    // Relative Path to System Config Files from BIGACE Root
$_INSTALL['DIR']['config']              = _DIR_INSTALL_HOME . $_INSTALL['DIR']['config_relative'];       // Path to System Config Files from here
$_INSTALL['DIR']['libs']                = _DIR_INSTALL_ROOT . '/system/libs/';       // Path to 3rd Party Libraries
$_INSTALL['DIR']['adodb']               = _DIR_INSTALL_ROOT . '/addon/adodb/';       // Path to the AdoDB Framework

define('_DIR_INSTALL_MAIN', $_INSTALL['DIR']['install']);
define('_DIR_INSTALL_LANG', $_INSTALL['DIR']['languages']);
define('_DIR_INSTALL_LIBS', $_INSTALL['DIR']['libs']);
define('_DIR_INSTALL_ADODB', $_INSTALL['DIR']['adodb']);

$_INSTALL['dir_rights']                 = 0755;                   							// Trying to create new directorys with these rights
$_INSTALL['file_rights']                = 0755;                   							// Trying to create files with these rights
$_INSTALL['dir_umask']                  = 0;                                                // The Umask new directorys will be created with
$_INSTALL['file_umask']                 = 0;                                                // The Umask new files will be created (or copied) with

$_INSTALL['db']['prefix']          		= '{DB_PREFIX}';

// ------------------------------------------
// -  DO NOT TOUCH THE FOLLOWING VARIABLES  -
// ------------------------------------------
$_INSTALL['replace']['dir']             = 'cid{CID}';

// ------------------------------------------
// -  LIST ALL KIND OF USED FILES			-
// ------------------------------------------

// IGNORE FILES FOR CONSUMER INSTALLATION
$_INSTALL['FILE']['ignore']        		= array('CVS', $_INSTALL['DIR']['relative'] . 'update', '.', '..');

// Create Database - adodb XML File!
$_INSTALL['FILE']['adodb_xml']          = $_INSTALL['DIR']['root'] . '/system/sql/structure.xml';

// File extension for all SQL Files that will be loaded from the install/uninstall (see below) directory
$_INSTALL['FILE']['sql_extension']		= 'sql';

// BIGACE basic configuration files
$_INSTALL['FILE']['config_system']      = $_INSTALL['DIR']['config'] . 'config.system.php';

// this array defines a list of files that will be copied if the Rewrite Engine is activated
$_INSTALL['FILE']['rewrite_enabled'] = array(
											$_INSTALL['DIR']['install'].'/access/root.htaccess'   => $_INSTALL['DIR']['root'] . '/.htaccess',
											$_INSTALL['DIR']['install'].'/access/public.htaccess' => $_INSTALL['DIR']['root'] . '/public/.htaccess'
                                       );

// this array defines a list of files that will be copied if the
// Security setting via .htaccess File is allowed
$_INSTALL['FILE']['security_enabled'] = array(
                                            //$_INSTALL['DIR']['install'].'/access/misc.htaccess'     => $_INSTALL['DIR']['root'] . '/misc/_.htaccess',
											//$_INSTALL['DIR']['install'].'/access/misc.htpasswd'     => $_INSTALL['DIR']['root'] . '/misc/.htpasswd',
											$_INSTALL['DIR']['install'].'/access/system.htaccess'   => $_INSTALL['DIR']['root'] . '/system/.htaccess',
											$_INSTALL['DIR']['install'].'/access/consumer.htaccess' => $_INSTALL['DIR']['root'] . '/consumer/.htaccess'
                                       );

// folder that might not come with the original BIGACE archive,
// cause they are empty in CVS but needed for the Core to work properly
$_INSTALL['SYSTEM']['empty_folder'] = array(
						                    "misc/logging/",
						                    "plugins/",
											"addon/smarty/",
						                    "addon/smarty/cache/",
						                    "addon/smarty/configs/",
						                    "addon/smarty/templates/",
						                    "addon/smarty/templates_c/",
											"system/admin/smarty/",
						                    "system/admin/smarty/cache/",
						                    "system/admin/smarty/configs/",
						                    "system/admin/smarty/templates/",
						                    "system/admin/smarty/templates_c/",
										);

// the following array defines a list of entrys that has to be writeable
// all directorys are referenced relative from the BIGACE Root directory
// we need this to pre-check the file rights
$_INSTALL['CONSUMER']['precheck_files'] = array(
                                        		'addon/b2evo/b2evo_captcha_tmp/',
                                        		'addon/smarty/',
                                        		'addon/smarty/cache/',
                                        		'addon/smarty/templates_c/',
												'consumer/',
                                        		$_INSTALL['DIR']['public'],
                                        		$_INSTALL['DIR']['relative'],
                                        		'misc/logging/',
                                        		'misc/updates/',
                                                'system/admin/plugins/extensions/',
                                                'system/admin/',
                                                'system/admin/smarty/',
                                                'system/admin/smarty/cache/',
                                                'system/admin/smarty/templates_c/',
                                        		$_INSTALL['DIR']['config_relative'],
                                        		$_INSTALL['DIR']['config_relative'] . 'config.system.php',
                                        		$_INSTALL['DIR']['config_relative'] . 'consumer.ini'
                                        	);
// -----------------------------------------------------------------------------------
// [END] CONFIGURATION FOR INSTALLATION SCRIPT
// -----------------------------------------------------------------------------------


define('_BIGACE_INSTALL_PARENT_', 'index.php');             // Yes, we are the parent script
define('_CID_',            '{CID}');                        // CID setting
define('_TABLE_WIDTH_',    '100%');                         // Width in px for all tables

define('_STATUS_DB_OK',        1);                          // Status if database installation is complete
define('_STATUS_DB_NOT_ALL',   2);                          // Status if database in not completely installed (for example: table is missing)
define('_STATUS_DB_NOT_OK',    3);                          // Status if database is not installed

define('_STATUS_CID_NONE',    3);

define('MENU_STEP_WELCOME',     '0');
define('MENU_STEP_CHECKUP',     '2');
define('MENU_STEP_CORE',        '3');
define('MENU_STEP_COMMUNITY',   '4');
define('MENU_STEP_SUCCESS',     '5');

require_once(dirname(__FILE__).'/classes.inc.php'); 			// All Classes used for Installation and Update
require_once(dirname(__FILE__).'/functions.inc.php');           // Some general functions for installation and update processes

require_once(_DIR_INSTALL_HOME . '/system/libs/constants.inc.php');  // Load BIGACE System Configuration
require_once($_INSTALL['FILE']['config_system']); 				            // Load BIGACE System Configuration

//directorys needed in some included scripts
$_BIGACE['DIR']['php_public']	= $_INSTALL['DIR']['root'] . '/public/';
$_BIGACE['DIR']['addon']        = $_INSTALL['DIR']['root'] . '/addon/';
$_BIGACE['DIR']['public']       = 'http://'. $_SERVER['HTTP_HOST'] . '/' . BIGACE_DIR_PATH . 'public/';

// load required classes without deeper dependency
import('classes.util.IOHelper');
import('classes.language.Language');


// default language for the Installation guide
$_INSTALL['LANGUAGE'] = 'en';

$available = getAvailableInstallationLanguages(); // available languages
$langs = getPreferredLanguages(); // sorted preferred languages

// look through sorted list of languages and compare
// use the first one, that matches, because it is the most preferred one!
foreach ($langs as $lang => $val) {
	foreach($available AS $check) {
		if (strpos($lang, $check) === 0) {
			$_INSTALL['LANGUAGE'] = $check;
			break 2;
		}
	}
}

// now a last check if the language really exists
// if not, fallback to german
$works = false;
foreach($available AS $check) {
	if($check == $_INSTALL['LANGUAGE'])
		$works = true;
}

// fallback for some rare circumstances
if(!$works) {
	$_INSTALL['LANGUAGE'] = 'de';
}

// ---------------------------------------- [START] Languages ----------------------------------------
// Define the Language settings
if (isset($_POST['INSTALL_LANGUAGE'])) {
    define('_INSTALL_LANGUAGE', $_POST['INSTALL_LANGUAGE']);
} else if (isset($_GET['LANGUAGE'])) {
    define('_INSTALL_LANGUAGE', $_GET['LANGUAGE']);
} else {
    define('_INSTALL_LANGUAGE', $_INSTALL['LANGUAGE']);
}

// make sure we get utf-8 encoded input data
header( "Content-Type:text/html; charset=UTF-8");

// load english
if (file_exists(_DIR_INSTALL_LANG.'en.php')) {
	require_once(_DIR_INSTALL_LANG.'en.php');
} else {
    require_once(_DIR_INSTALL_LANG.'de.php');
}

// load configured default language if not english and exists
if($_INSTALL['LANGUAGE'] != 'en' && file_exists($_INSTALL['DIR']['languages']._INSTALL_LANGUAGE.'.php')) {
    require_once(_DIR_INSTALL_LANG.$_INSTALL['LANGUAGE'].'.php');
}

// and then overwrite everything with existing translations for desired language
if(file_exists($_INSTALL['DIR']['languages']._INSTALL_LANGUAGE.'.php')) {
    require_once(_DIR_INSTALL_LANG._INSTALL_LANGUAGE.'.php');
}

// Definitions for the Pre-Checks
define('_CHECKUP_YES',  getTranslation('check_yes'));
define('_CHECKUP_NO',   getTranslation('check_no'));
define('_CHECKUP_ON',   getTranslation('check_on'));
define('_CHECKUP_OFF',  getTranslation('check_off'));

// ----------------------------------------- [END] Languages -----------------------------------------

$MENU           = extractVar('menu',MENU_STEP_WELCOME);
$DATA           = extractVar('data', array());
// might be deprecated - not used by the installer but probably by the used core classes
$LOGGER         = new Logger();

// -----------------------------------------------------------------------------------------
// --------------------  [START] PRINT THE INSTALLATION PAGE  ------------------------------
// -----------------------------------------------------------------------------------------


        switch ($MENU)
        {
            case MENU_STEP_CHECKUP:
	    			show_install_header($MENU);
	            	include_once ( 'checkup.inc.php' );
					show_install_footer($MENU);
                break;
            case MENU_STEP_CORE:
                    include_once ( 'install.db.inc.php' );
                break;
            case MENU_STEP_COMMUNITY:
	                include_once ( 'install.consumer.inc.php' );
                break;
            case MENU_STEP_SUCCESS:
                    show_install_header($MENU);
                    echo '<h1>' . getTranslation('menu_step_'.MENU_STEP_SUCCESS) . '</h1>';
                    echo getTranslation('community_install_good');
                    show_install_footer($MENU);
                break;
            default:
	    			show_install_header($MENU);
            		show_intro_page(MENU_STEP_CHECKUP);
					show_install_footer($MENU);
                break;
        } // end switch

    // #####################################################################################################
    //   functions follow
    // #####################################################################################################

	/**
     * Shows the Message, if a wrong menu was requested.
     */
    function showNotFoundMessages()
    {
        displayError('The requested Installation Step does not exist!');
    }

    /**
    * Displays the Main Page with a description to each Menu.
    */
    function show_intro_page($next)
    {
        if (file_exists($GLOBALS['_INSTALL']['DIR']['languages'] . '/welcome_'._INSTALL_LANGUAGE.'.html')) {
            echo '<h1>' . getTranslation('introduction'). '</h1>';
            readfile($GLOBALS['_INSTALL']['DIR']['languages'] . '/welcome_'._INSTALL_LANGUAGE.'.html');
        }

        echo '<h1>' . getTranslation('help_title'). '</h1>';
        echo '<p>' . getTranslation('help_text') . getHelpImage( getTranslation('help_demo') ) . '</p>';

        showLanguageChooser(MENU_STEP_WELCOME);

        displayNextButton($next, 'install_begin');
    }

    /**
     * Shows the HTML HEAD and all the things that should be displayed above the Main Screen.
     */
    function show_install_header($step)
    {
        ?>
        <html>
        <head>
            <title><?php echo getTranslation('intro'); ?></title>
            <link rel="stylesheet" href="web/install.css" type="text/css" />
            <script src="web/install.js" type="text/javascript"></script>
            <script src="web/overlib_mini.js" type="text/javascript"></script>
			<?php /* Input field tooltip, initialized in header */ ?>
			<link rel="stylesheet" href="web/form-field-tooltip.css" media="screen" type="text/css">
            <script src="web/rounded-corners.js" type="text/javascript"></script>
            <script src="web/form-field-tooltip.js" type="text/javascript"></script>
        </head>
        <body>
        <div id="overDiv" style="position:absolute;visibility:hidden; z-index:1000;"></div>
        <div class="outer">

            <h1 class="header">BIGACE Web CMS - <?php echo getTranslation('menu_title'); ?><span><?php echo getTranslation('thanks'); ?></span></h1>
            <div class="updateBox">

                <?php if($step > 1 && $step < 5) { ?>
                <p class="navEntry"><?php echo getTranslation('menu_step'); ?> <?php echo $step-1; ?> / 3: <?php echo getTranslation('menu_step_'.($step)); ?></p>
                <?php } ?>

                <div class="contentTD">
    <?php
    }

    /**
     * Shows the HTML HEAD and all the things that should be displayed above the Main Screen.
     */
    function show_install_footer($step)
    {
        ?>
                </div>
            </div>

        </div>

	    <script type="text/javascript">
	    var tooltipObj = new DHTMLgoodies_formTooltip();
	    tooltipObj.imagePath = 'web/';
	    tooltipObj.setTooltipPosition('right');
	    tooltipObj.setPageBgColor('#EEEEEE');
	    tooltipObj.setTooltipCornerSize(10);
	    tooltipObj.initFormFieldTooltip();
	    tooltipObj.setTooltipWidth(250) ;
	    tooltipObj.setCloseMessage("<?php echo getTranslation('form_tip_close'); ?>");
	    tooltipObj.setDisableTooltipMessage("<?php echo getTranslation('form_tip_hide'); ?>");
	    </script>

        </body>
        </html>
        <?php
    }


    function check_file_permission()
    {
        // Get Results for File Check
    	$folderPermissions = checkFileRights();
        $showChecks = false;

        foreach($folderPermissions as $folder)
        {
            if($folder['state'] != _CHECKUP_YES)
                $showChecks = true;
        }

        if($showChecks)
        {
        ?>
        <h1><?php echo getTranslation('check_files_title'); ?></h1>
        <table border="0" cellspacing="5">
            <tr>
                <td class="checkupInfo">
                    <?php echo getTranslation('check_files_help'); ?>
                </td>
                <td class="checkupContent">
                    <table border="0" width="100%">
                        <tr>
                            <td colspan="2"><span class="checkupLabel"><?php echo $GLOBALS['_INSTALL']['DIR']['root']; ?></span></td>
                        </tr>
                    <?php

                    foreach($folderPermissions as $folder)
                    {
                        if($folder['state'] != _CHECKUP_YES)
                        {
                        $cssClass  = ($folder['state'] == _CHECKUP_NO) ? "checkupFailure" : "checkupSuccess";
                        ?>
                            <tr>
                                <td><?php echo $folder['label']; ?></td>
                                <td align="right" style="padding-right:20px;"><span class="<?php echo $cssClass; ?>">
                                <?php
                                    if($folder['state'] == _CHECKUP_NO)
                                        echo '<img src="web/redled.png">';
                                    else if($folder['state'] == _CHECKUP_YES)
                                        echo '<img src="web/greenled.png">';
                                    else
                                        echo $folder['state'];
                                ?></span>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    ?>
                    </table>
                </td>
            </tr>
        </table>
        <?php
        }
    }

    /**
	 * Displays the Language Chooser.
	 */
	function showLanguageChooser($MENU)
    {
        echo '<h1>'.getTranslation('language_choose').'</h1>';
        echo '<p>'.getTranslation('language_text');
        showLanguageChooserForm($MENU);
        echo '</p>';
    }

    function showLanguageChooserForm($MENU)
    {
        echo '<form method="POST" action="'.createInstallLink($MENU).'" style="display:inline">';
        echo '<select name="INSTALL_LANGUAGE" onChange="this.form.submit()">';
        $langs = getAvailableInstallationLanguages();
        for($i=0; $i < count($langs); $i++) {
            echo '<option value="'.$langs[$i].'"';
            if (_INSTALL_LANGUAGE == $langs[$i])
                echo ' selected';
            echo '>'.getTranslation('language_'.$langs[$i]).'</option>';
        }
        echo '</select>';
        echo '&nbsp;<br>';
        echo '<noscript><input class="langChooser" id="languageButton" name="languageButton" type="submit" value="'.getTranslation('language_button').'"></noscript>';
        echo '</form>';
    }

    function displayNextButton($next, $translationKey)
    {
        displayNextButtonLink(createInstallLink($next), $translationKey);
    }

    function displayNextButtonLink($link, $translationKey)
    {
        echo '<div align="right">';
        echo '<a href="'.$link.'" class="buttonLink">' . getTranslation($translationKey). ' &gt;&gt;</a>';
        echo '</div>';
    }
