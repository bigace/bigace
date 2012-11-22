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
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *   
 * --------------------------------------------------------   
 * Inspired by:
 *
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2005 Frederico Caldeira Knabben
 * Frederico Caldeira Knabben (fredck@fckeditor.net)
 * --------------------------------------------------------   
 * 
 * @version $Id$
 * @author Kevin Papst 
 */

/**
* Error Codes: 
* 10 = No Functional Rights to access this frame
*/
include('../../../../../../../../system/libs/init_session.inc.php');
include('config.php') ;
include('util.php') ;
include('basexml.php') ;
include('cms_commands.php') ;

if (has_permission(_BIGACE_FRIGHT_USE_EDITOR ) || has_permission(_BIGACE_FRIGHT_ADMIN_MENUS ))
{
    DoResponse() ;
} 
else
{
    SendError( 10, 'You are not allowed to use this function!', array('javascript' => 'window.top.close()') ) ;
}

function DoResponse()
{
	if ( !isset( $_GET['Command'] ) || !isset( $_GET['Type'] ) || !isset( $_GET['CurrentFolder'] ) )
		return ;

	// Get the main request informaiton.
	$sCommand		= $_GET['Command'] ;
	$sResourceType	= $_GET['Type'] ;
	$sCurrentFolder	= $_GET['CurrentFolder'] ;

	// Check if it is an allowed type.
	if ( !in_array( $sResourceType, $GLOBALS['Config']['ResourceTypes'] ) )
		return ;

	// Check the current folder syntax (must begin and start with a slash).
	if ( ! ereg( '/$', $sCurrentFolder ) ) $sCurrentFolder .= '/' ;
	if ( strpos( $sCurrentFolder, '/' ) !== 0 ) $sCurrentFolder = '/' . $sCurrentFolder ;
	
	// Check for invalid folder paths (..)
	if ( strpos( $sCurrentFolder, '..' ) )
		SendError( 102, "" ) ;

	// File Upload doesn't have to Return XML, so it must be intercepted before anything.
	if ( $sCommand == 'FileUpload' )
	{
		FileUpload( $sResourceType, $sCurrentFolder ) ;
		return ;
	}

    $pos = strpos($sResourceType, "CMS_");
    if ($pos === false) 
    {
        SendError( 1, 'This option is disabled!' ) ;
    }
    else
    {
    	// start buffering
    	ob_start();
	    CreateXmlHeader( $sCommand, $sResourceType ) ;
    	
        $itemtype = substr($sResourceType, 4, 1);
        if (strlen($itemtype) == 1)
        {
        	switch ( $sCommand )
        	{
        		case 'GetFolders' :
        			GetFolders( $itemtype, $sCurrentFolder ) ;
        			break ;
        		case 'GetFoldersAndFiles' :
        			GetFoldersAndFiles( $itemtype, $sCurrentFolder ) ;
        			break ;
        		case 'CreateFolder' :
        			CreateFolder( $itemtype, $sCurrentFolder ) ;
        			break ;
        		default:
        		    SendPlainError(1, 'This Command is not supported!');
        		    break ;
        	}
        }
        else
        {
            SendPlainError(1, 'Could not fetch your requested Itemtype, check your Request Parameter!');
        }

    	CreateXmlFooter() ;
    	
    	// get xml from buffer
    	$cmdXml = ob_get_contents();
    	// stop buffering and send xml
    	ob_end_flush();

    	$GLOBALS['LOGGER']->logDebug("Sending XML to FCKeditor Filemanager: " . $cmdXml);
    }

	exit ;
}
