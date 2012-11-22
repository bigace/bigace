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

// initialize a proper bigace session. 
include_once(realpath(dirname(__FILE__).'/../../../../../../system/libs/').'/init_session.inc.php');

$LANGUAGE = new Language(_ULC_);
loadLanguageFile('bigace');
header( "Content-Type:text/html; charset=" . $LANGUAGE->getCharset() );

$brwLang = isset($_GET['brwLang']) ? $_GET['brwLang'] : (isset($_POST['brwLang']) ? $_POST['brwLang'] : $GLOBALS['_BIGACE']['SESSION']->getLanguageID());
    
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
    <head>
        <title>BIGACE <?php echo _BIGACE_ID; ?> | Filemanager</title>
        <link href="browser.css" type="text/css" rel="stylesheet">
        <script type="text/javascript" src="js/fckxml.js"></script>
        <script language="javascript">

var allResourceTypes = [
    ['CMS_1','<?php echo getTranslation('item_1'); ?>', ''],
    ['CMS_4','<?php echo getTranslation('item_4'); ?>', ''],
    ['CMS_5','<?php echo getTranslation('item_5'); ?>', '']
] ;

function GetUrlParam( paramName )
{
    var oRegex = new RegExp( '[\?&]' + paramName + '=([^&]+)', 'i' ) ;
    var oMatch = oRegex.exec( window.top.location.search ) ;
    
    if ( oMatch && oMatch.length > 1 )
        return oMatch[1] ;
    else
        return '' ;
}

// FIXME can ths be removed ?
function noRightsKick() { }

var oConnector = new Object() ;
oConnector.CurrentFolder    = '/' ;
oConnector.CurrentFolderID  = '-1' ;

var sConnUrl = GetUrlParam( 'Connector' ) ;

// Gecko has some problems when using relative URLs (not starting with slash).
if ( sConnUrl.substr(0,1) != '/' && sConnUrl.indexOf( '://' ) < 0 )
    sConnUrl = window.location.href.replace( /browser.php.*$/, '' ) + sConnUrl ;

oConnector.ConnectorUrl = sConnUrl + ( sConnUrl.indexOf('?') != -1 ? '&' : '?' ) ;

var sServerPath = GetUrlParam( 'ServerPath' ) ;
if ( sServerPath.length > 0 )
    oConnector.ConnectorUrl += 'ServerPath=' + escape( sServerPath ) + '&' ;

oConnector.ConnectorUrl += '<?php echo bigace_session_name() . "=" . bigace_session_id(); ?>&'; 

oConnector.ResourceType     = GetUrlParam( 'Type' ) ;
oConnector.ResourceTypes    = new Array();
oConnector.ResourceTypes[0] = oConnector.ResourceType

if( oConnector.ResourceType.length > 0 ) {
    var splitted = oConnector.ResourceType.split(",");
    if(splitted.length > 1) {
        oConnector.ResourceType     = splitted[0];
        oConnector.ResourceTypes    = splitted;
    }
}

oConnector.ShowAllTypes     = ( oConnector.ResourceType.length == 0 ) ;

if ( oConnector.ShowAllTypes ) {
    oConnector.ResourceType = allResourceTypes[0][0];
}

function setActualFolderTitle(title) {
    frames['frmActualFolder'].SetCurrentFolder(title);
}

oConnector.SendCommand = function( command, params, callBackFunction )
{
    var sUrl = this.ConnectorUrl + 'Command=' + command ;
    sUrl += '&Type=' + this.ResourceType ;
    sUrl += '&CurrentFolder=' + this.CurrentFolderID ;
    sUrl += '&brwLang=<?php echo $brwLang; ?>';
    
    if ( params ) sUrl += '&' + params ;
    
    //alert(sUrl.split("?")[1]);

    var oXML = new FCKXml() ;
    
    if ( callBackFunction )
        oXML.LoadUrl( sUrl, callBackFunction ) ;    // Asynchronous load.
    else
        return oXML.LoadUrl( sUrl ) ;
}

