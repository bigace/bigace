<?php
/**
* Simple RSS News Reader
*
* Copyright (C) Kevin Papst. 
*
* For further information go to http://www.bigace.de/ 
*
* @version $Id$
* @author Kevin Papst 
* @package bigace.modul
*/

import('classes.rss.RSSParser');

$feedFileName = dirname(__FILE__) . '/rssList.ini' ;
$newsFeedURLs = parse_ini_file ( $feedFileName, TRUE);

// Configuration array
$_NEWS                      = array();
$_NEWS['PARAM_URL']         = 'newsfeedUrl';
$_NEWS['PARAM_ACTION']      = 'newsAction';
$_NEWS['ACTION_READ']       = 'newsRead';

$newsAction         = isset($_POST[$_NEWS['PARAM_ACTION']]) ? $_POST[$_NEWS['PARAM_ACTION']] : '-1';
$feedUrl            = isset($_POST[$_NEWS['PARAM_URL']]) ? $_POST[$_NEWS['PARAM_URL']] : '';

?>
<div>
<form name="newfeedForm" action="<?php echo createMenuLink($MENU->getID()); ?>" method="POST">
<input type="hidden" name="<?php echo $_NEWS['PARAM_ACTION']; ?>" value="<?php echo $_NEWS['ACTION_READ']; ?>">
<table align="center" class="ct" cellpadding="2" cellspacing="2" width="90%">
    <tr>
    <td>
    <img src="<?php echo _BIGACE_DIR_PUBLIC_WEB; ?>system/images/rss.png" border="0" alt="RSS News lesen">    
    Newsquelle aussuchen:<br>
    <select name="newsfeedUrl">
<?php    
    foreach ($newsFeedURLs AS $desc => $urls)
    {
        echo '<optgroup label="'.$desc.'">';
        foreach ($urls as $newsName => $newsUrl)
        {
            echo '<option value="'.$newsUrl.'"';
            if($feedUrl == $newsUrl)
                echo ' selected';
            echo '>'.$newsName.'</option>';
        }
        echo '</optgroup>';
    }
?>
    
    </select>
    <input style="width:100px" type="submit" value="Lesen">
    </td>
    </tr>
</table>

</form>
</div>

<?php

if ($newsAction == $_NEWS['ACTION_READ'])
{

    $message = '';
    $seenFeed = false;
    $parseUrl = $feedUrl;
    
    if ($parseUrl != '')
    {
        $rss = new RSSParserCache($GLOBALS['_BIGACE']['DIR']['cache']);
        $rss->enableCaching();
        $rss->setLifetime(3600);
        $rss->OutputOptions(array('ChannelDesc' => TRUE,   // Channel - Beschreibung
                                  'ItemDesc'    => TRUE,   // Item - Beschreibung
                                  'Image'       => true,   // eventuell 'enthaltene' Grafik
                                  'Textinput'   => false    // eventuell 'enthaltenes' Formular
                                  ));
        $rss->OutputStyles(array('maxItem'        => 15,           // maximale Anzahl von angezeigten Items, wenn der Channel mehr enthaelt
                                 'ItemSeparator'  => '&nbsp;',       // Trenner zwischen den Items
                                 'DescSeparator'  => '<br>',        // Trenner zwischen Titel, Beschreibung und Link
                                 'ChannelTitle'   => '',           // CSS class for Channel-Titel
                                 'ChannelDesc'    => '',           // CSS class for Channel-Beschreibung
                                 'ChannelLink'    => '',           // CSS class for Channel-Link
                                 'ItemTitle'      => '',           // CSS class for Item-Titel
                                 'ItemDesc'       => '',           // CSS class for Item-Beschreibung
                                 'ItemLink'       => '',           // CSS class for Item-Link
                                 'TextInputTitle' => '',           // CSS class for TextInput-Titel
                                 'TextInputDesc'  => '',           // CSS class for TextInput-Beschreibung
                                 'TextInput'      => '',           // CSS class for TextInput-Feld
                                 'Submit'         => '',           // CSS class for TextInput-Submit
                                 'SubmitValue'    => 'GO',         // Submit Button Beschriftung
                                 'TableWidth'     => '90%',       // Tabellenbreite des Channels
                                 'TableClass'     => 'ct',         
                                 'TableAlign'     => 'center',
                                 'LinkTarget'     => '_blank'      // Linkziel for Links, die im channel enthalten sind
                                 ));
        
        
        $seenFeed = true;
        if ($out = $rss->parseRSS($parseUrl)) 
        {
            echo '<div>' . $out . '</div><br>';
        } 
        else 
        {
            $message .= 'URL: <i>' . $parseUrl . '</i><br>Fehlercode: '.$rss->getErrorCode().'<br>Fehlermeldung: '.$rss->getErrorMessage();
        }
    } // if ($parseUrl != '')


    if (!$seenFeed)
    {
        $message .= 'Bitte geben Sie ein Newsfeed an!';
    }
    

    echo '<div><table align="center" style="border-width:1px; border-style=dashed"><tr><td><b>' . $message . '</b></td></tr></table></div><br>';
}

?>