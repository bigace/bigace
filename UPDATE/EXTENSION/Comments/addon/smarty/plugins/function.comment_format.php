<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst.
 *
 * BIGACE is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * BIGACE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * @package addon.smarty
 * @subpackage function
 */

/*
 * Formats a users comment.
 * 
 * Paramater:
 * ===========
 * - text       = REQUIRED - the text to be formatted
 * - nl2br      = OPTIONAL (default: false)
 * - assign     = OPTIONAL - if not set the result is returned, otherwise bound to the named smarty variable 
 * 
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package addon.smarty
 * @subpackage function
 */
function smarty_function_comment_format($params, &$smarty)
{	
	if(!isset($params['text'])) {
		$smarty->trigger_error("comment_format: missing 'text' attribute");
		return;
	}
	
	if(!defined('COMMENT_NOFOLLOW')) {
    	if(ConfigurationReader::getConfigurationValue('comments', 'rel.nofollow', false))
    	    define('COMMENT_NOFOLLOW', ' rel="nofollow"');
    	else
    	    define('COMMENT_NOFOLLOW', '');
	}
	$nl2br = (isset($params['nl2br']) && $params['nl2br'] === true) ? true : false;
	$useFilter = (isset($params['filter']) && $params['filter'] === false) ? false : true;
		
	$ret = ' ' . $params['text'];
	// in testing, using arrays here was found to be faster
	$ret = preg_replace_callback('#([\s>])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);
	$ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $ret);
	$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);
	// this one is not in an array because we need it to run last, for cleanup of accidental links within links
	$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);

	$ret = trim($ret);

    if($useFilter)
        $ret = Hooks::apply_filters('comment_format', $ret);
        
	if($nl2br)
		$ret = nl2br($ret);
			
	if(isset($params['assign'])) {
    	$smarty->assign($params['assign'], $ret);
    	return;
	}
	
	return $ret;
}

//
// Some code taken from Wordpress /wp-includes/kses.php and /wp-includes/formatting.php
//

function _make_url_clickable_cb($matches) {
	$ret = '';
	$url = $matches[2];
	$url = clean_url($url);
	if ( empty($url) )
		return $matches[0];
	// removed trailing [.,;:] from URL
	if ( in_array(substr($url, -1), array('.', ',', ';', ':')) === true ) {
		$ret = substr($url, -1);
		$url = substr($url, 0, strlen($url)-1);
	}
	return $matches[1] . '<a href="'.$url.'"'.COMMENT_NOFOLLOW.'>'.$url.'</a>' . $ret;
}

function _make_web_ftp_clickable_cb($matches) {
	$ret = '';
	$dest = $matches[2];
	$dest = 'http://' . $dest;
	$dest = clean_url($dest);
	if ( empty($dest) )
		return $matches[0];
	// removed trailing [,;:] from URL
	if ( in_array(substr($dest, -1), array('.', ',', ';', ':')) === true ) {
		$ret = substr($dest, -1);
		$dest = substr($dest, 0, strlen($dest)-1);
	}
	return $matches[1] . '<a href="'.$dest.'"'.COMMENT_NOFOLLOW.'>'.$dest.'</a>' . $ret;
}

function _make_email_clickable_cb($matches) {
	$email = $matches[2] . '@' . $matches[3];
	return $matches[1] . '<a href="mailto:'.$email.'">'.$email.'</a>';
}

function clean_url( $url, $protocols = null, $context = 'display' ) {
	$original_url = $url;

	if ('' == $url) return $url;
	$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@()]|i', '', $url);
	$strip = array('%0d', '%0a');
	$url = str_replace($strip, '', $url);
	$url = str_replace(';//', '://', $url);
	/* If the URL doesn't appear to contain a scheme, we
	 * presume it needs http:// appended (unless a relative
	 * link starting with / or a php file).
	*/
	if ( strpos($url, ':') === false &&
		substr( $url, 0, 1 ) != '/' && !preg_match('/^[a-z0-9-]+?\.php/i', $url) )
		$url = 'http://' . $url;

	// Replace ampersands ony when displaying.
	if ( 'display' == $context )
		$url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);

	if ( !is_array($protocols) )
		$protocols = array('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet');
	if ( kses_bad_protocol( $url, $protocols ) != $url )
		return '';

	return Hooks::apply_filters('clean_url', $url, $original_url, $context);
}

/**
 * wp_kses_no_null() - Removes any NULL characters in $string.
 *
 * @param string $string
 * @return string
 */
function kses_no_null($string) {
	$string = preg_replace('/\0+/', '', $string);
	$string = preg_replace('/(\\\\0)+/', '', $string);

	return $string;
}

function kses_bad_protocol($string, $allowed_protocols) {
	$string = kses_no_null($string);
	$string = preg_replace('/\xad+/', '', $string); # deals with Opera "feature"
	$string2 = $string.'a';

	while ($string != $string2) {
		$string2 = $string;
		$string = kses_bad_protocol_once($string, $allowed_protocols);
	} # while

	return $string;
}

function kses_bad_protocol_once($string, $allowed_protocols) {
	$_kses_allowed_protocols = $allowed_protocols;

	$string2 = preg_split('/:|&#58;|&#x3a;/i', $string, 2);
	if ( isset($string2[1]) && !preg_match('%/\?%', $string2[0]) )
		$string = kses_bad_protocol_once2($string2[0], $allowed_protocols) . trim($string2[1]);
	else
		$string = preg_replace_callback('/^((&[^;]*;|[\sA-Za-z0-9])*)'.'(:|&#58;|&#[Xx]3[Aa];)\s*/', create_function('$matches', 'global $_kses_allowed_protocols; return kses_bad_protocol_once2($matches[1], $_kses_allowed_protocols);'), $string);

	return $string;
}

function kses_decode_entities($string) {
	$string = preg_replace('/&#([0-9]+);/e', 'chr("\\1")', $string);
	$string = preg_replace('/&#[Xx]([0-9A-Fa-f]+);/e', 'chr(hexdec("\\1"))', $string);

	return $string;
}

function kses_bad_protocol_once2($string, $allowed_protocols) {
	$string2 = kses_decode_entities($string);
	$string2 = preg_replace('/\s/', '', $string2);
	$string2 = kses_no_null($string2);
	$string2 = preg_replace('/\xad+/', '', $string2);
	# deals with Opera "feature"
	$string2 = strtolower($string2);

	$allowed = false;
	foreach ($allowed_protocols as $one_protocol)
		if (strtolower($one_protocol) == $string2) {
			$allowed = true;
			break;
		}

	if ($allowed)
		return "$string2:";
	else
		return '';
}
