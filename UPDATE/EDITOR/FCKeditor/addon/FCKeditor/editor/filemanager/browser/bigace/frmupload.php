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

include_once(realpath(dirname(__FILE__).'/../../../../../../system/libs/').'/init_session.inc.php');

$LANGUAGE = new Language(_ULC_);
loadLanguageFile('bigace');
loadLanguageFile('administration');
header( "Content-Type:text/html; charset=" . $LANGUAGE->getCharset() );

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<link href="browser.css" type="text/css" rel="stylesheet" />
		<script type="text/javascript" src="js/common.js"></script>
		<script type="text/javascript">
        function SetCurrentFolder( resourceType, folderPath )
        {
	        var sUrl = oConnector.ConnectorUrl +'Command=FileUpload' ;
	        sUrl += '&Type=' + resourceType ;
	        sUrl += '&CurrentFolder=' + folderPath ;
	
	        document.getElementById('frmUpload').action = sUrl ;
        }

        function OnSubmit()
        {
	        if ( document.getElementById('NewFile').value.length == 0 )
	        {
		        alert( '<?php echo getTranslation('upload_choose_file'); ?>' ) ;
		        return false ;
	        }

	        // Set the interface elements.
	        document.getElementById('btnUpload').disabled = true ;
	
	        return true ;
        }

        function OnUploadCompleted( errorNumber, fileName, message )
        {
	        // Reset the Upload Worker Frame.
	        window.parent.frames['frmUploadWorker'].location = 'about:blank' ;
	
	        // Reset the upload form (On IE we must do a little trick to avout problems).
	        if ( document.all )
		        document.getElementById('NewFile').outerHTML = '<input id="NewFile" name="NewFile" type="file">' ;
	        else
		        document.getElementById('frmUpload').reset() ;
	
	        // Reset the interface elements.
	        document.getElementById('btnUpload').disabled = false ;
	
	        switch ( errorNumber )
	        {
		        case 0 :
			        window.parent.frames['frmResourcesList'].Refresh() ;
			        resizeMe();
			        break ;
		        case 201 :
			        window.parent.frames['frmResourcesList'].Refresh() ;
			        alert( 'A file with the same name is already available. The uploaded file has been renamed to "' + fileName + '"' ) ;
			        break ;
		        case 202 :
			        alert( 'Invalid file format ' + message  ) ;
			        break ;
		        case 203 :
			        alert( 'Menu items do not support upload!' ) ;
			        break ;
		        case 204 :
			        alert( 'Missing Parameter: ' + message ) ;
			        break ;
		        case 205 :
			        alert( 'Please add a File!' ) ;
			        break ;
		        default :
			        alert( 'Error on file upload. Error number: ' + errorNumber ) ;
			        break ;
	        }
        }

        var isUploadVisible = false;

        function resizeMe() 
        {
            if (!isUploadVisible) {
                window.parent.document.getElementById('extensions').rows = "50,*,200";
                isUploadVisible = true;
                document.getElementById('resizeLink').innerHTML = '<?php echo getTranslation('title_upload'); ?>';
                document.getElementById('resizeImage').src = 'images/hideupload.gif';
            }
            else {
                window.parent.document.getElementById('extensions').rows = "50,*,15";
                isUploadVisible = false;
                document.getElementById('resizeLink').innerHTML = '<?php echo getTranslation('title_upload'); ?>';
                document.getElementById('resizeImage').src = 'images/showupload.gif';
            }
        }

        window.onload = function()
        {
	        window.top.IsLoadedUpload = true ;
        }
		</script>
	</head>
	<body bottomMargin="0" topMargin="0">
	    <a href="" onclick="javascript:resizeMe();return false;"><img id="resizeImage" src="images/showupload.gif" border="0"></a>
	    <a id="resizeLink" href="" onclick="javascript:resizeMe();return false;"><?php echo getTranslation('title_upload'); ?></a>
		<form id="frmUpload" action="" target="frmUploadWorker" method="post" enctype="multipart/form-data" onsubmit="return OnSubmit();" style="margin-top:20px">
			<table cellSpacing="0" cellPadding="0" border="0">
			<colgroup>
			    <col width="130px;" />
			    <col width="" />
			</colgroup>
				<tr>
					<td><?php echo getTranslation('name'); ?></td>
					<td align="left"><input id="NewTitle" name="NewTitle" style="width:200px" type="input"></td>
				</tr>
				<tr>
					<td><?php echo getTranslation('description'); ?></td>
					<td align="left">
					    <textarea style="width:200px" name="NewDescription"></textarea>
					</td>
				</tr>
				<tr>
					<td><?php echo getTranslation('choose_file'); ?></td>
					<td width="200" align="left"><input id="NewFile" name="NewFile" style="width:200px" type="file"></td>
				</tr>
				<tr>
					<td colspan="2" align="left"><input id="btnUpload" type="submit" value="<?php echo getTranslation('process_upload'); ?>"></td>
				</tr>
			</table>
		</form>
	</body>
</html>