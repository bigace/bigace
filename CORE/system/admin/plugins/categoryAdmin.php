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
 *  Administrate all your Categorys with this script!
 *
 *  mode 1 = Browse categorys
 *  mode 2 = Edit values for existing Category
 *  mode 5 = Save existing Category
 *  mode 6 = Show Links for category
 *  mode 7 = Delete ItemCategory Link
 *  mode 8 = Delete Category
 */

check_admin_login();
admin_header();

import('classes.util.html.FormularHelper');
import('classes.category.Category');
import('classes.category.ItemCategoryEnumeration');
import('classes.category.CategoryItemEnumeration');
import('classes.category.CategoryTreeWalker');
import('classes.category.CategoryAdminService');
import('classes.category.CategoryService');
import('classes.util.formular.CategorySelect');
import('classes.util.html.Option');
require_once(_BIGACE_DIR_LIBS.'sanitize.inc.php');

$CAT_SERVICE = new CategoryService();

$DATA = extractVar('data', array('id' => _BIGACE_TOP_LEVEL));
$MODE = extractVar('mode', '1');

switch ($MODE) {
    case '1':
        browseMenu($DATA['id']);
        break;
    case '2':
        editCategory($DATA);
        break;
    case '5':
        if(check_csrf_token()) {
            saveCategory($DATA);
        } else {
            displayError( getTranslation('category_missing_values') );
        }
        break;
    case '6':
        if(isset($DATA['id']) && check_csrf_token()) {
            $id = intval($DATA['id']);
        	showLinkedItems($id);
        } else {
            displayError( getTranslation('category_missing_values') );
        }
        break;
    case '7':
        if(isset($DATA['itemtype']) && isset($DATA['itemid']) && 
            isset($DATA['id']) && check_csrf_token()) {
        	$id = intval($DATA['id']);
            $itemid = intval($DATA['itemid']);
            $itemtype = intval($DATA['itemtype']);
            
            deleteLink($itemtype, $itemid, $id);
        } else {
            displayError( getTranslation('category_missing_values') );
        }
        break;
    case '8':
        break;
    default:
        browseMenu($DATA['id']);
}

if ($MODE == 8)
{
	if (!isset($_POST['catid']) && check_csrf_token()) {
        displayError( getTranslation('category_missing_values') );
	} else {
	    if($_POST['catid'] != _BIGACE_TOP_LEVEL)
	    {
            $id = intval($_POST['catid']);
	    	
	        $CAT = $CAT_SERVICE->getCategory($id);
	        $PARENT = $CAT->getParentID();
	        if (!$CAT->hasChilds() && $CAT->getID() != _BIGACE_TOP_LEVEL)
	        {
	            $ADMIN = new CategoryAdminService();
	            $ADMIN->deleteCategory($id);
	            unset ($ADMIN);
	            browseMenu($PARENT);
	        }
	        unset ($CAT);
	    }
	    else
	    {
	        displayError( getTranslation('category_missing_values') );
	    }
	}
}

// ---------------------------------------------------------
//     FUNCTIONS FOLLOW
// ---------------------------------------------------------


function deleteLink($itemtype, $itemid, $categoryID)
{
	$CAT_SERVICE = new CategoryService();
	
    $ADMIN = new CategoryAdminService();
    $ADMIN->deleteCategoryLink($itemtype, $itemid, $categoryID);
    unset ($ADMIN);
    if ($CAT_SERVICE->countLinksForCategory($categoryID) > 0) {
        showLinkedItems($categoryID);
    }
    else {
        $category = $CAT_SERVICE->getCategory($categoryID);
        if($category->getID() != _BIGACE_TOP_LEVEL && !$category->hasChilds())
            browseMenu($category->getParentID());
        else
            browseMenu($category->getID());
    }
}

