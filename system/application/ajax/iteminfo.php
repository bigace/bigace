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
 * This script sends XML Infos about an Item to the client.
 */
define('PARAM_ITEMTYPE', 'itemtype');

import('classes.item.ItemService');
import('classes.right.RightService');
import('classes.workflow.WorkflowService');

$itemtype = extractVar(PARAM_ITEMTYPE, _BIGACE_ITEM_MENU);
$itemService = new ItemService($itemtype);
$RS = new RightService();
$item = $itemService->getItem($GLOBALS['_BIGACE']['PARSER']->getItemID(), ITEM_LOAD_FULL, $GLOBALS['_BIGACE']['PARSER']->getLanguage());
if(!$item->exists())
    $item = $itemService->getItem($GLOBALS['_BIGACE']['PARSER']->getItemID(), ITEM_LOAD_FULL);

$l = new Language($item->getLanguageID());
CreateXmlHeader($l->getCharset());
echo getXmlForItem($item);
CreateXmlFooter();

// read all item infos
function getXmlForItem($item)
{
    $xml  = '<Item>' . "\n";

    if($item->exists())
    {
        $right = $GLOBALS['RS']->getItemRight($item->getItemtypeID(), $item->getID(), $GLOBALS['_BIGACE']['SESSION']->getUserID());
        if($right->canRead())
        {
            $xml .= getXmlForItemDetails($item);
            $xml .= getXmlForLanguages($item);
            $xml .= getXmlForRights($item);
            $xml .= getXmlForWorkflow($item);
        }
    }

    $xml .= '</Item>' . "\n";
    return $xml;
}

/**
 * All ItemDetails that can be fetched by calling Item->...()
 */
function getXmlForItemDetails($item)
{
    $xml  = createPlainNode('Itemtype', $item->getItemtype());
    $xml .= createPlainNode('ID', $item->getID());
    $xml .= createPlainNode('Name', $item->getName());
    $xml .= createPlainNode('Language', $item->getLanguageID());
    $xml .= createPlainNode('Description', $item->getDescription());
    $xml .= createPlainNode('Catchwords', $item->getCatchwords());
    $xml .= createPlainNode('Parent', $item->getParentID());
    $xml .= createPlainBooleanNode('IsHidden', $item->isHidden());
    $xml .= createPlainBooleanNode('IsLeaf', $GLOBALS['itemService']->isLeaf($item->getID()));

    return $xml;
}

/**
 * Information about the Item Rights.
 */
function getXmlForRights($item)
{
    $right = $GLOBALS['RS']->getItemRight($item->getItemtypeID(), $item->getID(), $GLOBALS['_BIGACE']['SESSION']->getUserID());

    $xml  = '<Right user="'.$GLOBALS['_BIGACE']['SESSION']->getUserID().'">' . "\n";
    $xml .= createPlainBooleanNode('Read', $right->canRead());
    $xml .= createPlainBooleanNode('Write', $right->canWrite());
    $xml .= createPlainBooleanNode('Delete', $right->canDelete());
    $xml .= '</Right>' . "\n";
    return $xml;
}

/**
 * Information about the Workflow.
 */
function getXmlForWorkflow($item)
{
    $ws = new WorkflowService($item->getItemtypeID());

    $xml  = '<Workflow>' . "\n";
    $xml .= createPlainBooleanNode('InWorkflow', $ws->hasRunningWorkflow($item->getID(), $item->getLanguageID()));
    $xml .= '</Workflow>' . "\n";
    return $xml;
}

/**
 * reads all item languages.
 */
function getXmlForLanguages($item)
{
    $s = new ItemService($item->getItemtype());
    $ile = $s->getItemLanguageEnumeration($item->getID());

    $xml  = '<Languages>' . "\n";

    for ($i=0; $i < $ile->count(); $i++)
    {
        $tempLanguage = $ile->next();
        $xml .= createPlainNode('Language', $tempLanguage->getLocale());
    }

    $xml .= '</Languages>' . "\n";
    return $xml;
}

function ConvertToXmlAttribute( $value )
{
    return htmlspecialchars( $value );
}

function createPlainBooleanNode($nodeName, $nodeValue)
{
    return createPlainNode($nodeName, (is_bool($nodeValue) && $nodeValue === TRUE ? 'TRUE' : 'FALSE'));
}

function createPlainNode($nodeName, $nodeValue)
{
    return '  <'.$nodeName.'>' . ConvertToXmlAttribute($nodeValue) . '</'.$nodeName.'>' . "\n";
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