<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.<br>Copyright (C) Kevin Papst.
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
 * Functions that ae used within the Install/Uninstall scripts!
 */

// exit if we are not included in the main installation script
if(!defined('_BIGACE_INSTALL_PARENT_')) {
    die('Not runnable alone, go to '.dirname(__FILE__).'/index.php');
}

$helpImageCounter = 0;

function getPreferredLanguages() 
{
	$langs = array();
	if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && strlen($_SERVER['HTTP_ACCEPT_LANGUAGE']) > 0)
	{
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			// break up string into pieces (languages and q factors)
			preg_match_all('/([a-z]{2}(-[a-z]{2})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);

			if (count($lang_parse[1])) {
				// create a list like "en" => 0.8
				$langs = array_combine($lang_parse[1], $lang_parse[4]);
			
				// set default to 1 for any without q factor
				foreach ($langs as $lang => $val) {
				    if ($val === '') $langs[$lang] = 1;
				}

				// sort list based on value	
				arsort($langs, SORT_NUMERIC);
			}
		}
	}
	return $langs;
}



function getDefaultLanguageChooser($tooltip = null) 
{
    $def_languages = '<select'.($tooltip != null ? ' tooltipText="'.$tooltip.'"' : '').' name="data[default_lang]">';
    $langs = IOHelper::getFilesFromDirectory(_BIGACE_LANGUAGE_PATH, 'ini', false);

    foreach($langs AS $lang) 
    {
        $language = new Language( stripFileExtension($lang) );
        $sel = '';
        if ($language->getLocale() == _INSTALL_LANGUAGE)
            $sel = ' selected';
        $def_languages .= '<option value="'.$language->getLocale().'"'.$sel.'>'.getTranslation('language_'.$language->getLocale(), $language->getName()).'</option>';
    }
    $def_languages .= '</select>';
    unset($langs);
    return $def_languages;
}

/**
 * Loads a Class from the BIGACE class directory.
 */
function loadClass($package, $classname) 
{
	include_once(_BIGACE_DIR_ROOT.'/system/classes/'.$package.'/'.$classname.'.php');
}

function import($name)
{
    include_once(_BIGACE_DIR_ROOT.'/system/'.str_replace('.', DIRECTORY_SEPARATOR, $name) . '.php');
}

/**
 * Checks if the all preconfigured directorys and files have the correct right settings.
 */
function checkFileRights()
{
	foreach ($GLOBALS['_INSTALL']['CONSUMER']['precheck_files'] as $folder) {
		$folderPermissions[] = array(
			'label' => $folder,
			'state' => is_writeable( $GLOBALS['_INSTALL']['DIR']['root'] . '/' . $folder ) ? _CHECKUP_YES : _CHECKUP_NO
		);
	}
	return $folderPermissions;
}


/**
 * Deletes a File.
 */
function deleteFile($filename)
{
    if (file_exists($filename))
    {
        if(!unlink($filename)) 
        {
            displayError( "Could not delete File: " . $filename );
            return FALSE;
        }
        return TRUE;
    }
    displayError( "File to delete does not exist: " . $filename );
    return FALSE;
}

/**
 * Returns all available Installation Language Locales.
 */
function getAvailableInstallationLanguages()
{
    $files = IOHelper::getFilesFromDirectory($GLOBALS['_INSTALL']['DIR']['languages'],'php',false);
    for ($i=0; $i < count($files); $i++) {
        $files[$i] = stripFileExtension($files[$i]);
    }
    return $files;
}

/**
 * Always use this function to create a Link into any Menu!
 */
function createInstallLink($menu, $params = '') 
{
    $link = 'index.php?menu='.$menu;
    if ($params == '')
    	$params = array(); 
	// track the choosen language
	$params['LANGUAGE'] = _INSTALL_LANGUAGE; 
    foreach ($params AS $key => $value) {
        $link .= '&' . $key . '=' . $value;
    }

    return $link;
}

/**
 * Checks all Settings that have to be fulfilled to start a Installation.
 * If at last one fails, a Error message will be displayed and false will be returned,
 * otherwise it returns true.
 * @return boolean true if the installation can be started, otherwise false
 */
