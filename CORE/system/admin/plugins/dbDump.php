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

/**
 * Script for creating Database dumps for a special Community.
 */

check_admin_login();
admin_header();

define('_PARAM_DUMP_MODE',          'dumpMode');
define('_MODE_START',          'dumpMenu');
define('_MODE_CREATE',         'dumpCreate');
define('_MODE_IMPORTABLE',     'makeImportable');
define('_WITH_STATISTICS',     'includeStatistics');

define('_MODE_COMMENT',        'showComments');
define('_MODE_DROP_TABLE',     'showDropTable');
define('_MODE_CREATE_TABLE',   'showCreateTable');

define('_MODE_DISPLAY_TYPE',   'displayType');

define('_MODE_TEXTAREA',       'showInTextArea');
define('_MODE_SAVE_FILE',      'saveAsFile');
define('_MODE_SHOW_SQL',       'showSql');

define('_BIGACE_EXPORT_DIRECTORY', $GLOBALS['_BIGACE']['DIR']['consumer'].'export/');

import('classes.util.html.FormularHelper');
import('classes.util.IOHelper');
import('classes.sql.mysqldump');
import('classes.fright.FrightExporter');

$dumpMode = extractVar(_PARAM_DUMP_MODE, _MODE_START);

if ($dumpMode == _MODE_CREATE) 
{
    $data = extractVar('data', array(_MODE_COMMENT => false, _MODE_DROP_TABLE => false, _MODE_CREATE_TABLE => false));
    
    $showComment        = (isset($data[_MODE_COMMENT]))       ? true  : false;
    $showCreateTable    = (isset($data[_MODE_CREATE_TABLE]))  ? true  : false;
    $showDropTable      = (isset($data[_MODE_DROP_TABLE]))    ? true  : false;
    $showImportable     = (isset($data[_MODE_IMPORTABLE]))    ? true  : false;
    $includeStats       = (isset($data[_WITH_STATISTICS]))    ? true  : false;

    $displayMode		= (isset($data[_MODE_DISPLAY_TYPE]))  ? $data[_MODE_DISPLAY_TYPE]  : _MODE_SHOW_SQL;

    $dump = new mysqldump($GLOBALS['_BIGACE']['db']['host'],$GLOBALS['_BIGACE']['db']['user'],$GLOBALS['_BIGACE']['db']['pass']);
    $dump->setTablePreString($GLOBALS['_BIGACE']['db']['prefix']);
    $dump->setShowCreateTable($showCreateTable);
    $dump->setShowDropTable($showDropTable);
    $dump->setShowComments($showComment);
      
    if ($showImportable) {
       $dump->setUseReplacer(true);
       $dump->addReplacer("cid","{CID}");
    }
    
    $excludeTables = array('session');
    if(!$includeStats) {
        array_push($excludeTables, 'statistics');
        array_push($excludeTables, 'statistics_history');
    }

    if(!$dump->backup($GLOBALS['_BIGACE']['db']['name'], $excludeTables))
        echo '<br><b>Problems connecting to Database!</b><br>';
    
    echo createBackLink($GLOBALS['MENU']->getID()) . '<br/>';
    
    if ($displayMode == _MODE_TEXTAREA) {
        echo '<textarea rows="20" cols="100" style="width:90%;height:75%">';
        echo $dump->getDump();
        echo '</textarea>';
    } else if ($displayMode == _MODE_SAVE_FILE) {

        IOHelper::createDirectory(_BIGACE_EXPORT_DIRECTORY);

    	$filename = 'export_' . time() . '.sql';
    	$fullfile = _BIGACE_EXPORT_DIRECTORY . $filename;
        $fpointer = fopen($fullfile, "wb");
        fputs($fpointer, $dump->getDump());
        fclose($fpointer);
        
        echo getTranslation('dump_savedfile') . '<br><b><a href="http://'.$_SERVER['SERVER_NAME'].'/consumer/cid'._CID_.'/export/'.$filename.'">' . $filename . '</a></b>';
    } else {
        echo '<pre>';
        echo $dump->getDump();
        echo '</pre>';
	}
   
}
else
{
    $entries = array( getTranslation('dump_create_table') => createCheckBox(_MODE_CREATE_TABLE, false, false, false),
                      getTranslation('dump_drop_table')   => createCheckBox(_MODE_DROP_TABLE, false, false, false),
                      getTranslation('dump_comment')      => createCheckBox(_MODE_COMMENT, true, true, false),
                      getTranslation('dump_importable')   => createCheckBox(_MODE_IMPORTABLE, false, false, false),
                      getTranslation('dump_statistics')   => createCheckBox(_WITH_STATISTICS, false, false, false),
                      'empty' 							  => '',
                      getTranslation('dump_showsql')      => createRadioButton(_MODE_DISPLAY_TYPE, _MODE_SHOW_SQL, true),
                      getTranslation('dump_textarea')     => createRadioButton(_MODE_DISPLAY_TYPE, _MODE_TEXTAREA, false),
                      getTranslation('dump_saveasfile')   => createRadioButton(_MODE_DISPLAY_TYPE, _MODE_SAVE_FILE, false)
                     ) ;


    echo '<div id="dbDump">';
    $config = array(
					'width'		    =>	ADMIN_MASK_WIDTH_SMALL,
					'align'		    =>	array (
					                        'table'     =>  'left',
					                        'left'      =>  'left'
					                    ),
					'title'			=> 	getTranslation('dump_title'),
					'form_action'	=>	createAdminLink($GLOBALS['MENU']->getID()),
					'form_method'	=>	'post',
					'form_hidden'	=>	array(
					                        'data[]'            => '',
											_PARAM_DUMP_MODE			=>	_MODE_CREATE
									),
					'entries'		=> 	$entries,
					'form_submit'	=>	true,
					'submit_label'	=>	getTranslation('create')
	);
	echo createTable($config);
    echo '</div>';
}

admin_footer();

?>
