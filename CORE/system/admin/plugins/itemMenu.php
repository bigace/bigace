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
 * Plugin used to display the classical Menu administration.
 */

check_admin_login();
admin_header();

import('classes.menu.Menu');
import('classes.menu.MenuAdminService');
import('classes.administration.MenuAdminMask');
import('classes.util.LinkHelper');
import('classes.util.CMSLink');
import('classes.util.html.JavascriptHelper');
import('classes.util.links.EditorLink');


define('_HAS_FRIGHT_CREATE_PAGE', $GLOBALS['FRIGHT_SERVICE']->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), _BIGACE_FRIGHT_ADMIN_MENUS));
// TODO remove fright
//define('_HAS_FRIGHT_SOURCE_CODE', $GLOBALS['FRIGHT_SERVICE']->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'editor.html.sourcecode'));

$_ITEMTYPE     = _BIGACE_ITEM_MENU;
$_ADMIN        = new MenuAdminService();

// include item logic
include_once(_ADMIN_INCLUDE_DIRECTORY.'item_main.php' );

// execute whatever was done by the user
propagateAdminMode();

// show the footer
admin_footer();

function createAdminMask($itemtype) {
    return new MenuAdminMask();
}

function createEditDataMaskForm($data, $item)
{
    $mask = new MenuAdminMask();
    $mask->editItem($item->getID(), $item->getLanguageID());
}

function createEditorLink($id, $langid, $showTitle = false, $delim = '&nbsp;', $quotes = '\'')
{
    $html  = '<a href="javascript:useEditor('.$quotes.$id.$quotes.','.$quotes.$langid.$quotes.')" title="'.getTranslation('editor').'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'menu_fckeditor.png" alt="'.getTranslation('editor').'" title="'.getTranslation('editor').'">';
    if ($showTitle) {
        $html .= ' ' . getTranslation('editor');
    }
    $html .= '</a>';
    return $html; 
}

function getEditorJS() 
{
    $editorLink = new EditorLink();
    $editorLink->setItemID('"+val0+"');
    $editorLink->setLanguageID('"+val1+"');

    return JavascriptHelper::createJSPopup('useEditor', 'Editor', '(screen.width)', '(screen.height)', LinkHelper::getUrlFromCMSLink($editorLink),array('id','langid'),'no','yes',false) . "\n";
}

/**
 * Show menu specific File listing.
 */
