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
	include_once(dirname(__FILE__).'/searchmask.php');
	echo '</div>' . "\n";

    include_once(dirname(__FILE__) . '/sidebar.php'); 
	
	include_once(dirname(__FILE__) . '/footer.php');
	
?>