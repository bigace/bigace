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
 * @version $Id$
 * @author Kevin Papst
 * @package bigace.addon.filemanager
 */

/*
 * Inspired by a file from:
 * FCKeditor - (C) Frederico Caldeira Knabben
 * ----------------------------------------------------------------------
 * Customized by Kevin Papst for BIGACE.
 */

require_once(dirname(__FILE__).'/environment.php');
if(!defined('_BIGACE_FILEMANAGER')) die('An error occured.');

import('classes.util.links.AjaxItemInfoLink');

define('JS_FUNCTION_URL', (isset($_GET['jsFunc']) ? $_GET['jsFunc'] : 'SetUrl'));
define('JS_FUNCTION_INFOS', (isset($_GET['imgInfos']) ? $_GET['imgInfos'] : 'SetImageInfos'));

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
    <head>
        <title>BIGACE <?php echo _BIGACE_ID; ?> | <?php echo getTranslation('filemanager_title'); ?></title>
        <script type="text/javascript" src="<?php echo _BIGACE_DIR_PUBLIC_WEB; ?>system/javascript/ajax_xml.js"></script>
        <script type="text/javascript" src="<?php echo _BIGACE_DIR_PUBLIC_WEB; ?>system/javascript/bigace_ajax.js"></script>
        <script type="text/javascript">
        var oIcons = new Object() ;
        oIcons.AvailableIcons = new Object() ;

        oIcons.AvailableIconsArray = [
            'ai','avi','bmp','cs','dll','doc','exe','fla','gif','htm','html','jpeg','jpg','js',
            'mdb','mp3','pdf','ppt','rdp','swf','swt','txt','vsd','xls','xml','zip' ] ;


        for ( var i = 0 ; i < oIcons.AvailableIconsArray.length ; i++ )
            oIcons.AvailableIcons[ oIcons.AvailableIconsArray[i] ] = true ;

        function GetUrlParam( paramName )
        {
            var oRegex = new RegExp( '[\?&]' + paramName + '=([^&]+)', 'i' ) ;
            var oMatch = oRegex.exec( window.top.location.search ) ;

            if ( oMatch && oMatch.length > 1 )
                return oMatch[1] ;
            else
                return '' ;
        }

        function getItem(itemtype, itemid, languageid)
        {
            <?php
                $link = new AjaxItemInfoLink();
                $link->setItemID('"+itemid+"');
                $link->setLanguageID('"+languageid+"');
                $link->setUseSSL(BIGACE_USE_SSL);
            ?>
            var itemRequestUrl = "<?php echo LinkHelper::getUrlFromCMSLink($link, array('itemtype' => '"+itemtype+"')); ?>";
            return loadItem(itemRequestUrl, false);
        }

        var sActiveItem = {
            itemID          : null,
            languageID      : null,
            name            : '',
            filename        : '',
            url             : '',
            setFilename     : function (name) { this.filename = name; },
            getFilename     : function()    { return this.filename; },
            setItemID       : function (id) { this.itemID = id; },
            getItemID       : function()    { return this.itemID; },
            setLanguageID   : function (id) { this.languageID = id; },
            getLanguageID   : function()    { return this.languageID; },
            setURL          : function (url) { this.url = url; },
            getURL          : function()    { return this.url; }
        }

        function chooseAndClose(url,itemid,filename)
        {
            if (typeof(window.top.opener.<?php echo JS_FUNCTION_URL; ?>) == "undefined")
            {
                alert('<?php getTranslation('undefined_js_function'); ?>: "<?php echo JS_FUNCTION_URL; ?>"');
            }
            else
            {
                window.top.opener.<?php echo JS_FUNCTION_URL; ?>(encodeURI(url));
                if (typeof(window.top.opener.<?php echo JS_FUNCTION_INFOS; ?>) != "undefined")
                    window.top.opener.<?php echo JS_FUNCTION_INFOS; ?>(itemid, filename);
                window.top.close();
                window.top.opener.focus();
            }
        }

        function acceptItem()
        {
            if(sActiveItem.getURL() == null || sActiveItem.getURL() == '') {
                alert('<?php getTranslation('no_image_selected'); ?>.');
                return false;
            }
            chooseAndClose(sActiveItem.getURL(), sActiveItem.getItemID(), sActiveItem.getFilename());
        }

        function getExtension(mimetype) {
            mimetype = mimetype.toLowerCase();
            return oIcons.GetIcon("."+mimetype);
        }

        function setSelectedItem(itemtype, itemid, languageid, filename, itemUrl, mimetype)
        {
            sActiveItem.setItemID(itemid);
            sActiveItem.setLanguageID(languageid);
            sActiveItem.setFilename(filename);
            sActiveItem.setURL(itemUrl);
        }

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
$contentUrl = 'startscreen.php';
if ($itemtype != null) {
    $contentUrl = 'by_itemtype.php';
    $parameter .= '&itemtype='.$itemtype;
    if(defined('GALLERY_PARENT') && $itemtype != _BIGACE_ITEM_MENU) {
        $contentUrl = 'by_parent.php';
    }
}

?>
    <frameset cols="160,*" framespacing="0" style="border-color:#f1f1e3" bordercolor="#f1f1e3" frameborder="yes">
            <frame name="folders" src="folder.php?<?php echo $parameter; ?>" scrolling="auto" frameborder="yes">
            <frame name="main" src="<?php echo $contentUrl . '?' . $parameter; ?>" scrolling="auto" frameborder="1" border="1">
    </frameset>
</html>