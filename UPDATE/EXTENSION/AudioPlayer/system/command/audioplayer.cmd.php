<?php

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

/**
 * This command is specialized for sending XSPF playlists.
 * 
 * Its part of the Audio-Player Plugin.
 */

import('classes.file.File');
import('classes.item.ItemService');
import('classes.item.ItemEnumeration');
import('classes.right.RightService');

$ITEM_SERVICE  = new ItemService();
$ITEM_SERVICE->initItemService( _BIGACE_ITEM_FILE );

header('Content-Type: application/xspf+xml');

// Fetch all audio items from database
$sqlString = 'SELECT a.* FROM {DB_PREFIX}item_{ITEMTYPE} a WHERE a.cid={CID} AND (a.mimetype={MIMETYPE1} OR a.mimetype={MIMETYPE2})';

$values = array(
    'ITEMTYPE'    => _BIGACE_ITEM_FILE,
    'CID'         => _CID_,
    'MIMETYPE1'    => 'audio/mp3',
    'MIMETYPE2'    => 'audio/mpeg'
);

$title    = isset($_GET['title']) ? $_GET['title'] : 'Playlist' ;
$sql      = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
$result   = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
$playlist = new ItemEnumeration($result, _BIGACE_ITEM_FILE);

echo '<?xml version="1.0" encoding="UTF-8"?>
<playlist version="1" xmlns="http://xspf.org/ns/0/">
    <title>'.urldecode($title).'</title>
    <trackList>
';
  
for ($i=0; $i < $playlist->count(); $i++) 
{
    $audiofiles[0] = $playlist->next();
    foreach ($audiofiles as $audiofile) {
        //TODO Any way to get info from ID3Tag (pear MP3/Id.php) ???
        if (strstr($audiofile->getName(), ".mp3")) {
            //$songtitle = $this->ru2Lat(substr($audiofile->getName(), 0, strlen($audiofile->getItemText(2))-4));
            $songtitle = substr($audiofile->getName(), 0, strlen($audiofile->getItemText(2))-4);
        } else {
            //$songtitle = $this->ru2Lat($audiofile->getName());
            $songtitle = $audiofile->getName();
        }

        echo '
        <track>
            <title>'.$songtitle.'</title>
            <location>'.LinkHelper::itemUrl($audiofile).'</location>
        </track>';
    }
}
echo '
    </trackList>
</playlist>';
    
flush();
exit;
