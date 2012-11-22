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
 * This script is used for loading and handling the Plain Text Editor.
 */

import('classes.util.CMSLink');
import('classes.util.LinkHelper');
import('classes.util.links.ImageChooserLink');

showEditorHeader();

define('MARKITUP_DIR' , _BIGACE_DIR_ADDON_WEB.'markitup/');

$imageURL = get_image_dialog_settings($MENU->getID(), $MENU->getLanguageID());
$linkURL = get_link_dialog_settings($MENU->getID(), $MENU->getLanguageID());

?>
<link rel="stylesheet" type="text/css" href="<?php echo MARKITUP_DIR; ?>skins/bigace/style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo MARKITUP_DIR; ?>sets/bigace/style.css" />

<script src="<?php echo MARKITUP_DIR; ?>jquery.markitup.js" type="text/javascript"></script>
<script src="<?php echo MARKITUP_DIR; ?>sets/bigace/set.js" type="text/javascript"></script>

<script type="text/javascript">
<!--
	var isDirty = false;
	var lastServerBrowser = 1;

	$(document).ready(function()	{
		$('textarea').markItUp(mySettings);
	});

	function SetUrl(cmsUrl) {
		if(lastServerBrowser == 1) {
			$.markItUp({ openWith:'<a href="' + cmsUrl + '\" title="">', closeWith:'</a>' } );
		}
		else {
			$.markItUp({ replaceWith:'<img src="' + cmsUrl + '" alt="" />' } );
		}
	}

	function getProjectContent(contentName) {
		return $(contentName).value;
	}

	function getEditorContent() {
	    return getContentArea().value;
	}
	function setEditorContent(myHtml) {
	    getContentArea().value = myHtml;
	}
	function resetIsDirtyEditor() {
		isDirty = false;
	}
	function setIsDirtyEditor() {
	    isDirty = true;
		return true;
	}
	function isDirtyEditor() {
	    return isDirty;
	}
   	function getContentArea() {
	    return document.getElementById('<?php echo CONTENT_PARAMETER; ?>');
   	}
	function OpenServerBrowser( url, width, height )
	{
		var iLeft = (screen.width  - width) / 2 ;
		var iTop  = (screen.height - height) / 2 ;

		var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes" ;
		sOptions += ",width=" + width ;
		sOptions += ",height=" + height ;
		sOptions += ",left=" + iLeft ;
		sOptions += ",top=" + iTop ;

		var oWindow = window.open( url, "BrowseWindow", sOptions ) ;
		return oWindow;
	}
	function showImageDialog()
	{
		lastServerBrowser = 0;
	    OpenServerBrowser('<?php echo $imageURL['url']; ?>',
	    				  <?php echo $imageURL['width']; ?>,
	    				  <?php echo $imageURL['height']; ?>);
   	}
	function showLinkDialog()
	{
		lastServerBrowser = 1;
	    OpenServerBrowser('<?php echo $linkURL['url']; ?>',
	    				  <?php echo $linkURL['width']; ?>,
	    				  <?php echo $linkURL['height']; ?>);
   	}

// -->
</script>

<?php

$addContent = getContentToEdit($MENU);
$hasMultipleContents = (count($addContent) > 1);

foreach($addContent AS $contentPiece)
{
	echo '<h1 id="'.$contentPiece['param'].'Ref" class="contentTitle">&raquo; '.$contentPiece['title'].'</h1>';
	echo '<textarea onChange="return setIsDirtyEditor();" name="'.$contentPiece['param'].'" id="'.$contentPiece['param'].'" dir="ltr" class="contentBox">';
	echo htmlspecialchars($contentPiece['content']);
	echo"</textarea>";
}

showEditorFooter();