function canStartInstallation($displayCheckRightMask = true) 
{
    $result = true;

    $folderPermissions = checkFileRights();
    foreach($folderPermissions AS $folder) {
        if ($folder['state'] == _CHECKUP_NO) {
            displayError(getTranslation('cid_check_failed'));
            if($displayCheckRightMask)
                check_file_permission();
            return false;
        }
    }
    
    return true;
}

function extractVar($varname, $notfound = '')
{
    if ( isset($_POST[$varname]) ) {
        return $_POST[$varname]; 
    } else if ( isset($_GET[$varname]) ) {
        return $_GET[$varname]; 
    } else if ( isset($_COOKIE[$varname]) ) {
        return $_COOKIE[$varname]; 
    } else if ( isset($GLOBALS[$varname]) ) {
        return $GLOBALS[$varname]; 
    } else {
        return $notfound;
    }
}

function getTranslation($name, $common = '')
{
    if (isset($GLOBALS['LANG'][$name])) {
        return $GLOBALS['LANG'][$name];
    } else {
        if($common == '')
            return '???'.$name.'???';
        return $common;
    }
}

function styleDebug($message)
{
    return '<span class="debug">'.$message.'</span>';
}

function getHelpImage($msg)
{
    return '<img class="helpImage" src="web/help.gif" onmouseover="overlib(\''.$msg.'\', VAUTO, WIDTH, 250)" onMouseOut="nd();">';
}


function installTableStart($title = null) 
{
    echo '<table width="'._TABLE_WIDTH_.'" cellpadding="3" cellspacing="3" align="center" class="installTable">';
    echo '<col width="30%"/>' . "\n";
    echo '<col width="10%"/>' . "\n";
    echo '<col width="60%"/>' . "\n";
	if(!is_null($title)) {
		echo '<tr>' . "\n";
		echo '  <th colspan="3">'.$title.'</th>' . "\n";
		echo '</tr>' . "\n";
	}
}

function installTableEnd() 
{
    echo '</table>' . "\n";
    echo '<br/>' . "\n";
}

function installRowPasswordField($translateKey, $name, $value)
{
    installRow($translateKey, createPasswordField($name,$value,'',getTranslation($translateKey.'_help')));
}

function installRowTextInput($translateKey, $name, $value)
{
    installRow($translateKey, createTextInputType($name,$value,'',false,getTranslation($translateKey.'_help')));
}

function installRow($translateKey, $value) 
{
    echo '<tr>' . "\n";
    echo '  <td>' . "\n";
    echo getTranslation($translateKey) . "\n";
    echo '  </td>' . "\n";
    echo '  <td>' . "\n";
    echo getHelpImage( getTranslation($translateKey.'_help') ). "\n";
    echo '  </td>' . "\n";
    echo '  <td>' . "\n";
    echo $value . "\n";
    echo '  </td>' . "\n";
    echo '</tr>' . "\n";
}

function createNamedTextInputType($name, $value, $max, $disabled = false, $tooltip = null)
{
    $html = '<input type="text" name="'.$name.'" id="'.$name.'" maxlength="'.$max.'" size="35" value="'.addslashes($value).'"';
	if($tooltip != null)  $html .= ' tooltipText="'.$tooltip.'"';
    if ($disabled) $html .= ' readonly ';
    return $html . '>';
}

function createTextInputType($name, $value, $max, $disabled = false, $tooltip = null)
{
    return createNamedTextInputType('data['.$name.']', $value, $max, $disabled, $tooltip);
}

function createPasswordField($name, $value, $max, $tooltip = null)
{
    return createNamedPasswordField('data['.$name.']', $value, $max, $tooltip);
}

function createNamedPasswordField($name, $value, $max, $tooltip = null)
{
    $html = '<input type="password" name="'.$name.'" id="'.$name.'" maxlength="'.$max.'" size="35" value="'.$value.'"';
	if($tooltip != null) $html .= ' tooltipText="'.$tooltip.'"';
	return $html . '>';
}

function createNamedRadioButton($name, $value, $selected)
{
    $html  = '<input type="radio" name="'.$name.'" value="'.$value.'"';
    if ($selected) {
        $html .= ' checked ';
    }
    $html .= '>';
    return $html;
}

