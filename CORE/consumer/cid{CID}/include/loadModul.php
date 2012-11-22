<?php
/**
 * Standard include for displaying the Menus Modul.
 * You might include this Script even in a loop for displaying several Pages at once.
 *
 * Copyright (C) Kevin Papst.
 *
 * For further information go to {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 */

    $_seenModul = false;        // if a modul could be found, switch this value
    $mod = $MENU->getModul();   // menus configurated modul

    if($mod != null && is_object($mod)) {
        if ($mod->isTranslated()) {
            $mod->loadTranslation( $GLOBALS['_BIGACE']['PARSER']->getLanguage() );
        }

        if(file_exists($mod->getFullURL())) {
            $_seenModul = true;
            include($mod->getFullURL());
        }
    }

    // fallback if Modul could not be found
    // probably it was deleted before
    if(!$_seenModul) {
        $GLOBALS['LOGGER']->logError('Could not find Menus ('.$MENU->getID().') Modul "'.$MENU->getModulID().'" ... loading Default Modul "displayContent".');
        $mod = new Modul('displayContent');
        if(file_exists($mod->getFullURL())) {
            $_seenModul = true;
            include($mod->getFullURL());
        }
    }

    // all right we could not even find the default Modul to display Content
    // try to display Content by directly fetching it from Menu
    if(!$_seenModul) {
        $GLOBALS['LOGGER']->logError('Could not even find Standard Modul "displayContent" ... trying to display plain Menu Content.');
        echo $MENU->getContent();
    }

	// if you include this file more than once, the unset causes strange errors 
    //unset ($_seenModul);
    //unset ($mod);

?>