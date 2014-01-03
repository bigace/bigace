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
 */

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

if($GLOBALS['_BIGACE']['SESSION']->isAnonymous())
{
    loadClass('exception', 'ExceptionHandler');
    loadClass('exception', 'NoFunctionalRightException');
    ExceptionHandler::processCoreException( new NoFunctionalRightException('Protected Area. You are not allowed to enter!', createMenuLink(_BIGACE_TOP_LEVEL)) );
    return;
}

/**
 * This template lets you browse your Menu Tree and select one by clicking.
 * It uses XML Requests for dyanmic tree loading and uses expandable tree nodes,
 * so no browser reload is required.
 * Instead Javascript support is required.
 */

import('classes.menu.MenuService');
import('classes.item.SimpleItemTreeWalker');
import('classes.util.LinkHelper');
import('classes.util.links.MenuChooserLink');
import('classes.right.RightService');

include_once(_BIGACE_DIR_ADMIN . 'styling.php');

loadLanguageFile('jstree', $GLOBALS['_BIGACE']['PARSER']->getLanguage());
$lang = new Language($GLOBALS['_BIGACE']['PARSER']->getLanguage());

define('_PUBLIC_TREE_DIR', _BIGACE_DIR_ADDON_WEB.'webfx/xloadtree/');
define('_PARAM_JSFUNC_TO_CALL', 'jsfunction');
define('_PARAM_MODE', 'mode');
define('_PARAM_TREE_ID', 'treeId');
define('_PARAM_HIDE_TREE', 'hideTree');
define('_MODE_XML', 'xml');

// pass an parameter 'showHidden' and set 'false' as value if
// you want to hide those items. by default hidden items will be shown.
$showHiddenNavi = extractVar('showHidden', 'true');

// the javascript function that this popup calls
define('JS_FUNCTION', extractVar(_PARAM_JSFUNC_TO_CALL, 'setMenu'));

function SetXmlHeaders($charset)
{
    // Prevent the browser from caching the result.
    // Date in the past
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT') ;
    // always modified
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT') ;
    // HTTP/1.1
    header('Cache-Control: no-store, no-cache, must-revalidate') ;
    header('Cache-Control: post-check=0, pre-check=0', false) ;
    // HTTP/1.0
    header('Pragma: no-cache') ;

    // Set the response format.
    header( 'Content-Type:text/xml; charset=' . $charset ) ;
}

function ConvertToXmlAttribute( $value )
{
    return htmlspecialchars( $value );
}

function createItemActionAttribute($temp_menu) {
    //return 'javascript:alert(webFXTreeHandler.selected.text)';
    return 'javascript:setMyMenu(\''.$temp_menu->getID().'\',\''.$temp_menu->getLanguageID().'\',webFXTreeHandler.selected.text)';//\''.prepareJSName($temp_menu->getName()).'\')';
}

function prepareXMLName($str) {
    return prepareJSName($str);
}

function prepareJSName($str) {
    $str = htmlspecialchars($str);
    $str = str_replace('"', '&quot;', $str);
    //$str = addSlashes($str);
    //$str = str_replace("'", '\%27', $str);
    $str = str_replace("'", '&#039;', $str);
    return $str;
}

function createItemXmlTreeLink($item) {
    $link = new MenuChooserLink();
    $link->setJavascriptCallback(JS_FUNCTION);
    $link->setFileName('tree.xml');
    return LinkHelper::getUrlFromCMSLink($link, array(_PARAM_TREE_ID => $item->getID(), _PARAM_MODE => _MODE_XML));
}

function createTreeXmlNode($item) {
}

$hideID = extractVar(_PARAM_HIDE_TREE, '');
$data = extractVar('data', array('id' => _BIGACE_TOP_LEVEL));

$ITEM_SERVICE = new MenuService();

