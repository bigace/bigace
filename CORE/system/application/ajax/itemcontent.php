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

if($GLOBALS['_BIGACE']['SESSION']->isAnonymous())
{
    import('classes.exception.ExceptionHandler');
    import('classes.exception.NoFunctionalRightException');
    ExceptionHandler::processCoreException( new NoFunctionalRightException('Protected Area. You are not allowed to enter!', createMenuLink(_BIGACE_TOP_LEVEL)) );
    return;
}

/**
 * This script sends an Items content to the client.
 */

import('classes.item.ItemService');
import('classes.right.RightService');

$itemtype = _BIGACE_ITEM_MENU;
if(isset($_GET['itemtype']))
    $itemtype = $_GET['itemtype'];
else if(isset($_POST['itemtype']))
    $itemtype = $_POST['itemtype'];

$itemService = new ItemService($itemtype);
$item = $itemService->getItem($GLOBALS['_BIGACE']['PARSER']->getItemID(), ITEM_LOAD_FULL, $GLOBALS['_BIGACE']['PARSER']->getLanguage());
if(!$item->exists()) {
    $item = $itemService->getItem($GLOBALS['_BIGACE']['PARSER']->getItemID(), ITEM_LOAD_FULL);
}

if(!$item->exists()) {
    $l = new Language($GLOBALS['_BIGACE']['PARSER']->getLanguage());
} else {
    $l = new Language($item->getLanguageID());
}
CreateXmlHeader($l->getCharset());

echo '<Item>';
if(!$item->exists()) {
    echo '<Error>Requested Item does not exist.</Error>';
}
else {
    $RS = new RightService();
    $right = $RS->getItemRight($item->getItemtypeID(), $item->getID(), $GLOBALS['_BIGACE']['SESSION']->getUserID());
    if($right->canRead()) {
        echo '<Content>';
        echo '<![CDATA[';
        echo $item->getContent();
        echo ']]>';
        echo '</Content>';
    } else {
        echo '<Error>Not sufficient rights to read this Item.</Error>';
    }
}

echo '</Item>' . "\n";
CreateXmlFooter();

function ConvertToXmlAttribute( $value )
{
    return htmlspecialchars( $value );
}

// Dump HTTP Headers and create the XML document root
function CreateXmlHeader($enc = null)
{
    if($enc == null) {
        $l = new Language($GLOBALS['_BIGACE']['PARSER']->getLanguage());
        $enc = $l->getCharset();
    }
    SetXmlHeaders($enc) ;

    echo '<?xml version="1.0" encoding="'.$enc.'" ?>' ;
    echo "\n";
}

// dump the XML Footer
function CreateXmlFooter()
{
    echo "\n";
}

function SetXmlHeaders($enc)
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
    header( 'Content-Type:text/xml; charset='.$enc ) ;
}

?>
