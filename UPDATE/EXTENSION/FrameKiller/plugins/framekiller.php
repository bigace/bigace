<?php
/*
Plugin Name: Frame Killer 
Plugin URI: http://wiki.bigace.de/bigace:extensions:framekiller
Description: This plugins tries to detect framesets around your site and kills them. Services like Digg and their "toolbar", but also Google use framesets. Think before you activate the framekiller!   
Author: Kevin Papst
Version: 0.1
Author URI: http://www.kevinpapst.de/
*/


if(!defined('_BIGACE_ID'))
    die('Ooops');

Hooks::add_filter('metatags_more', 'fk_metatags', 10, 2);

function fk_metatags($values, $item)
{
	// administration uses framesets for previews, do not kill them for logged in users!
	if ($GLOBALS['_BIGACE']['SESSION']->isAnonymous()) {
		$values[] = ' <script type="text/javascript">
  if ( top.location != location )
    top.location.href = document.location.href;
</script> ';
	}
	return $values;
}