function createRadioButton($name, $value, $selected)
{
    return createNamedRadioButton('data['.$name.']', $value, $selected);
}

function createNamedCheckBox($name, $value, $checked, $disabled = '') {
    $html  = '<input type="checkbox" name="'.$name.'" ';
    $html .= ' value="'.$value.'"';
    if ($checked) { $html .= ' checked '; }
    if ($disabled) { $html .= ' disabled '; }
    $html .= '>';
    return $html;
}

function createCheckBox($name, $value, $checked, $disabled = '')
{
    return createNamedCheckBox('data['.$name.']', $value, $checked);
}

function createTextArea($name, $value, $rows = '10', $cols = '50', $wrap = '') 
{
    $html  = '<textarea name="data['.$name.']" id="'.$name.'" rows="'.$rows.'" cols="'.$cols.'" wrap="'.$wrap.'">';
    $html .= $value;
    $html .= '</textarea>';
    return $html;
}

/**
* Creates an File Input Type with the given name.
*/
function createFileInput($name) {
    return '<input type="file" name="'.$name.'" id="'.$name.'">';
}

function createNamedSelectBox($name, $opt_name_val, $sel = '', $onChange = '', $disabled = false, $id = '', $tooltip) 
{
    $select = '<select name="'.$name.'"';
    if ($id != '') {
        $select .= ' id="'.$id.'"';
    }
    if ($onChange != '') {
        $select .= ' onChange="'.$onChange.'"';
    }
    if ($disabled) {
        $select .= ' disabled';
    }
    $select .= '>';
    foreach ($opt_name_val AS $key => $val) {
        $select .= '<option value="'.$val.'"';
        if ($sel != '' && $sel == $val) {
            $select .= ' selected';
        }
        $select .= '>'.$key.'</option>';
    }
    $select .= '</select>';
    return $select;
}

function createSelectBox($name, $opt_name_val, $sel = '', $onChange = '', $disabled = false) 
{
    return createNamedSelectBox('data['.$name.']', $opt_name_val, $sel, $onChange, $disabled, $name);
}

function displayError($message) 
{ 
    echo '<h3 class="error">'.$message.'</h3>';
} 
 
function displayMessage($message) 
{ 
    echo '<h3 class="info">'.$message.'</h3>';
}

// +-----------------------------------------------------------------------------+
// |                       DATABASE INSTALLATION METHODS                         |   
// +-----------------------------------------------------------------------------+


// install all files that are required for using the rewrite engine
function installModRewrite()
{
    $error = array();
    foreach($GLOBALS['_INSTALL']['FILE']['rewrite_enabled'] AS $fileNameToCopy => $copyLocation) {
        if(file_exists($fileNameToCopy)) {
        	if(file_exists($copyLocation) && !is_writeable($copyLocation)) {
            	$error[] = 'File: ' . $copyLocation.' already exists, but is NOT writeable.';
        	} else {
	        	if(!@copyFile($fileNameToCopy, $copyLocation))
	            	$error[] = 'Could not create file: ' . $copyLocation.'. Already existing with wrong file permission?';
        	}
        } else {
            $error[] = 'Missing input file: ' . $fileNameToCopy . '. Could not copy to: ' . $copyLocation;
        }
    }
    return $error;
}

function installHtaccessFiles()
{
    $error = array();
    foreach($GLOBALS['_INSTALL']['FILE']['security_enabled'] AS $fileNameToCopy => $copyLocation) {
    	if(file_exists($fileNameToCopy)) {
        	if(file_exists($copyLocation) && !is_writeable($copyLocation)) {
            	$error[] = 'File: ' . $copyLocation.' already exists, but is NOT writeable.';
        	} else {
	    		if(!@copyFile($fileNameToCopy, $copyLocation))
	            	$error[] = 'Could not create file: ' . $copyLocation.'. Already existing with wrong file permission?';
        	}
        } else {
            $error[] = 'Missing input file: ' . $fileNameToCopy . '. Could not copy to: ' . $copyLocation;
        }
    }
    return $error;
}

function parse_sql($prefix, $sql) {
	return str_replace($GLOBALS['_INSTALL']['db']['prefix'], $prefix, $sql);
}

?>
