<?php

/**
* Display the TIP-OF-THE-DAY at on of the following positions:
*
* - if there is a Replacer is the Menus content {TIP-OF-THE-DAY} it will be replaced with the randomized Tip
* - otherwise append it at the Contents bottom
*
* Copyright (C) Kevin Papst
*
* For further information go to http://www.bigace.de/ 
*
* @version $Id$
* @author Kevin Papst 
* @package bigace.modul
*/

loadClass('tipoftheday', 'TipOfTheDay');
loadClass('tipoftheday', 'TipOfTheDayService');


$TIP_SERVICE = new TipOfTheDayService();
//randomize number and get TIP
$TIP = $TIP_SERVICE->getRandomTip();

if ($TIP != null)
{
    if (preg_match ('/{TIP-OF-THE-DAY}/i', $MENU->getContent())) 
    {
        //parse content and replace $replacer with TIP.
        $parse = $MENU->getContent();
        $parse = preg_replace('/{TIP-OF-THE-DAY}/i', $TIP->getTip(), $parse);
        $parse = preg_replace('/{LINK-OF-THE-DAY}/i', $TIP->getLink(), $parse);
        $parse = preg_replace('/{NAME-OF-THE-DAY}/i', $TIP->getName(), $parse);
        echo $parse;
    } 
    else 
    {
        // replacer could not be found - append TIP at the bottom
        
        $displayLink = (strlen(trim($TIP->getLink())) > 0);
        
        echo '<div class="tipoftheday"><b>';
        if  ($displayLink)
            echo '<a href="' . $TIP->getLink() . '">';
        
        echo $TIP->getName();
        
        if  ($displayLink)
            echo '</a>';
        echo '</b><br/>';
        echo $TIP->getTip();
        echo '</div>';
    }
}
else
{
    // fallback if no tip-of-the-day was entered into the database
    echo $MENU->getContent();
}

?>