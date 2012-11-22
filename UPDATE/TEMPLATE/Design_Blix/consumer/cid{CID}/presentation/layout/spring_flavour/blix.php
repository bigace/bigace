<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst.
 * -------------------------------------------------
 * The BLIX Layout for BIGACE.
 * 
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 */
 
	include_once(dirname(__FILE__) . '/environment.php');
	include_once(dirname(__FILE__) . '/header.php');

	echo '<div id="content">' . "\n";
	echo '<h2>' . $MENU->getName() . '</h2>';
	include(_BIGACE_DIR_CID . 'include/loadModul.php');
	echo '</div>' . "\n";

	if(!IS_SINGLE_COLUMN) {
	    include_once(dirname(__FILE__) . '/sidebar.php'); 
	}
	
	include_once(dirname(__FILE__) . '/footer.php');
	
?>