function showLinkedItems($categoryID)
{
    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("CategorizedItems.tpl.htm", true, true);

    $CAT_SERVICE = new CategoryService();
    
    $currentCategory = $CAT_SERVICE->getCategory($categoryID);
    $links = $CAT_SERVICE->getAllItemsForCategory($categoryID);

    $tpl->setVariable("CATEGORY_NAME", $currentCategory->getName());

    echo createBackLink( $GLOBALS['MENU']->getID() );

    $cssClass = "row1";

    for ($i=0; $i < $links->count(); $i++)
    {
        $temp = $links->next();
        $currentItem = new Item($temp["itemtype"],$temp["itemid"]);

        $tpl->setCurrentBlock("row") ;
        $tpl->setVariable("CSS", $cssClass) ;
        $tpl->setVariable("ITEM_NAME", $currentItem->getName());
        $tpl->setVariable("ITEM_TYPE_NAME", getTranslation('item_'.$temp["itemtype"]));
        $tpl->setVariable("ITEM_TYPE_ID", $temp["itemtype"]);
        $tpl->setVariable("ITEM_ID", $temp["itemid"]);
        $tpl->setVariable('CSRF_TOKEN', get_csrf_token());        
        $tpl->setVariable("DELETE_LINK", createAdminLink($GLOBALS['MENU']->getID(), get_csrf_token(array('mode' => '7', 'data[itemtype]' => $temp["itemtype"], 'data[itemid]' => $temp["itemid"], 'data[id]' => $categoryID))));
        $tpl->parseCurrentBlock("row") ;

	    $cssClass = ($cssClass == "row1") ? "row2" : "row1";
    }
    $tpl->show();

    unset ($links);
	unset ($currentCategory);
}


function browseMenu($categoryID)
{
    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("AdminCategoryMenu.tpl.htm", true, true);
    $CAT_SERVICE = new CategoryService();
    
    $entrys = array();
    $category = $CAT_SERVICE->getCategory($categoryID);

    $name = getTranslation('category_create_new');
    
    echo '<form action="'.createAdminLink(_ADMIN_ID_CATEGORY_CREATE, array('data[parent]'=>$category->getID())).'" method="post" class="actionForm">
            <button type="submit">
        		<img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'category_new.png" border="0" style="margin-right:5px" alt="'.$name.'">' . $name . '
		    </button>
		  </form>';

    if($category->getID() != _BIGACE_TOP_LEVEL) 
    {
        $wayhome = '';
        $tempCat = $category;
         
        do {
            $temp = ' <a href="'.createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => $tempCat->getID())).'">'.$tempCat->getName().'</a>';
            if($wayhome != '')
                $temp .= ' &raquo; ';
            $wayhome = $temp . $wayhome;
            $tempCat = $CAT_SERVICE->getCategory($tempCat->getParentID());
        } while($tempCat->getParentID() != _BIGACE_TOP_PARENT);

        // top level item - not translated so show an icon instead of its name
        $wayhome = ' <a href="'.createAdminLink($GLOBALS['MENU']->getID()).'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'folder_in.png" border="0" style="vertical-align:middle;" alt="'.$tempCat->getName().'"></a> &raquo; ' . $wayhome;
        //$wayhome = ' <a href="'.createAdminLink($GLOBALS['MENU']->getID()).'">'.$tempCat->getName().'</a> &raquo; ' . $wayhome;
        
        echo '<div style="margin-bottom:6px;">' . getTranslation('you_are_here') . ':' . $wayhome . '</div>';
    }

	$cssClass = "row1";

    // Current Category
    if ($category->getID() != _BIGACE_TOP_LEVEL) 
    {
    	$tools = getCategoryMenuTools($category->getID());
    	$name = $tools['EDIT'];
    	$parent = $category->getParent();
      	$tree = '<a href="'.createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => $parent->getID(), 'mode' => '1')).'" title="'.getTranslation('back_to').' '.$parent->getName().'">'
      		  . '<img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'folder_out.png" border="0" alt="'.getTranslation('back_to').' '.$parent->getName().'"></a>';

	    $tpl->setCurrentBlock("row");
	    $tpl->setVariable("CSS", $cssClass);
	    $tpl->setVariable("ITEM_ID", $category->getID());
	    $tpl->setVariable("ITEM_TREE", $tree);
	    $tpl->setVariable("ITEM_NAME", $name);
	    $tpl->setVariable("ACTION_EDIT", $tools['EDIT']);
	    $tpl->setVariable("ACTION_LINKED", $tools['LINKS']);
	    $tpl->setVariable("ACTION_DELETE", $tools['DELETE']);
	    $tpl->setVariable("AMOUNT", '('.$tools['AMOUNT'].')');
	    $tpl->parseCurrentBlock("row");
	}

    $enum = $category->getChilds();
    $val = $enum->count();
	
	if($val == 0 && $category->getID() == _BIGACE_TOP_LEVEL) {
		echo '<p><b>' . getTranslation('category_none_existing') . '</b></p>';
		return;
	}

	for ($i = 0; $i < $val; $i++)
    {
        $temp = $enum->next();
        $tools = getCategoryMenuTools($temp->getID());
        $name = $tools['EDIT'];
        
        if ($temp->hasChilds()) {
            $tree = '<a href="'.createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => $temp->getID(), 'mode' => '1')).'" title="'.$temp->getName().'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'folder_in.png" border="0" alt="'.getTranslation('cd').' '.$temp->getName().'"></a>';
        } else {
            if($i < $val-1)
                $tree = '<img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'/menutree/T.png" width="16" border="0" >';
            else
                $tree = '<img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'/menutree/L.png" width="16" border="0" >';
        }
		
        $tpl->setCurrentBlock("row");
        $tpl->setVariable("CSS", $cssClass);
        $tpl->setVariable("ITEM_ID", $temp->getID());
        $tpl->setVariable("ITEM_TREE", $tree);
        $tpl->setVariable("ITEM_NAME", $name);
        $tpl->setVariable("ACTION_EDIT", $tools['EDIT']);
        $tpl->setVariable("ACTION_LINKED", $tools['LINKS']);
        $tpl->setVariable("ACTION_DELETE", $tools['DELETE']);
        $tpl->setVariable("AMOUNT", '('.$tools['AMOUNT'].')');
        $tpl->parseCurrentBlock("row");
		
		$cssClass = ($cssClass == "row1") ? "row2" : "row1";
    }

    $tpl->show();
}


