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
 * Checks all required settings for BIGACE.
 */

if(!defined('_BIGACE_INSTALL_PARENT_')) {
    die('Not runnable alone, go to '.dirname(__FILE__).'/index.php');
}

function get_php_setting($val) {
	$r =  (ini_get($val) == '1' ? 1 : 0);
	return $r ? _CHECKUP_ON : _CHECKUP_OFF;
}

// indicates whether we can install or not
$criticalProblem = false;
$checkupErrors = array();
// --------------------------------------------------------------------------------------------------------
// -------------------------------------- [START] PHP Configurations --------------------------------------

$allSettings[] = array(
	'label' => 'PHP version >= 5.1',
	'state' => ( (version_compare(phpversion(), '5.1', ">=") === false) ? _CHECKUP_NO : _CHECKUP_YES ),
    'description' => 'You PHP Version is not compatible, at least PHP 5.1 is required!'
);
//$allSettings[] = array(
//	'label' => '- zlib compression support',
//	'state' => extension_loaded('zlib') ? _CHECKUP_YES : _CHECKUP_NO,
//  'description' => 'SimpleXML blablabla'
//);
$allSettings[] = array(
	'label' => '- Simple XML support',
	'state' => extension_loaded('SimpleXML') ? _CHECKUP_YES : _CHECKUP_NO,
	'statetext' => extension_loaded('xml') ? _CHECKUP_YES : _CHECKUP_NO,
    'description' => 'SimpleXML extension is not activated!'
);
$allSettings[] = array(
	'label' => '- XML support',
	'state' => extension_loaded('xml') ? _CHECKUP_YES : _CHECKUP_NO,
	'statetext' => extension_loaded('xml') ? _CHECKUP_YES : _CHECKUP_NO,
    'description' => 'XML support is not available!'
);
$allSettings[] = array(
	'label' => '- MySQL support',
	'state' => function_exists( 'mysql_connect' ) ? _CHECKUP_YES : _CHECKUP_NO,
    'description' => 'MySQL extension is not loaded!'
);
// --------------------------------------- [END] PHP Configurations ---------------------------------------
// --------------------------------------------------------------------------------------------------------


// --------------------------------------------------------------------------------------------------------
// ----------------------------------------- [START] PHP Settings -----------------------------------------

	$recommendedSettings = array(
		array( 'Safe Mode', 'safe_mode', _CHECKUP_OFF, '' ),
		array( 'Display Errors', 'display_errors', _CHECKUP_OFF, '' ),
		array( 'File Uploads', 'file_uploads', _CHECKUP_ON, '' ),
		array( 'Magic Quotes GPC', 'magic_quotes_gpc', _CHECKUP_ON, '' ),
		array( 'Magic Quotes Runtime', 'magic_quotes_runtime', _CHECKUP_OFF, '' ),
		array( 'Register Globals', 'register_globals', _CHECKUP_OFF, '' ),
		array( 'Output Buffering', 'output_buffering', _CHECKUP_OFF, '' ),
		array( 'Session auto start', 'session.auto_start', _CHECKUP_OFF, '' )
	);

	foreach ($recommendedSettings as $setting) {
		$phpSettings[] = array(
			'label'   => $setting[0],
			'setting' => $setting[2],
			'actual'  => get_php_setting( $setting[1] ),
			'state'   => get_php_setting( $setting[1] ) == $setting[2] ? _CHECKUP_YES : _CHECKUP_NO,
			'msg'     => (isset($setting[3]) ? $setting[3] : '')
		);
	}

    $phpSettings[] = array(
        'label'   => 'Image Support',
        'setting' => _CHECKUP_ON,
        'actual'  => function_exists('imagecreatetruecolor'),
        'state'   => function_exists('imagecreatetruecolor') ? _CHECKUP_YES : _CHECKUP_NO,
		
    );
    $phpSettings[] = array(
        'label'   => 'GIF Support',
        'setting' => _CHECKUP_ON,
        'actual'  => function_exists('imagegif') && function_exists("imagecreatefromgif"),
        'state'   => (function_exists('imagegif') && function_exists("imagecreatefromgif")) ? _CHECKUP_YES : _CHECKUP_NO
    );
    $phpSettings[] = array(
        'label'   => 'JPEG Support',
        'setting' => _CHECKUP_ON,
        'actual'  => function_exists('imagejpeg') && function_exists("imagecreatefromjpeg"),
        'state'   => (function_exists('imagejpeg') && function_exists("imagecreatefromjpeg")) ? _CHECKUP_YES : _CHECKUP_NO
    );
    $phpSettings[] = array(
        'label'   => 'Apache Header',
        'setting' => _CHECKUP_ON,
        'actual'  => function_exists('apache_request_headers'),
        'state'   => function_exists('apache_request_headers') ? _CHECKUP_YES : _CHECKUP_NO
    );

// ------------------------------------------ [END] PHP Settings ------------------------------------------
// --------------------------------------------------------------------------------------------------------


