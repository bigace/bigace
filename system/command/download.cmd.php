<?php
//
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS for MySQL                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) Kevin Papst                                              |
// | Web           http://www.bigace.de                                     |
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
 * This Command create ZIP Archives on the fly from Items with
 * the given Itemtype and ItemIDs.
 * It can handle several Items at once.
 *
 * There is always an ZIP Archive returned, even if it is empty because of missing ItemIDs.
 *
 * Submit at least two Parameter:
 * - "itemtype" (for example "itemtype=4" for Images)
 * - ItemIDs within an array "data['ids'][]=ItemID" (for example "data[ids][]=0&data[ids][]=9")
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.command
 */

import('classes.file.File');
import('classes.image.Image');
import('classes.item.ItemService');
import('classes.right.RightService');
import('classes.util.ss_zip.class');
import('classes.util.IOHelper');

require_once(_BIGACE_DIR_ADDON . 'zip/zipfile.php');

$itemtype = extractVar('itemtype', '');
$ids      = extractVar('data', array());

$languageid = $GLOBALS['_BIGACE']['PARSER']->getLanguageFromRequest();
// fallback cause ItemService uses an empty String to define a NONE Language dependend item call
if ($languageid == NULL) $languageid = '';

// create zip file
$zip = new zipfile();

$RIGHT_SERVICE = new RightService();
$ITEM_SERVICE  = new ItemService();
$ITEM_SERVICE->initItemService( $itemtype );
if ($itemtype != '' )
{
    if (count($ids) > 0 && ($itemtype == _BIGACE_ITEM_IMAGE || $itemtype == _BIGACE_ITEM_FILE))
    {
        $GLOBALS['LOGGER']->logInfo('Download Items with Itemtype:'.$itemtype);


        // add files if given
    	if (isset($ids['ids']) && is_array($ids['ids']) && (count($ids['ids']) > 0) )
    	{
    	    foreach($ids['ids'] AS $itemid)
    	    {
                $GLOBALS['LOGGER']->logInfo('Download Item with ID:'.$itemid.' and Itemtype:'.$itemtype);

    	        $ITEM_RIGHT = $RIGHT_SERVICE->getItemRight( $itemtype , $itemid, $GLOBALS['_BIGACE']['SESSION']->getUserID());

    	        if ($ITEM_RIGHT->canRead())
    	        {
    	            $FILE = $ITEM_SERVICE->getClass($itemid, ITEM_LOAD_FULL, $languageid);

    	            $ITEM_SERVICE->increaseViewCounter($FILE->getID(),$FILE->getLanguageID());

    	            // add files to zip
    	            $zip->addFile(file_get_contents($FILE->getFullURL()), $FILE->getOriginalName(), time());
    	        }
    	    }
    	}
    }
    else if($GLOBALS['_BIGACE']['PARSER']->getItemID() != _BIGACE_TOP_LEVEL)
    {
        $itemid = $GLOBALS['_BIGACE']['PARSER']->getItemID();
        $ITEM_RIGHT = $RIGHT_SERVICE->getItemRight( $itemtype , $itemid, $GLOBALS['_BIGACE']['SESSION']->getUserID());
        if ($ITEM_RIGHT->canRead())
        {
            $FILE = $ITEM_SERVICE->getClass($itemid, ITEM_LOAD_FULL, $languageid);
            // add to zip
            $zip->addFile(file_get_contents($FILE->getFullURL()), $FILE->getOriginalName(), time());
        }

    }
}

$filename = $GLOBALS['_BIGACE']['PARSER']->getFileName();

if (strtolower(getFileExtension($filename)) != "zip") {
	$filename = getNameWithoutExtension($filename);
}

if ($filename == "" || strtolower($filename) == "zip" || strtolower($filename) == ".zip") {
	//$filename = "download";
	$filename = $FILE->getOriginalName();
}

if(strpos(strtolower($filename), ".zip") === false)
	$filename .= ".zip";

// send it to the browser
header('Content-Type: application/zip');
header( 'Content-Disposition: inline; filename=' . urlencode($filename) );
ob_end_clean();
echo $zip->file();

unset ($zip);

flush();
exit;
