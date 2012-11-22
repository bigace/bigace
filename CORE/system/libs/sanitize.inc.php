<?php
//
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS for MySQL                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) Kevin Papst                                              |
// | Web           http://www.bigace.de                                     |
// | Sourceforge   http://sourceforge.net/projects/bigace/                  |
// +------------------------------------------------------------------------+
// | This source file is subject to version 2 or (at your option) any later |
// | version, of the GNU General Public License as published by the Free    |
// | Software Foundation, available at:                                     |
// | http://www.gnu.org/licenses/gpl.html                                   |
// +------------------------------------------------------------------------+
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// +------------------------------------------------------------------------+
//

/**
 * Functions used to sanitize user input.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst, formerly by wordpress.org
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.libs
 */

// original by wordpress - formatting.php
function sanitize_user($username, $strict = false)
{
	$username = strip_tags($username);
	// Kill octets
	$username = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', $username);
	$username = preg_replace('/&.+?;/', '', $username); // Kill entities
	$username = preg_replace("/'/", '', $username); // Kill single quotes
	$username = preg_replace('/"/', '', $username); // Kill double quotes
	
	// If strict, reduce to ASCII for max portability.
	if ( $strict ) {
		$username = preg_replace('|[^a-z0-9 _.\-@#]|i', '', $username);
	}

	// Consolidate contiguous whitespace
	$username = preg_replace('|\s+|', ' ', $username);

	return $username;
}

// original by wordpress - formatting.php
function sanitize_email($email)
{
	return preg_replace('/[^a-z0-9+_.@-]/i', '', $email);
}

// original by wordpress - formatting.php
function sanitize_file_name( $name )
{
	$name = strtolower( $name );
	$name = preg_replace('/&.+?;/', '', $name); // kill entities
	$name = str_replace( '_', '-', $name );
	$name = preg_replace('/[^a-z0-9\s-.]/', '', $name);
	$name = preg_replace('/\s+/', '-', $name);
	$name = preg_replace('|-+|', '-', $name);
	$name = trim($name, '-');
	return $name;
}

/**
 * make sure the given string is really plain text,
 * to prevent xss.
 */ 
function sanitize_plain_text($string)
{
	return htmlspecialchars(
	   strip_tags(
	       stripslashes(
	           $string
	       )
	   ), 
	   ENT_QUOTES
	);
}