// create XML for Tree Loading
$myMode = extractVar(_PARAM_MODE, '');
if($myMode == _MODE_XML)
{
    $myId = extractVar(_PARAM_TREE_ID, '');
    if ($myId != '')
    {
        SetXmlHeaders($lang->getCharset());
        echo '<?xml version="1.0"?>';
        ?>
        <tree>
            <?php

                $req = new ItemRequest($ITEM_SERVICE->getItemType(), $myId);
                $req->setTreetype(ITEM_LOAD_LIGHT);
                $req->setOrderBy(ORDER_COLUMN_POSITION);
                $req->setOrder($req->_ORDER_ASC);
                // display hidden navi entrys by default
                if($GLOBALS['showHiddenNavi'] == 'true') {
                   $req->setFlagToExclude($req->FLAG_ALL_EXCEPT_TRASH);
                }
                $menu_info = new SimpleItemTreeWalker($req);

                //$menu_info = $ITEM_SERVICE->getLightTree($myId);

            for ($i=0; $i < $menu_info->count(); $i++)
            {
                $temp_menu = $menu_info->next();
                if ($ITEM_SERVICE->isLeaf($temp_menu->getID())) {
                    echo '<tree text="'.prepareXMLName($temp_menu->getName()).'" action="'.createItemActionAttribute($temp_menu).'"/>' . "\n";
                } else {
                    echo '<tree text="'.prepareXMLName($temp_menu->getName()).'" src="'.ConvertToXmlAttribute(createItemXmlTreeLink($temp_menu)).'" action="'.createItemActionAttribute($temp_menu).'"/>' . "\n";
                }
            }
            ?>
        </tree>
        <?php
    }
}
else
{
    $R_SERVICE = new RightService();
	$USERTOPRIGHT = $R_SERVICE->getMenuRight($GLOBALS['_BIGACE']['SESSION']->getUserID(), _BIGACE_TOP_LEVEL);

    ?>

    <!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <html>
    <head>
    <title>BIGACE - <?php echo getTranslation('title'); ?></title>
    <meta name="generator" content="BIGACE">
    <meta name="robots" content="noindex,nofollow">
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $lang->getCharset(); ?>">
    <script type="text/javascript" src="<?php echo _PUBLIC_TREE_DIR; ?>xtree.js"></script>
    <script type="text/javascript" src="<?php echo _PUBLIC_TREE_DIR; ?>xmlextras.js"></script>
    <script type="text/javascript" src="<?php echo _PUBLIC_TREE_DIR; ?>xloadtree.js"></script>
    <link type="text/css" rel="stylesheet" href="<?php echo _PUBLIC_TREE_DIR; ?>xtree.css" />
    <link rel="stylesheet" href="<?php echo $GLOBALS['_BIGACE']['style']['class']->getCSS(); ?>" type="text/css">
    <script type="text/javascript">

    window.focus();

    function setMyMenu(id, language, name) {
        if (typeof(opener.<?php echo JS_FUNCTION; ?>) == "undefined")
        {
            alert('There is no Javascript function called "<?php echo JS_FUNCTION; ?>" defined!');
        }
        else
        {
            opener.<?php echo JS_FUNCTION; ?>(id, language, name);
            window.close();
        }
    }

    /// XP Look
    webFXTreeConfig.rootIcon        = "<?php echo _PUBLIC_TREE_DIR; ?>images/xp/folder.png";
    webFXTreeConfig.openRootIcon    = "<?php echo _PUBLIC_TREE_DIR; ?>images/xp/openfolder.png";
    webFXTreeConfig.folderIcon      = "<?php echo _PUBLIC_TREE_DIR; ?>images/xp/folder.png";
    webFXTreeConfig.openFolderIcon  = "<?php echo _PUBLIC_TREE_DIR; ?>images/xp/openfolder.png";
    webFXTreeConfig.fileIcon        = "<?php echo _PUBLIC_TREE_DIR; ?>images/xp/file.png";
    webFXTreeConfig.lMinusIcon      = "<?php echo _PUBLIC_TREE_DIR; ?>images/xp/Lminus.png";
    webFXTreeConfig.lPlusIcon       = "<?php echo _PUBLIC_TREE_DIR; ?>images/xp/Lplus.png";
    webFXTreeConfig.tMinusIcon      = "<?php echo _PUBLIC_TREE_DIR; ?>images/xp/Tminus.png";
    webFXTreeConfig.tPlusIcon       = "<?php echo _PUBLIC_TREE_DIR; ?>images/xp/Tplus.png";
    webFXTreeConfig.iIcon           = "<?php echo _PUBLIC_TREE_DIR; ?>images/xp/I.png";
    webFXTreeConfig.lIcon           = "<?php echo _PUBLIC_TREE_DIR; ?>images/xp/L.png";
    webFXTreeConfig.tIcon           = "<?php echo _PUBLIC_TREE_DIR; ?>images/xp/T.png";
    webFXTreeConfig.blankIcon       = "<?php echo _PUBLIC_TREE_DIR; ?>images/xp/blank.png";

    var rti;
    <?php
        // fetch toplevel
        $topLevel = $ITEM_SERVICE->getItem(_BIGACE_TOP_LEVEL, ITEM_LOAD_FULL, $GLOBALS['_BIGACE']['PARSER']->getLanguage());
    ?>
    var tree = new WebFXTree("<?php echo prepareJSName($topLevel->getName()); ?>", "<?php echo createItemActionAttribute($topLevel); ?>");
    <?php

    //$menu_info = $ITEM_SERVICE->getLightTree($topLevel->getID());

    $req = new ItemRequest($ITEM_SERVICE->getItemType(), $topLevel->getID());
    $req->setTreetype(ITEM_LOAD_LIGHT);
    $req->setOrderBy(ORDER_COLUMN_POSITION);
    $req->setOrder($req->_ORDER_ASC);
    // display hidden navi entrys by default
    if($showHiddenNavi == 'true') {
       $req->setFlagToExclude($req->FLAG_ALL_EXCEPT_TRASH);
    }
    $menu_info = new SimpleItemTreeWalker($req);

    for ($i=0; $i < $menu_info->count(); $i++)
    {
        $temp_menu = $menu_info->next();
        if ($ITEM_SERVICE->isLeaf($temp_menu->getID())) {
            echo 'tree.add(new WebFXTreeItem("'.prepareJSName($temp_menu->getName()).'", "'.createItemActionAttribute($temp_menu).'"));' . "\n";
        } else {
            echo 'tree.add(new WebFXLoadTreeItem("'.prepareJSName($temp_menu->getName()).'", "'.createItemXmlTreeLink($temp_menu).'", "'.createItemActionAttribute($temp_menu).'"));' . "\n";
        }
    }

    ?>
    </script>
    </head>
    <body style="margin:10px;">
    <h1><?php echo getTranslation('title'); ?></h1>
    <?php
    if($USERTOPRIGHT->canRead()) {
    ?>
    <script type="text/javascript">
    <!--
        document.write(tree);
        document.write('<br>');
        document.write('<div style="position:relative;bottom:5px;right:10px;" align="right">');
        document.write('<button onclick="javascript:self.close();"><?php echo getTranslation('close'); ?></button>');
        document.write('</div>');
    // -->
    </script>
    <?php
    }
    else {
    	loadLanguageFile('access_rights');
        displayError(getTranslation('missing.read.rights.menu.toplevel'));
    }
    ?>
    </head>
    </body>
    </html>

<?php
	unset($res);
	unset($USERTOPRIGHT);
	unset($R_SERVICE);
}

    $GLOBALS['LOGGER']->logDebug('Hide-ID: ' . $hideID);

    include_once(_BIGACE_DIR_LIBS . 'footer.inc.php');

?>