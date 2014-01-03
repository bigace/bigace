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
 * For further information visit {@link http://www.kevinpapst.de www.kevinpapst.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 */


/**
 * Some helper function handling XML and Ajax requests
 */

function SetXmlHeaders()
{
	// Prevent the browser from caching the result.
	// Date in the past
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT') ;
	// always modified
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT') ;
	// HTTP/1.1
	header('Cache-Control: no-store, no-cache, must-revalidate') ;
	header('Cache-Control: post-check=0, pre-check=0', false) ;
	// HTTP/1.0
	header('Pragma: no-cache') ;

    $l = new Language(ADMIN_LANGUAGE);
    // Set the response format.
    header( 'Content-Type:text/xml; charset=' . $l->getCharset() ) ;
}

function ConvertToXmlAttribute( $value )
{
	return htmlspecialchars( $value );
}

function prepareXMLName($str) {
    return prepareJSName($str);
}

function prepareJSName($str) {
    $str = htmlspecialchars($str);
    $str = str_replace('"', '&quot;', $str);
    //$str = addSlashes($str);
    //$str = str_replace("'", '\%27', $str);
    $str = str_replace("'", '&#039;', $str);
    return $str;
}

function createBooleanNode($nodeName, $nodeValue, $atts = array())
{
    return createXmlNode($nodeName, (is_bool($nodeValue) && $nodeValue === TRUE ? 'TRUE' : 'FALSE'), $atts);
}

function createPlainNode($nodeName, $nodeValue)
{
    return createXmlNode($nodeName, $nodeValue, array());
}

function createXmlNode($nodeName, $nodeValue, $atts = array())
{
    $xml  = '<'.$nodeName;
    foreach($atts AS $key => $value)
        $xml .= ' ' . $key . '="'.$value.'"';
    $xml .= '>'.ConvertToXmlAttribute($nodeValue).'</'.$nodeName.'>' . "\n";
    return $xml;
}
