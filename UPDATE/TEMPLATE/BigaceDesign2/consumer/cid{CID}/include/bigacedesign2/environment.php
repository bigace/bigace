<?php
/**
* -- BIGACE DESIGN 2 --
* 
* Definition file for the Layout Environment.
* 
* Copyright (C) Kevin Papst. 
*
* For further information go to http://www.bigace.de/ 
*
* @version $Id$
* @author Kevin Papst 
*/

    
    /**
    * ---------------------------------------------
    * Please fill in your Menu IDs below!
    *
    * All IDs must be within your current Website!
    * ---------------------------------------------
    */
    
	// The Menu ID for the Sitemap. 
    define('DESIGN_LINKID_SITEMAP', $MENU->getID());

	// The Menu ID for the Impressum. 
    define('DESIGN_LINKID_IMPRESSUM', $MENU->getID());

	// The Menu ID for the Contact Formular.
    define('DESIGN_LINKID_CONTACT', $MENU->getID());


    // ----------------------------------
    //    DO NOT TOUCH SETTINGS BELOW
    // ----------------------------------
    loadLanguageFile('bigacedesign2'); 
    $publicDir = _BIGACE_DIR_PUBLIC_WEB.'cid'._CID_.'/';
    $LANGUAGE = new Language($MENU->getLanguageID());

?>