?>
<h1><?php echo getTranslation('required_settings_title'); ?></h1>
<table border="0" cellspacing="5">
    <tr>
        <td class="checkupInfo">
            <?php echo getTranslation('check_install_help'); ?>
        </td>
        <td class="checkupContent">
            <table border="0" width="100%">
            <?php
            foreach($allSettings AS $setting)
            {
                $cssClass  = ($setting['state'] == _CHECKUP_NO) ? "checkupFailure" : "checkupSuccess";
                ?>
                    <tr>
                        <td><?php echo $setting['label']; ?></td>
                        <td align="right" style="padding-right:20px;"><span class="<?php echo $cssClass; ?>">
                        <?php
                            if($setting['state'] == _CHECKUP_NO) {
                                echo '<img src="web/redled.png">'; 
                                $criticalProblem = true;
                                $checkupErrors[] = $setting['description'];
                            } else if($setting['state'] == _CHECKUP_YES) {
                                echo '<img src="web/greenled.png">'; 
                            } else {
                                echo $setting['state']; 
                            }
                        ?>
                        </span>
                        </td>
                    </tr>
                <?php
            }
            ?>
            </table>
        </td>
    </tr>
</table>
<?php
if(count($checkupErrors) > 0)
    foreach($checkupErrors AS $ce)
        displayError($ce);

$checkupErrors = array();
?>

<h1><?php echo getTranslation('check_settings_title'); ?></h1>
<table border="0" cellspacing="5">
    <tr>
        <td class="checkupInfo">
            <?php echo getTranslation('check_settings_help'); ?>
        </td>
        <td class="checkupContent">
            <table border="0" width="100%">
            <?php
            foreach($phpSettings AS $setting)
            {
                $cssClass  = ($setting['state'] == _CHECKUP_NO) ? "checkupFailure" : "checkupSuccess";
                ?>
                    <tr>
                        <td><?php echo $setting['label']; ?></td>
                        <td align="right" style="padding-right:20px;"><span class="<?php echo $cssClass; ?>">
                        <?php
                            $recommended = $setting['setting'];
                            $current = ($setting['state'] == _CHECKUP_YES ? $setting['setting'] : ($setting['setting'] == _CHECKUP_ON ? _CHECKUP_OFF : _CHECKUP_ON));

                            if($setting['state'] == _CHECKUP_NO) {
                                echo '<img src="web/yellowled.png" onMouseOver="overlib(\''.getTranslation('check_recommended').' <b>'.$recommended.'</b>,<br>'.getTranslation('check_setting').' <b>'.$current.'</b>\')" onMouseOut="nd()">'; 
								if(isset($setting['msg']) && strlen(trim($setting['msg'])) > 0) {
									echo '</span></td></tr><tr><td align="right" colspan="2">';
									echo '<span class="'. $cssClass . '">'.$setting['msg'];
								}
                            }
							else if($setting['state'] == _CHECKUP_YES) {
                                echo '<img src="web/greenled.png" onMouseOver="overlib(\''.getTranslation('check_recommended').' <b>'.$recommended.'</b>,<br>'.getTranslation('check_setting').' <b>'.$current.'</b>\')" onMouseOut="nd()">'; 
							}
                            else if($setting['state'] != _CHECKUP_NO){
                                echo $setting['state']; 
							}
                        ?>
                        </span></td>
                    </tr>
                <?php
            }
            ?>
            </table>
        </td>
    </tr>
</table>
<?php
$missingEmptyDirs = array();

foreach($GLOBALS['_INSTALL']['SYSTEM']['empty_folder'] as $newDir) {
	$newDir2 = $GLOBALS['_INSTALL']['DIR']['root'].'/'.$newDir;
	if(!file_exists($newDir2)) {
		if (!@mkdir($newDir2, $GLOBALS['_INSTALL']['dir_rights'])) {
			$missingEmptyDirs[] = $newDir;
		}
	}
}

if(count($missingEmptyDirs) > 0)
{
    $criticalProblem = true;

?>
<h1><?php echo getTranslation('required_empty_dirs'); ?></h1>
<table border="0" cellspacing="5">
    <tr>
        <td class="checkupInfo">
            <?php echo getTranslation('empty_dirs_description'); ?>
        </td>
        <td class="checkupContent">
            <table border="0" width="100%">
            <?php
            foreach($missingEmptyDirs as $oe)
            {
                $cssClass  = ($setting['state'] == _CHECKUP_NO) ? "checkupFailure" : "checkupSuccess";
                ?>
                    <tr>
                        <td><?php echo $oe; ?></td>
                        <td align="right" style="padding-right:20px;"><span class="<?php echo $cssClass; ?>">
                        <?php
                            echo '<img src="web/redled.png" />'; 
                        ?>
                        </span>
                        </td>
                    </tr>
                <?php
            }
            ?>
            </table>
        </td>
    </tr>
</table>
<?php
}

check_file_permission();

unset($folderPermissions);
unset($allSettings);
unset($phpSettings);
unset($recommendedSettings);

echo '<div style="clear:both;margin-top:10px;" />';

if ( canStartInstallation(false) && !$criticalProblem )
{
    displayNextButton(MENU_STEP_CORE, 'next');
}
else
{
    displayNextButton(MENU_STEP_CHECKUP, 'check_reload');
}
