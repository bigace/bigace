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
 * @package bigace.editor
 */

/**
 * This script is used for loading and handling the FCKEditor.
 */

import('classes.util.LinkHelper');
import('classes.util.links.ImageChooserLink');

$TINYMCE_PATH['NAME']   = 'tinymce';
$TINYMCE_PATH['BASE']   = _BIGACE_DIR_ADDON_WEB.$TINYMCE_PATH['NAME'] . '/';
$TINYMCE_PATH['PUBLIC'] = BIGACE_URL_ADDON.$TINYMCE_PATH['NAME'] . '/';

if (file_exists($EDITOR_DIRECTORY) && file_exists($GLOBALS['_BIGACE']['DIR']['addon'].$TINYMCE_PATH['NAME'].'/jscripts/tiny_mce/tiny_mce.js'))
{
        showEditorHeader();

        $menulinkDialogLink = new MenuChooserLink();
        $menulinkDialogLink->setCommand('application');
        $menulinkDialogLink->setItemID($MENU->getID());
        $menulinkDialogLink->setAction('util');
        $menulinkDialogLink->setSubAction('jstree');
        $menulinkDialogLink->setJavascriptCallback('setCmsUrl');

        $css = '';

        if(ConfigurationReader::getConfigurationValue('system', 'use.smarty', true)) {
            import('classes.smarty.SmartyDesign');
            $SMARTY_DESIGN = new SmartyDesign($MENU->getLayoutName());
            $SMARTY_STYLESHEET = $SMARTY_DESIGN->getStylesheet();
            $EDITOR_STYLESHEET = $SMARTY_STYLESHEET->getEditorStylesheet();
            $css = $EDITOR_STYLESHEET->getURL();
        }
        else {
        	$layout = new Layout($MENU->getLayoutName());
            $css = $layout->getSetting('CSS', '');
        }

        ?>
        <script language="javascript" type="text/javascript" src="<?php echo $TINYMCE_PATH['PUBLIC']; ?>jscripts/tiny_mce/tiny_mce.js"></script>
        <script language="javascript" type="text/javascript">
        <!--

        var lastPopup = null;
        var lastWin = null;
        var lastType = null;
        var lastFieldName = null;

        function getEditorContent() {
            return tinyMCE.getContent('<?php echo CONTENT_PARAMETER; ?>Id');
        }
        function setEditorContent(myHtml) {
            tinyMCE.setContent(myHtml);
        }
        function isDirtyEditor() {
            return (tinyMCE.getInstanceById('<?php echo CONTENT_PARAMETER; ?>Id')).isDirty();
        }
        function resetIsDirtyEditor() {
	        (tinyMCE.getInstanceById('<?php echo CONTENT_PARAMETER; ?>Id')).isNotDirty = 1; // Force not dirty state
        }

    	tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		plugins : "style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
//		plugins : "devkit,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
		theme_advanced_buttons1_add_before : "save,newdocument,separator",
		theme_advanced_buttons1_add : "fontselect,fontsizeselect",
		theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,separator,forecolor,backcolor",
		theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
		theme_advanced_buttons3_add_before : "tablecontrols,separator",
		theme_advanced_buttons3_add : "emotions,iespell,media,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_path_location : "bottom",
		content_css : "<?php echo $css; ?>",
	    plugin_insertdate_dateFormat : "%Y-%m-%d",
	    plugin_insertdate_timeFormat : "%H:%M:%S",
		extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
		//external_link_list_url : "example_link_list.js",
		//external_image_list_url : "example_image_list.js",
		//flash_external_list_url : "example_flash_list.js",
		//media_external_list_url : "example_media_list.js",
		//template_external_list_url : "example_template_list.js",
		file_browser_callback : "fileBrowserCallBack",
		theme_advanced_resize_horizontal : false,
		theme_advanced_resizing : true,
		nonbreaking_force_tab : true,
		apply_source_formatting : true,
		template_replace_values : {},
		relative_urls : false,
		remove_script_host : false,
	    });

		<?php

		$imageURL = get_image_dialog_settings($MENU->getID(), $MENU->getLanguageID());
		$linkURL = get_link_dialog_settings($MENU->getID(), $MENU->getLanguageID());

		?>
    	function fileBrowserCallBack(field_name, url, type, win) {
            var myUrl = '';
            var sizeX = 450;
            var sizeY = 760;

            if(type == 'image') {
                myUrl = '<?php echo $imageURL['url']; ?>';
                sizeY = <?php echo $imageURL['width']; ?>;
                sizeX = <?php echo $imageURL['height']; ?>;
            } else if(type == 'file') {
                myUrl = '<?php echo $linkURL['url']; ?>';
                sizeY = <?php echo $linkURL['width']; ?>;
                sizeX = <?php echo $linkURL['height']; ?>;
            }

            if(myUrl != '') {

                lastWin = win;
                lastFieldName = field_name;
                lastType = type;

                lastPopup = open (myUrl,"Browser"+type,"menubar=no,toolbar=no,statusbar=no,directories=no,location=no,scrollbars=yes,resizable=yes,height="+sizeX+",width="+sizeY+",screenX=0,screenY=0");bBreite=screen.width;bHoehe=screen.height;fenster.moveTo((bBreite-sizeY)/2,(bHoehe-sizeX)/2);
            }
            else {
	        	alert("Not supported by this implementation. Field_name: " + field_name + ", Url: " + url + ", Type: " + type + ", Win: " + win);
            }
	    }

        function SetUrl(tempUrl) {
	    	lastWin.document.forms[0].elements[lastFieldName].value = tempUrl;
            //if(lastType == 'image') {
            //    lastWin.showPreviewImage(tempUrl);
            //}
        }

        function setCmsUrl(id,language,name) {
            <?php
            $tempLink = new CMSLink();
            $tempLink->setCommand(_BIGACE_CMD_MENU);
            $tempLink->setItemID('"+id+"');
            $tempLink->setLanguageID('"+language+"');
            echo '   SetUrl("'.LinkHelper::getUrlFromCMSLink($tempLink).'");  ';
            ?>
        }

        // -->
        </script>
    	<textarea name="<?php echo CONTENT_PARAMETER; ?>" id="<?php echo CONTENT_PARAMETER; ?>Id" style="width: 100%; height: 100%;">
        <?php echo $content; ?>
        </textarea>
        <?php

        showEditorFooter();
}
else
{
    // EDITOR IS NOT INSTALLED
    echo '<p><b>The TinyMCE Editor seems not to be installed on your System!</b></p>';
}
