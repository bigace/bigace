<?php
//
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS for MySQL                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) 2002-2006 Kevin Papst                                    |
// | Web           http://www.bigace.de                                     |
// | Mirror        http://bigace.sourceforge.net/                           |
// | Sourceforge   http://sourceforge.net/projects/bigace/                  |
// +------------------------------------------------------------------------+
// | This source file is subject to version 2 or (at your option) any later |
// | version, of the GNU General Public License as published by the Free    |
// | Software Foundation, available at:                                     |
// | http://www.gnu.org/licenses/gpl.html                                   |
// +------------------------------------------------------------------------+
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// +------------------------------------------------------------------------+
//

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

/**
 * This command is specialized for sending files to clients.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.command
 */

loadClass('file', 'File');
loadClass('item', 'ItemService');
loadClass('right', 'RightService');

$RIGHT_SERVICE = new RightService();

$ITEM_SERVICE  = new ItemService();
$ITEM_SERVICE->initItemService( _BIGACE_ITEM_FILE );

$itemid 	= $GLOBALS['_BIGACE']['PARSER']->getItemID();
$languageid = $GLOBALS['_BIGACE']['PARSER']->getLanguageFromRequest();
// fallback cause ItemService uses an empty String to define a NONE Language dependend item call
if ($languageid == NULL) $languageid = '';

$ITEM_RIGHT = $RIGHT_SERVICE->getItemRight(_BIGACE_ITEM_FILE, $itemid, $GLOBALS['_BIGACE']['SESSION']->getUserID());

if ($ITEM_RIGHT->canRead())
{
    $FILE = $ITEM_SERVICE->getClass($itemid, ITEM_LOAD_FULL, $languageid);

   	$ITEM_SERVICE->increaseViewCounter($FILE->getID(),$FILE->getLanguageID());

    header('Content-Type: '. $FILE->getMimetype());
    header('Content-Disposition: inline; filename='.urlencode($FILE->getOriginalName()));
//    header('Content-Length: ' . filesize($FILE->getFullURL()));
    ob_end_clean();
    readfile ($FILE->getFullURL());
}

flush();
exit;
