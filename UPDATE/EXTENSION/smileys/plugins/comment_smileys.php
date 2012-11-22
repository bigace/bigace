<?php
/*
Plugin Name: Comment Smileys
Plugin URI: http://wiki.bigace.de/bigace:extensions:modul:smileys
Description: This plugins formats comments and renders emoticons inside into smiley icons.
Author: Kevin Papst
Version: 1.1
Author URI: http://www.kevinpapst.de/
*/

if(!defined('_BIGACE_ID'))
    die('Ooops');

Hooks::add_filter('comment_format', 'comment_smileys', 10, 1);

function comment_smileys($code)
{
    import('classes.parser.Smileys');
    return Smileys::parseCode($code, true);
}