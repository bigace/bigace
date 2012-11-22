<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst.
 * -------------------------------------------------
 * The BLIX Layout for BIGACE.
 */
 

define('_BLIX_CONFIG_PACKAGE', 'blix.design');
// cache config package
ConfigurationReader::getPackage(_BLIX_CONFIG_PACKAGE);
 
/*
 * This is the TOP LEVEL Item of the Blix Design.
 */
define('_BLIX_HOME', _BIGACE_TOP_LEVEL);
/*
 * Below this Item the TOP Menu Items resist.
 * All Children of the given ID will be shown in the top navigation.
 */
define('_BLIX_TOP_MENU', ConfigurationReader::getConfigurationValue(_BLIX_CONFIG_PACKAGE, 'top.menu.start.id', _BLIX_HOME));
/**
 * Will be shown in the footer as Copyright holder.
 */
define('_BLIX_COPYRIGHT_BY', ConfigurationReader::getConfigurationValue(_BLIX_CONFIG_PACKAGE, 'copyright.footer', 'Kevin Papst'));
/**
 * Show the Login/Logout Linkwithin the Footer?
 */
define('_BLIX_SHOW_STATUS', ConfigurationReader::getConfigurationValue(_BLIX_CONFIG_PACKAGE, 'show.footer.login', true));
/**
 * Show the TOP-LEVEL Link in the Menu?
 */
define('_BLIX_HOME_IN_MENU', ConfigurationReader::getConfigurationValue(_BLIX_CONFIG_PACKAGE, 'show.home.in.topmenu', false));
/**
 * Show the Google Search Form?
 */
define('_BLIX_SEARCH_WITH_GOOLGE', ConfigurationReader::getConfigurationValue(_BLIX_CONFIG_PACKAGE, 'show.google.search', false));

/*
 * -------------------------------------------
 * ! NO FURTHER EDITING NECESSARY !
 * -------------------------------------------
 *
 * The following values are calculated by the 
 * script itself. DON'T MODIFY!
 */
 
loadClass('util', 'ApplicationLinks');
loadLanguageFile('blix');

define('_BLIX_WEB_DIR', _BIGACE_DIR_PUBLIC_WEB.'spring_flavour/');
define('_IS_ANONYMOUS', $GLOBALS['_BIGACE']['SESSION']->isAnonymous());
define('IS_SINGLE_COLUMN', false);

$appLinks = new ApplicationLinks();
// ---- [start] get Portlets for this Page ----
$portlets = array();
$services = ServiceFactory::get();
$portletService = $services->getService('portlet');
$portlets = $portletService->getPortlets(_BIGACE_ITEM_MENU, $MENU->getID(), $MENU->getLanguageID());
unset($portletService);
unset($services);
// ---- [stop] get Portlets for this Page ----

$_BLIX = array( 
                'LANGUAGE'  => new Language($MENU->getLanguageID()),
                'TOP_LEVEL' => $MENU_SERVICE->getMenu(_BLIX_HOME, $MENU->getLanguageID()),
                'PORTLETS'  => $portlets
);


?>