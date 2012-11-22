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
 */

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

/**
 * This template lets you browse your Menu Tree and select one by clicking.
 *
 * Parameter:
 * - showHidden (true) Default: false
 * - hideTree (long)   Default: ''
 * - data[id] (long)   Default: _BIGACE_TOP_LEVEL
 *
 * Pass an parameter 'showHidden' and set 'true' as value if
 * you want to show hidden items. by default hidden items will NOT be shown.
 *
 */

if($GLOBALS['_BIGACE']['SESSION']->isAnonymous())
{
    import('classes.exception.ExceptionHandler');
    import('classes.exception.NoFunctionalRightException');
    ExceptionHandler::processCoreException( new NoFunctionalRightException('Protected Area. You are not allowed to enter!', createMenuLink(_BIGACE_TOP_LEVEL)) );
    return;
}

import('classes.util.LinkHelper');
import('classes.util.links.HtmlMenuChooserLink');
import('classes.menu.MenuService');
import('classes.right.RightService');
import('classes.util.html.FormularHelper');

require_once (_BIGACE_DIR_ADMIN.'styling.php');

loadLanguageFile('administration', _ULC_);

$data = extractVar('data', array('id' => _BIGACE_TOP_LEVEL));

define('SHOW_HIDDEN_ITEMS', extractVar('showHidden', 'false'));
define('HIDE_TREE_ID', extractVar('hideTree', null));
define('START_ID', $data['id']);

$lang = new Language($GLOBALS['_BIGACE']['SESSION']->getLanguageID());

function createProjectLink($id)
{
    $link = new HtmlMenuChooserLink();
    $link->setStartID($id);
    if (HIDE_TREE_ID != null) {
        $link->setHiddenID(HIDE_TREE_ID);
    }
    if(SHOW_HIDDEN_ITEMS == 'true') {
        $link->setShowHidden('true');
    }
    return LinkHelper::getUrlFromCMSLink($link);
}
?>

    <!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <html>
    <head>
    <title>BIGACE - <?php echo getTranslation('choose_menu','Choose Menu'); ?></title>
    <meta name="generator" content="BIGACE">
    <meta name="robots" content="noindex,nofollow">
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $lang->getCharset(); ?>">


    <link rel="stylesheet" href="<?php echo $GLOBALS['_BIGACE']['style']['class']->getCSS(); ?>" type="text/css">
    <script language="JavaScript">
    <!--
    window.focus();

    function setMyMenu(id, language, name) {
        opener.setMenu(id, language, name);
        window.close();
    }
    // -->
    </script>
    </head>
    <body>

<?php
    $MENU_SERVICE = new MenuService();
    $RIGHT_SERVICE = new RightService();

    $menu = $GLOBALS['MENU_SERVICE']->getClass(START_ID, ITEM_LOAD_LIGHT, $lang->getID());
    $menu_right = $GLOBALS['RIGHT_SERVICE']->getMenuRight( $GLOBALS['_BIGACE']['SESSION']->getUserID(), $menu->getId() );

    $parent = $menu->getParent();

    $menuStruc = array();
    if ($menu_right->canRead()) {
        $temp_name = '';
        if (($menu->getID() != _BIGACE_TOP_LEVEL)) {
            $temp_name .= '<a href="'.createProjectLink($parent->getID()).'" title="'.getTranslation('back_to').' '.$parent->getName().'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'folder_out.png" border="0" alt="'.getTranslation('back_to').' '.$parent->getName().'"></a>&nbsp;&nbsp;';
        }
        $temp_name .= '<b>' . $menu->getName() . '</b>';
        $menuStruc[$temp_name] = '<a class="textLink" href="javascript:setMyMenu(\''.$menu->getID().'\',\''.$menu->getLanguageID().'\',\''.$menu->getName().'\')" onclick="setMyMenu(\''.$menu->getID().'\',\''.$menu->getLanguageID().'\',\''.$menu->getName().'\')">'.getTranslation('choose', 'Choose').'</a>';
    }
    $menuStruc['empty'] = '&nbsp;';

	$req = new ItemRequest($GLOBALS['MENU_SERVICE']->getItemType(), $menu->getID());
	$req->setTreetype(ITEM_LOAD_LIGHT);
	$req->setOrderBy(ORDER_COLUMN_POSITION);
	$req->setOrder($req->_ORDER_ASC);
    // display hidden navi entrys by default
    if(SHOW_HIDDEN_ITEMS == 'true') {
	   $req->setFlagToExclude($req->FLAG_ALL_EXCEPT_TRASH);
    }
	$enum = new SimpleItemTreeWalker($req);


	$entries = $enum->count();
	for ($i=0; $i < $entries; $i++)
	{
		$c_menu = $enum->next();
		$show = true;
        if (HIDE_TREE_ID != null) {
            if ($c_menu->getId() == HIDE_TREE_ID) {
                $show = false;
            } else {
                $show = !$GLOBALS['MENU_SERVICE']->isChildOf(HIDE_TREE_ID, $c_menu->getId());
            }
        }
        if ($show) {
            $temp_name = '';
            if ($c_menu->hasChildren()) {
                $temp_name .= '<a href="'.createProjectLink($c_menu->getID()).'" title="'.$c_menu->getName().'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'folder_in.png" border="0" alt="'.getTranslation('cd').' '.$c_menu->getName().'"></a>&nbsp;&nbsp;';
            }
            $temp_name .= $c_menu->getName();
            $menuStruc[$temp_name] = '<a class="textLink" href="javascript:setMyMenu(\''.$c_menu->getID().'\',\''.$c_menu->getLanguageID().'\',\''.$c_menu->getName().'\')" onclick="setMyMenu(\''.$c_menu->getID().'\',\''.$c_menu->getLanguageID().'\',\''.$c_menu->getName().'\')">'.getTranslation('choose', 'Choose').'</a>';
        }
    }

    $menuStruc['&nbsp;'] = 'empty';

    /* Create Menu structure   */
	$config = array(
				'width'		    => '350',
				'align'		    => array (
				                        'table'     =>  'center',
				                        'left'      =>  'left',
				                        'title'     =>  'center'
				                   ),
				'title'			=> getTranslation('choose_menu','Choose Menu'),
				'entries'		=> $menuStruc,
				'form_action'   => '',
				'form_submit'   => false,
				'form_reset'    => 'window.close()',
				'reset_label'   => 'Schliessen' // TRANSLATE

	);
	echo '<br>';
	echo createTable($config);
	echo '<br>&nbsp;';

    echo '</body>';
    echo '</html>';

    include_once(_BIGACE_DIR_LIBS.'footer.inc.php');

?>