function createFileListing($data)
{
    if(!isset($data['id']))
        return null; //TODO throw exception
    $item = $GLOBALS['_SERVICE']->getItem($data['id']);
    $cssClass = "row2";
    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("AdminMenuListing.tpl.htm", false, true);

    // -------------------------------- FORMULAR VALUES --------------------------------
    $tpl->setCurrentBlock("formValues");
    $tpl->setVariable("ALL_SELECTED_ITEM_URL", createAdminLink($GLOBALS['MENU']->getID()));
    $tpl->setVariable("DATA_ID", $item->getID());
    $tpl->setVariable("JAVASCRIPT", getEditorJS());
    $tpl->parseCurrentBlock("formValues");

    // -------------------------------- TOP-LEVEL AND WAY-HOME --------------------------------
    // Display current Top-Level and way home
    $allLevel = $GLOBALS['_SERVICE']->getWayHome($item->getId(), true);
    for($i=count($allLevel)-1; $i >= 0; $i--)
    {
		$cssClass = ($cssClass == "row1") ? "row2" : "row1";
        $parent = $GLOBALS['_SERVICE']->getItem($allLevel[$i]);
        $back = '';
        
        if ($i > 0 && $item->getID() != _BIGACE_TOP_LEVEL) {
            $back .= '<a href="'.createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => $parent->getID(), 'mode' => _MODE_BROWSE_MENU)).'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'folder_out.png" alt="'.getTranslation('back_to', 'Back to').' '.$parent->getName().'" border="0"></a>&nbsp;';
        }
        if ($parent->isHidden())
            $back .= '<i>';
        $back .= $parent->getName();
        if ($parent->isHidden())
            $back .= '</i>';
        $tools = getToolLinksForItem($parent);

        // check if user may write, so we can display the editor link
        $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $item->getID(), $GLOBALS['_BIGACE']['SESSION']->getUserID());
        if ($temp_right->canWrite()) {
            $tools['editor'] = createEditorLink($parent->getID(), $parent->getLanguageID());
        }
        
        $tpl->setCurrentBlock("wayhome");
        $tpl->setVariable("CSS_CLASS", $cssClass);
        $tpl->setVariable("TOOL_HISTORY", (isset($tools['history']) ? $tools['history'] : ''));
        $tpl->setVariable("BACK_LINK", $back);
        $tpl->setVariable("TOOL_ADMIN", (isset($tools['admin']) ? $tools['admin'] : ''));
        $tpl->setVariable("TOOL_EDITOR", (isset($tools['editor']) ? $tools['editor'] : ''));
        $tpl->setVariable("TOOL_PREVIEW", (isset($tools['preview']) ? $tools['preview'] : ''));
        $tpl->setVariable("TOOL_RIGHTS", (isset($tools['rights']) ? $tools['rights'] : ''));
        $tpl->setVariable("TOOL_DELETE", (isset($tools['delete']) ? $tools['delete'] : ''));
        $tpl->parseCurrentBlock("wayhome");
    }

    // -------------------------------- SPACER ROW --------------------------------
	$cssClass = ($cssClass == "row1") ? "row2" : "row1";

    $tpl->setCurrentBlock("spacer");
    $tpl->setVariable("CSS_CLASS", $cssClass);
    $tpl->parseCurrentBlock("spacer");

    // -------------------------------- CURRENT ITEMS CHILDREN --------------------------------

    //$_INFO = $GLOBALS['_SERVICE']->getTree($item->getID());
    //$_INFO = $GLOBALS['_SERVICE']->getTreeForLanguage($item->getID(), $languageID);
    
	$req = new ItemRequest($GLOBALS['_SERVICE']->getItemType(), $item->getID());
    //$req->setLanguageID($languageID);
	$req->setTreetype(ITEM_LOAD_LIGHT);
	$req->setOrderBy(ORDER_COLUMN_POSITION);
	$req->setOrder($req->_ORDER_ASC);
	$req->setFlagToExclude($req->FLAG_ALL_EXCEPT_TRASH);
	$_INFO = new SimpleItemTreeWalker($req);
	
    $a = $_INFO->count();

    for ($i=0; $i < $a; $i++)
    {
        $item = $_INFO->next();
        $tools = getToolLinksForItem($item);
        
        // check we user may write, so we can display the editor link
        $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($GLOBALS['_ITEMTYPE'], $item->getID(), $GLOBALS['_BIGACE']['SESSION']->getUserID());
        if ($temp_right->canWrite()) {
            $tools['editor'] = createEditorLink($item->getID(), $item->getLanguageID());
        }

        $name = '';
        if (!$GLOBALS['_SERVICE']->isLeaf($item->getId()) > 0) {
            $name = '<a href="'.createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => $item->getID(), 'mode' => _MODE_BROWSE_MENU)).'" title="'.getTranslation('cd').' '.$name.'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'folder_in.png" border="0" alt="'.getTranslation('cd').' '.$name.'"></a>&nbsp;';
        }
        if($item->isHidden())
            $name .= '<i>';
        $name .= $tools['name'];
        if($item->isHidden())
            $name .= '</i>';

        $tpl->setCurrentBlock("children");
        $tpl->setVariable("CSS_CLASS", $cssClass);
        $tpl->setVariable("TOOL_HISTORY", (isset($tools['history']) ? $tools['history'] : ''));
        $tpl->setVariable("ITEM_NAME", $name);
        $tpl->setVariable("TOOL_ADMIN", (isset($tools['admin']) ? $tools['admin'] : ''));
        $tpl->setVariable("TOOL_EDITOR", (isset($tools['editor']) ? $tools['editor'] : ''));
        $tpl->setVariable("TOOL_PREVIEW", (isset($tools['preview']) ? $tools['preview'] : ''));
        $tpl->setVariable("TOOL_RIGHTS", (isset($tools['rights']) ? $tools['rights'] : ''));
        $tpl->setVariable("TOOL_DELETE", (isset($tools['delete']) ? $tools['delete'] : ''));
        $tpl->setVariable("TOOL_UP", ( $i > 0 && isset($tools['up']) ? $tools['up'] : ''));
        $tpl->setVariable("TOOL_DOWN", ( $i+1 < $a && isset($tools['down']) ? $tools['down'] : ''));
        $tpl->parseCurrentBlock("children");

		$cssClass = ($cssClass == "row1") ? "row2" : "row1";
    }

    // -------------------------------- FOOTER --------------------------------
	$cssClass = ($cssClass == "row1") ? "row2" : "row1";
    $modeList = array(
        ''                              => '',
        getTranslation('updatemode_3')  => _MODE_DELETE_HISTORY_MENU,
        getTranslation('update_multiple') => _MODE_UPDATE_MULTIPLE
    );
    
    $tpl->setCurrentBlock("footer");
    $tpl->setVariable("CSS_CLASS", $cssClass);
    if(_HAS_FRIGHT_CREATE_PAGE)
        $tpl->setVariable("CREATE_MENU_LINK", '<a href="'.createAdminLink(_ADMIN_ID_MENU_CREATE, array('data[id]' => $data['id'], 'data[nextAdmin]' => 'itemMenu')).'" class="textLink"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'item_1_new.png" border="0"> '.getTranslation('new_page').'</a>');
    else
        $tpl->setVariable("CREATE_MENU_LINK", '');
    
    $tpl->setVariable("SELECT_MODE", createNamedSelectBox('mode', $modeList));
    $tpl->parseCurrentBlock("footer");

    $tpl->show();
}

function createPlainEditorLink($id, $langid)
{
    $lang = new Language($langid);
    return createAdminLink($GLOBALS['MENU']->getID(), array('enhancedMode' => 'edit', 'enhancedID' => $id, 'langid' => $langid, 'adminCharset' => $lang->getCharset()));
}
