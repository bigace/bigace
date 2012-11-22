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

define('FCK_DIR', 'FCKeditor');
define('FCK_BASE', _BIGACE_DIR_ADDON_WEB . FCK_DIR . '/');

if (!file_exists($EDITOR_DIRECTORY) || !file_exists(_BIGACE_DIR_ADDON.FCK_DIR.'/fckeditor.php'))
{
    // FCKEDITOR IS NOT INSTALLED
    echo '<p><b>The FCKEditor is not to installed on your system!</b></p>';
	return;
}

// ------------------ THE EDITOR ------------------
if ($mode == 'customConfig')
{
    include_once(_BIGACE_DIR_EDITOR.'fckeditor/javascript.php');
}
else
{
    require_once(_BIGACE_DIR_ADDON.FCK_DIR.'/fckeditor.php');

    showEditorHeader();

	$addContent = getContentToEdit($MENU);

    ?>
    <script type="text/javascript">
    <!--
    // receive the FCKeditor instance that is used to edit the content
    function FCKeditor_OnComplete( editorInstance ) {
		resetIsDirtyEditor();
	}
    function getFCKInstance(fckName) {
        return FCKeditorAPI.GetInstance(fckName);
    }
    
    // =================================================
    // Required methods for the BIGACE Editor Framework.
    // =================================================
    function getEditorContent() {
        return getFCKInstance('<?php echo CONTENT_PARAMETER; ?>').GetXHTML(false);
    }
    function setEditorContent(myHtml) {
        FCKeditorAPI.GetInstance('<?php echo CONTENT_PARAMETER; ?>').SetHTML(myHtml);
    }
    function isDirtyEditor() {
		<?php
		foreach($addContent AS $contentPiece) 
		{
			echo "if(getFCKInstance('".$contentPiece['param']."').IsDirty()) return true; \n";
		}
		?>
        return false;
    }
    function resetIsDirtyEditor() {
		<?php
		foreach($addContent AS $contentPiece) 
		{
			echo "getFCKInstance('".$contentPiece['param']."').ResetIsDirty(); \n";
		}
		?>
    }

    // -->
    </script>

	<?php
	
	$hasMultipleContents = (count($addContent) > 1);
	
	foreach($addContent AS $contentPiece) 
	{
		echo '<h1 id="'.$contentPiece['param'].'Ref" class="contentTitle">&raquo; '.$contentPiece['title'].'</h1>';
        $oFCKeditor = new FCKeditor($contentPiece['param']);
        $oFCKeditor->ToolbarSet         = 'Bigace';
        $oFCKeditor->Width              = '100%';
        $oFCKeditor->Height             = ($hasMultipleContents ? '403px' : '100%');
        $oFCKeditor->DefaultLanguage    = $LANGUAGE->getLocale();
        $oFCKeditor->DefaultLanguageID  = $LANGUAGE->getID();
        $oFCKeditor->Value              = $contentPiece['content'];
		$oFCKeditor->BasePath           = FCK_BASE;
        $oFCKeditor->Config             = array('CustomConfigurationsPath' => createEditorLink(EDITOR_FCKEDITOR, $MENU->getID(), $MENU->getLanguageID(), 'customConfig', array(), 'editor.js'));
        $oFCKeditor->Create();
	}

    showEditorFooter();
}