function editCategory($data)
{
    $CAT_SERVICE = new CategoryService();
	
    $entrys              = array();
    $topLevelCat         = $CAT_SERVICE->getCategory(_BIGACE_TOP_LEVEL);
    $category            = $CAT_SERVICE->getCategory($data['id']);
    $data['name']        = $category->getName();
    $data['description'] = $category->getDescription();
    $hidden              = array( 'mode'      => '5',
                                  'data[id]'  => $category->getID() );
    $s = new CategorySelect();
    $s->setHideID($category->getID());
    $s->setPreSelectedID($category->getParentID());
    $s->setName('data[parent]');

	$e = new Option();
    $e->setText('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
    $e->setValue($topLevelCat->getID());
    $s->addOption($e);
	
    $s->setStartID(_BIGACE_TOP_LEVEL);
    
    $desc = sanitize_plain_text($data['description']);
    $name = sanitize_plain_text($data['name']);
    
    $entrys[getTranslation('name')] = createTextInputType('name', $name, '');

    if ($category->getID() == _BIGACE_TOP_LEVEL) {
        $hidden['data[parent]'] = _BIGACE_TOP_PARENT;
    } else {
        $entrys[getTranslation('category_child_of')] = $s->getHtml();
    }
    
    $hidden = get_csrf_token($hidden);
        
    $entrys[getTranslation('description')] = createTextArea('description', $desc, '10','50');

    if($category->getID() != _BIGACE_TOP_LEVEL && !$category->hasChilds())
        echo createBackLink( $GLOBALS['MENU']->getID(), array('data[id]' => $category->getParentID()) ) ;
    else
        echo createBackLink( $GLOBALS['MENU']->getID(), array('data[id]' => $category->getID()) ) ;

	$config = array(
				'width'		    =>	ADMIN_MASK_WIDTH_SMALL,
				'title'			=> 	getTranslation('edit') . ' ('.getTranslation('id').': '.$category->getID().')',
				'form_action'	=>	createAdminLink($GLOBALS['MENU']->getID()),
				'form_method'	=>	'post',
				'form_hidden'	=>	$hidden,
				'entries'		=>  $entrys,
				'form_submit'	=>	TRUE,
    		    'submit_label'	=>	getTranslation('save')
	);
	echo createTable($config);
}



function saveCategory($data)
{
	$save = true;
	
    if ( !isset($data['name']) || (isset($data['name']) && $data['name'] == '') ) {
		displayError( getTranslation('category_name_not_empty') );
        $save = false;
	}
	if (!isset($data['parent']) || (isset($data['parent']) && $data['parent'] == '')) {
		displayError( getTranslation('category_missing_values') );
        $save = false;
	}
	if (!isset($data['id']) || (isset($data['id']) && $data['id'] == '')) {
		displayError( getTranslation('category_missing_values') );
        $save = false;
	}

	if ($save) 
	{
        $desc = '';
        if (isset($data['description'])) {
            $desc = $data['description'];
        }

        $desc = sanitize_plain_text($data['description']);
        $name = sanitize_plain_text($data['name']);
        $id = intval($data['id']);
        $pid = intval($data['parent']);
        
        $ADMIN = new CategoryAdminService();
        $CAT_SERVICE = new CategoryService();
        
		$pos = $ADMIN->_getMaxPositionFor($pid) + 1;
		$res = $ADMIN->changeCategory($id, $pid, $pos, $name, $desc);
		$GLOBALS['LOGGER']->logDebug('Changing Category (id: '.$id.')');
		
		$category = $CAT_SERVICE->getCategory($id);
		if($category->getID() != _BIGACE_TOP_LEVEL && !$category->hasChilds())
			browseMenu($category->getParentID());
		else
			browseMenu($category->getID());
    }
    else
    {
        editCategory($data);
    }
}



function getCategoryMenuTools($id)
{
    $tools = array('EDIT' => '', 'LINKS' => '', 'DELETE' => '', 'AMOUNT' => 0);
    $CAT_SERVICE = new CategoryService();
    
    $CAT = $CAT_SERVICE->getCategory($id);
    $links = $CAT_SERVICE->countLinksForCategory($id);

    // Create Link to edit Category
    $edit = '<a href="'.createAdminLink($GLOBALS['MENU']->getID(), get_csrf_token(array('mode' => '2', 'data[id]' => $id, 'data[mode]' => 'edit'))).'"';
    $edit .= ' title="'.getTranslation('edit').'">';
    $edit .= $CAT->getName();
    $edit .= '</a>';
    $tools['EDIT'] = $edit;

    $tools['AMOUNT'] = $links;

    if ($links > 0)
    {
      $linked = '<a href="'.createAdminLink($GLOBALS['MENU']->getID(), get_csrf_token(array('mode' => '6', 'data[id]' => $id))).'"';
      $linked .= ' title="'.getTranslation('category_show_links').': '.$links.'">';
      $linked .= '<img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'category_go.png" border="0" alt="'.getTranslation('category_show_links').': '.$links.'">';
      $linked .= '</a>';
      $tools['LINKS'] = $linked;
    }
    else if ($CAT->getID() != _BIGACE_TOP_LEVEL && !$CAT->hasChilds())
    {
      $delete = '<form action="'.createAdminLink($GLOBALS['MENU']->getID()).'" method="post" onsubmit="return confirm(\''.getTranslation('category_ask_for_delete').' '.$CAT->getName().'?\')">';
      $delete .= '<input type="hidden" name="mode" value="8" />';
      $delete .= '<input type="hidden" name="catid" value="'.$id.'" />';
      $delete .= get_csrf_token();
      $delete .= '<button type="submit" class="delete">'.getTranslation('delete').'</button>';
      $delete .= '</form>';
      $tools['DELETE'] = $delete;
    }

    return $tools;
}

admin_footer();
