<?php 
/*
 * Customized by Kevin Papst for BIGACE.
 * Idea and sources taken from:
 * ========================================================================
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2005 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 *
 * Authors: Frederico Caldeira Knabben (fredck@fckeditor.net)
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

	// Set the response format.
	header( 'Content-Type:text/xml; charset=utf-8' ) ;
}

function CreateXmlHeader( $command, $resourceType )
{
	SetXmlHeaders() ;
	
	// Create the XML document header.
	echo '<?xml version="1.0" encoding="utf-8" ?>' ;

	// Create the main "Connector" node.
	echo '<Connector command="' . $command . '" resourceType="' . $resourceType . '">' ;
}

function CreateXmlFooter()
{
	echo '</Connector>' ;
}

function SendError( $number, $text, $extensions = array() )
{
	SetXmlHeaders() ;
	
	// Create the XML document header
	echo '<?xml version="1.0" encoding="utf-8" ?>' ;
	
	echo '<Connector>'. createPlainError( $number, $text, $extensions ).'</Connector>' ;
	
	exit ;
}

function createPlainError( $number, $text, $extensions = array() )
{
    $err = '<Error number="' . $number . '" text="' . ConvertToXmlAttribute(htmlspecialchars( $text )) . '"';
    foreach($extensions AS $att => $val)
	   $err .= ' ' . $att . '="'.ConvertToXmlAttribute($val).'"';
	return $err . ' />' ;
}

function SendPlainError( $number, $text, $extensions = array() )
{
	echo createPlainError( $number, $text, $extensions );
}