oConnector.CheckError = function( responseXml )
{
    var iErrorNumber = 0
    var oErrorNode = responseXml.SelectSingleNode( 'Connector/Error' ) ;
    
    if ( oErrorNode )
    {
        iErrorNumber = parseInt( oErrorNode.attributes.getNamedItem('number').value ) ;
        
        switch ( iErrorNumber )
        {
            case 0 :
                break ;
            case 1 :    
                // Custom error. Message placed in the "text" attribute.
                alert( oErrorNode.attributes.getNamedItem('text').value ) ;
                
                if(oErrorNode.attributes.getNamedItem('javascript') != null)
                    eval(oErrorNode.attributes.getNamedItem('javascript').value);
                break ;
            case 10 :
                // User has no rights to access this frame
                if  ( oErrorNode.attributes.getNamedItem('text') != null )
                    alert( oErrorNode.attributes.getNamedItem('text').value ) ;
                else 
                    alert( 'Missing rights to access this Frame!' ) ;
                    
                if(oErrorNode.attributes.getNamedItem('javascript') != null)
                    eval(oErrorNode.attributes.getNamedItem('javascript').value);
                break ;
            default :
                alert( 'Error on your request. Error number: ' + iErrorNumber ) ;
                break ;
        }
    }
    return iErrorNumber ;
}

var oIcons = new Object() ;

oIcons.AvailableIconsArray = [ 
    'ai','avi','bmp','cs','dll','doc','exe','fla','gif','htm','html','jpg','js',
    'mdb','mp3','pdf','ppt','rdp','swf','swt','txt','vsd','xls','xml','zip' ] ;
    
oIcons.AvailableIcons = new Object() ;

for ( var i = 0 ; i < oIcons.AvailableIconsArray.length ; i++ )
    oIcons.AvailableIcons[ oIcons.AvailableIconsArray[i] ] = true ;

oIcons.GetIcon = function( fileName )
{
    var sExtension = fileName.substr( fileName.lastIndexOf('.') + 1 ).toLowerCase() ;
//  alert(sExtension);

    if ( this.AvailableIcons[ sExtension ] == true )
        return sExtension ;
    else
        return 'default.icon' ;
}
        </script>
    </head>
<?php

// make sure the User has the rights to upload a new Item.
$showUpload = has_permission(_BIGACE_FRIGHT_ADMIN_ITEMS);

?>  
    
    <frameset cols="150,*" class="Frame" framespacing="3" style="border-color:#f1f1e3" bordercolor="#f1f1e3" frameborder="yes">
        <frameset rows="50,*" framespacing="0">
            <frame src="frmresourcetype.html" scrolling="no" frameborder="no">
            <frame name="frmFolders" src="frmfolders.html" scrolling="auto" frameborder="yes">
        </frameset>
        <frameset id="extensions" rows="50,*<?php if ($showUpload) echo ',15'; ?>" framespacing="0" noresize>
            <frame name="frmActualFolder" src="frmactualfolder.html" scrolling="no" frameborder="no">
            <frame name="frmResourcesList" src="frmresourceslist.html" scrolling="auto" frameborder="1" border="1" style="border-top:3px;">
            <?php
            if($showUpload) {
                ?>
            <frameset name="uploadAll" cols="0,*,10" framespacing="0" frameborder="0" border="0" noresize>
                <frame name="frmCreateFolder" src="frmcreatefolder.html" scrolling="no" border="0"  frameborder="0" style="border-width:0px;">
                <frame name="frmUpload" src="frmupload.php" scrolling="no" border="0"  frameborder="0">
                <frame name="frmUploadWorker" src="blank.html" scrolling="no" border="0"  frameborder="0">
            </frameset>
                <?php
            }
            ?>
        </frameset>
    </frameset>
</html>
