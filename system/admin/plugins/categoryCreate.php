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
 *  Create categorys with this script!
 *
 *  '1' (default)     => Display create formular
 *  'saveNewCategory' => Create Category
 */

check_admin_login();

// load translations, some of them are used in the header already
// do not move this line below the admin_header()
loadLanguageFile('categoryAdmin', ADMIN_LANGUAGE);

admin_header();

import('classes.category.Category');
import('classes.category.ItemCategoryEnumeration');
import('classes.category.CategoryItemEnumeration');
import('classes.category.CategoryTreeWalker');
import('classes.category.CategoryAdminService');
import('classes.category.CategoryService');
import('classes.util.formular.CategorySelect');
import('classes.util.html.Option');
import('classes.util.html.FormularHelper');

include_once(_ADMIN_INCLUDE_DIRECTORY . 'answer_box.php');
require_once(_BIGACE_DIR_LIBS . 'sanitize.inc.php');

$CAT_SERVICE = new CategoryService();

$data = extractVar('data', array('id' => _BIGACE_TOP_LEVEL));
$MODE = extractVar('mode', '1');

if ($MODE == 'saveNewCategory')
{
    if ( !isset($data['name']) || (isset($data['name']) && $data['name'] == '') ) {
		displayError( getTranslation('category_name_not_empty') );
        $MODE = '1';
	}
	if (!isset($data['parent']) || (isset($data['parent']) && $data['parent'] == '')) {
		displayError( getTranslation('category_missing_values') );
        $MODE = '1';
	}
    
	if($MODE != '1')
    {
        if (!isset($data['description'])) {
        	$data['description'] = '';
        }
        
        $desc = sanitize_plain_text($data['description']);
        $name = sanitize_plain_text($data['name']);
		
        $ADMIN = new CategoryAdminService();
				
		$new_cat = array(
				'parentid'      => intval($data['parent']),
				'description'   => $desc,
				'name'          => $name
		);
		$res = $ADMIN->createCategory($new_cat);
		$GLOBALS['LOGGER']->logDebug('Creating Category with Result ID: ' . $res);
		
		$hidden = array(
						'mode'		   => '2',
						'data[id]'     => $res
		);
		$msg = array(
						getTranslation('name')  => $name,
						'ID'                    => $res
		);
		displayAnswer(getTranslation('category_created'), $msg, createAdminLink( _ADMIN_ID_CATEGORY_ADMIN ), $hidden, getTranslation('admin'), $icon = 'category_new.png');
		unset ($msg);
		unset ($hidden);
    }
}


if ($MODE == '1')
{
    if (!isset($data['parent'])) {
        $data['parent'] = _BIGACE_TOP_LEVEL;
    }

    echo createBackLink(_ADMIN_ID_CATEGORY_ADMIN, array('data[id]' => $data['parent']));
        
    if (!isset($data['description'])) {
        $data['description'] = '';
    }

    if (!isset($data['name'])) {
        $data['name'] = '';
    }

    $name = sanitize_plain_text($data['name']);
    $desc = sanitize_plain_text($data['description']);
    
    $topLevelCat = $GLOBALS['CAT_SERVICE']->getCategory(_BIGACE_TOP_LEVEL);

    $s = new CategorySelect();
    $s->setPreSelectedID($data['parent']);
    $s->setName('data[parent]');
    $e = new Option();
    $e->setText( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' );
    $e->setValue($topLevelCat->getID());
    $s->addOption($e);
    $s->setStartID(_BIGACE_TOP_LEVEL);

	  $config = array(
				'width'		    	=>	ADMIN_MASK_WIDTH_SMALL,
				'image'				=>  $GLOBALS['_BIGACE']['style']['DIR'].'category_new.png',
				'title'			  		=> 	getTranslation('category_create_new'),
				'form_action'	=>	createAdminLink($GLOBALS['MENU']->getID()),
				'form_method'	=>	'post',
				'form_hidden'	=>	array( 'mode' => 'saveNewCategory',
										   'uid'  => $GLOBALS['_BIGACE']['SESSION']->getUserID() ),
				'entries'		  	=>  array(
												getTranslation('name') 				=> createTextInputType('name', $name, ''),
												getTranslation('category_child_of') => $s->getHtml(),
												getTranslation('description') 		=> createTextArea('description', $desc, '10','50'),
												),
				'form_submit'	=>	TRUE,
				'submit_label'	=>	getTranslation('save')
  	);
  	echo createTable($config);

}

admin_footer();

