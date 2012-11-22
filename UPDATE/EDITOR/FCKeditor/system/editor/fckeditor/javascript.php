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
 * Custom FCKEDITOR Javascript for BIGACE implementation.
 */

// load fright service for toolbar separation
import('classes.layout.Layout');
import('classes.util.LinkHelper');

// define the available toolbar types
define('_TOOLBAR_MODE_MINIMUM',     'minimum');
define('_TOOLBAR_MODE_SIMPLE',      'simple');
define('_TOOLBAR_MODE_DEFAULT',     'default');
define('_TOOLBAR_MODE_EXPERT',      'expert');
define('_TOOLBAR_MODE_FULL',        'full');

// define the possible toolbar entrys
define('_TOOLBAR_SOURCECODE',       'SourceCode');
define('_TOOLBAR_SAVE',             'Save');
define('_TOOLBAR_UNDOREDO',         'UndoRedo');
define('_TOOLBAR_SIMPLEFORMAT',     'SimpleFormat');
define('_TOOLBAR_ADVANCEDFORMAT',   'AdvancedFont');
define('_TOOLBAR_COPYPASTE',        'CopyAndPaste');
define('_TOOLBAR_LOADPREVIEW',      'LoadAndPreview');
define('_TOOLBAR_LISTS',            'ListItems');
define('_TOOLBAR_ALIGN',            'Alignment');
define('_TOOLBAR_LINKS',            'LinkManagement');
define('_TOOLBAR_IMAGE',            'Image');
define('_TOOLBAR_ADVANCEDEDITING',  'AdvancedEditing');
define('_TOOLBAR_FORMS',            'Formulars');
define('_TOOLBAR_COLORS',           'Colors');
define('_TOOLBAR_ABOUT',            'HelpAbout');
define('_TOOLBAR_SPECIALS',         'SpecialSigns');
define('_TOOLBAR_SIMPLEFONT',       'SimpleFont');
define('_TOOLBAR_SPLITTER',         '/');


class FckeditorToolBar
{
    /**
     * @access private
     */
    var $entrys = array();
    /**
     * @access private
     */
    var $fright = '';

    /**
     * Pass all ToolBar Entrys, either as Array or as a list of arguments.
     */
    function FckeditorToolBar($toolbarEntrys) {
        if (is_array($toolbarEntrys)) {
            $this->entrys = $toolbarEntrys;
        } else {
            $arg_list   = func_get_args();
            $numargs    = func_num_args();
            for ($i = 0; $i < $numargs; $i++) {
                if(is_string($arg_list[$i]))
                    array_push($this->entrys, $arg_list[$i]);
            }
        }
    }

    function getEntrys() {
        return $this->entrys;
    }

    function setFrightProtection($frightString) {
        if (is_string($frightString)) {
            $this->fright = $frightString;
        }
    }

    function isFrightProtected() {
        return ($this->fright != '');
    }

    function getFrightToCheck() {
        return $this->fright;
    }

}


// ------------------------------------------------------------


// define the toolbar that will be used
$toolbarmode = ConfigurationReader::getConfigurationValue('editor', 'fckeditor.toolbar');

$MENU_SERVICE = new MenuService();
$ITEM = $MENU_SERVICE->getMenu($MENU->getID(), $MENU->getLanguageID());

$css = '';
$fckstyles = '';
$fcktemplates = '';

if(ConfigurationReader::getConfigurationValue('system', 'use.smarty', true)) {
    import('classes.smarty.SmartyDesign');
    $SMARTY_DESIGN = new SmartyDesign($ITEM->getLayoutName());
    $SMARTY_STYLESHEET = $SMARTY_DESIGN->getStylesheet();
    $EDITOR_STYLESHEET = $SMARTY_STYLESHEET->getEditorStylesheet();
    //$css = $SMARTY_STYLESHEET->getURL();
    $css = $EDITOR_STYLESHEET->getURL();

    $path_parts1 = pathinfo($EDITOR_STYLESHEET->getFullFilename());
    $path_parts2 = pathinfo($EDITOR_STYLESHEET->getURL());
    $pathes = array(
        _BIGACE_DIR_PUBLIC.'cid'._CID_.'/editor' => _BIGACE_DIR_PUBLIC_WEB.'cid'._CID_.'/editor',
        $path_parts1["dirname"]                  => $path_parts2["dirname"]
    );

    foreach($pathes as $pPath => $pUrl) {
        if(file_exists($pPath . '/templates.xml')) {
            $fcktemplates = $pUrl . '/templates.xml';
        }
        if(file_exists($pPath . '/fcktemplates.xml')) {
            $fcktemplates = $pUrl . '/fcktemplates.xml';
        }
        if(file_exists($pPath . '/fckstyles.xml')) {
            $fckstyles = $pUrl . '/fckstyles.xml';
        }
    }
}
else {
	$layout 		= new Layout($ITEM->getLayoutName());
    $css            = $layout->getSetting('CSS', '');
    $fckstyles      = $layout->getSetting('fckstyles', '');
    $fcktemplates   = $layout->getSetting('fcktemplates', '');
}

$sourceCode     = new FckeditorToolBar('Source');
$sourceCode->setFrightProtection('editor.html.sourcecode');

$save           = new FckeditorToolBar('SaveToBigace');
//$loadAndPreview = new FckeditorToolBar('LoadFromBigace','-','NewPage','Preview','-', 'Templates'); // 'DocProps'
$loadAndPreview = new FckeditorToolBar('-','NewPage','Preview','-', 'Templates');
$copyAndPaste   = new FckeditorToolBar('Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck');
$undRedo        = new FckeditorToolBar('Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat');
$simpleFormat   = new FckeditorToolBar('Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript');
$listItems      = new FckeditorToolBar('OrderedList','UnorderedList','-','Outdent','Indent');
$alignment      = new FckeditorToolBar('JustifyLeft','JustifyCenter','JustifyRight','JustifyFull');
$linkManagement = new FckeditorToolBar('Link','Unlink','Anchor');
$image          = new FckeditorToolBar('Image');
$advancedHtml   = new FckeditorToolBar('Table','Rule', 'Flash');
$specialHtml    = new FckeditorToolBar('Smiley','SpecialChar','PageBreak'); // removed with 2.3.2 'UniversalKey'
$formulars      = new FckeditorToolBar('Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField');
$simpleFont     = new FckeditorToolBar('FontName','FontSize');
$advancedFont   = new FckeditorToolBar('Style','FontFormat');
$colors         = new FckeditorToolBar('TextColor','BGColor');
$helpAbout      = new FckeditorToolBar('FitWindow'); // do not show about, cause of dirty donation questions... 'About'
$splitter       = new FckeditorToolBar( array() );


if($toolbarmode == _TOOLBAR_MODE_MINIMUM)
{
    $toolbarItemsToLoad = array(
//        _TOOLBAR_SAVE               => $save,
        _TOOLBAR_UNDOREDO           => $undRedo,
        _TOOLBAR_SIMPLEFORMAT       => $simpleFormat,
    );
}
else if($toolbarmode == _TOOLBAR_MODE_SIMPLE)
{
    $toolbarItemsToLoad = array(
//        _TOOLBAR_SAVE               => $save,
        _TOOLBAR_UNDOREDO           => $undRedo,
        _TOOLBAR_SIMPLEFORMAT       => $simpleFormat,
        _TOOLBAR_SPLITTER           => $splitter,
        _TOOLBAR_ADVANCEDFORMAT     => $advancedFont,
    );
}
else if($toolbarmode == _TOOLBAR_MODE_DEFAULT)
{
    $toolbarItemsToLoad = array(
//        _TOOLBAR_SAVE               => $save,
        _TOOLBAR_COPYPASTE          => $copyAndPaste,
        _TOOLBAR_UNDOREDO           => $undRedo,
        _TOOLBAR_SIMPLEFORMAT       => $simpleFormat,
        _TOOLBAR_LISTS              => $listItems,
        _TOOLBAR_ALIGN              => $alignment,
        _TOOLBAR_LINKS              => $linkManagement,
        _TOOLBAR_IMAGE              => $image,
        _TOOLBAR_ADVANCEDEDITING    => $advancedHtml,
        _TOOLBAR_SPLITTER           => $splitter,
        _TOOLBAR_ADVANCEDFORMAT     => $advancedFont,
    );
}
else if($toolbarmode == _TOOLBAR_MODE_EXPERT)
{
    $toolbarItemsToLoad = array(
        _TOOLBAR_SOURCECODE         => $sourceCode,
//        _TOOLBAR_SAVE               => $save,
        _TOOLBAR_LOADPREVIEW        => $loadAndPreview,
        _TOOLBAR_COPYPASTE          => $copyAndPaste,
        _TOOLBAR_UNDOREDO           => $undRedo,
        _TOOLBAR_SIMPLEFORMAT       => $simpleFormat,
        _TOOLBAR_LISTS              => $listItems,
        _TOOLBAR_ALIGN              => $alignment,
        _TOOLBAR_LINKS              => $linkManagement,
        _TOOLBAR_IMAGE              => $image,
        _TOOLBAR_ADVANCEDEDITING    => $advancedHtml,
        _TOOLBAR_FORMS              => $formulars,
        _TOOLBAR_SPLITTER           => $splitter,
        _TOOLBAR_ADVANCEDFORMAT     => $advancedFont
    );
}
else if($toolbarmode == _TOOLBAR_MODE_FULL)
{
    // all available toolbar items
    $toolbarItemsToLoad = array(
        _TOOLBAR_SOURCECODE         => $sourceCode,
//        _TOOLBAR_SAVE               => $save,
        _TOOLBAR_LOADPREVIEW        => $loadAndPreview,
        _TOOLBAR_COPYPASTE          => $copyAndPaste,
        _TOOLBAR_UNDOREDO           => $undRedo,
        _TOOLBAR_SIMPLEFORMAT       => $simpleFormat,
        _TOOLBAR_LISTS              => $listItems,
        _TOOLBAR_ALIGN              => $alignment,
        _TOOLBAR_LINKS              => $linkManagement,
        _TOOLBAR_IMAGE              => $image,
        _TOOLBAR_ADVANCEDEDITING    => $advancedHtml,
        _TOOLBAR_SPECIALS           => $specialHtml, // missing for experts
        _TOOLBAR_FORMS              => $formulars,
        _TOOLBAR_SPLITTER           => $splitter,
        _TOOLBAR_SIMPLEFONT         => $simpleFont, // missing for experts
        _TOOLBAR_ADVANCEDFORMAT     => $advancedFont,
        _TOOLBAR_COLORS             => $colors, // missing for experts
        _TOOLBAR_ABOUT              => $helpAbout // missing for experts
    );
}
else
{
    // the default toolbar if a misconfiguration is applied
    $toolbarItemsToLoad = array(
        _TOOLBAR_SOURCECODE         => $sourceCode,
//        _TOOLBAR_SAVE               => $save,
        _TOOLBAR_LOADPREVIEW        => $loadAndPreview,
        _TOOLBAR_COPYPASTE          => $copyAndPaste,
        _TOOLBAR_UNDOREDO           => $undRedo,
        _TOOLBAR_SIMPLEFORMAT       => $simpleFormat,
        _TOOLBAR_LISTS              => $listItems,
        _TOOLBAR_ALIGN              => $alignment,
        _TOOLBAR_LINKS              => $linkManagement,
        _TOOLBAR_IMAGE              => $image,
        _TOOLBAR_SPLITTER           => $splitter,
        _TOOLBAR_ADVANCEDFORMAT     => $advancedFont
    );
}

?>

FCKConfig.ToolbarSets["Bigace"] = [
    <?php
    $i = 0;
    foreach($toolbarItemsToLoad AS $toolbarName => $toolbar)
    {
        $showToolbar = TRUE;
        if ($toolbar->isFrightProtected()) {
            $showToolbar = has_user_permission($GLOBALS['_BIGACE']['SESSION']->getUserID(), $toolbar->getFrightToCheck());
        }

        if($showToolbar)
        {
            $toolbarItems = $toolbar->getEntrys();

            if ($toolbarName == _TOOLBAR_SPLITTER || count($toolbarItems) > 0)
            {
                if ($toolbarName == _TOOLBAR_SPLITTER)
                {
                    echo "'/'";
                }
                else if (count($toolbarItems) > 0)
                {
                    echo "[";
                    for($a=0; $a < count($toolbarItems); $a++) {
                        $itemName = $toolbarItems[$a];
                        echo "'".$itemName."'";
                        if ($a < count($toolbarItems)-1)
                            echo ',';
                    }
                    echo "]";
                }

                if($i < count($toolbarItemsToLoad)-1)
                    echo ",";

                echo "\n";
            }
        }

        $i++;
    }
    unset($i);
    ?>
] ;

FCKConfig.ContextMenu = ['Generic','Link','Anchor','Image','Select','Textarea','Checkbox','Radio','TextField','HiddenField','ImageButton','Button','BulletedList','NumberedList','TableCell','Table','Form'] ; // ,'Flash'

//FCKConfig.Plugins.Add('LoadFromBigace', 'en,de');
//FCKConfig.Plugins.Add('SaveToBigace', 'en,de');

if (config == null) {
    var config = new Array();
}
//FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/office2003/' ;
//FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/silver/' ;
FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/default/' ;

FCKConfig.BaseHref = '<?php echo $GLOBALS['_BIGACE']['DOMAIN']; ?>' ;

FCKConfig.ContentLangDirection  = 'ltr' ;
FCKConfig.UseBROnCarriageReturn = false;

FCKConfig.EnableXHTML = true ;
FCKConfig.EnableSourceXHTML = true ;

<?php /* see http://dev.bigace.org/jira/browse/BIGACE-50 */ ?>
FCKConfig.ProcessNumericEntities = false;
FCKConfig.ProcessHTMLEntities = false;
FCKConfig.IncludeLatinEntities = false;
FCKConfig.IncludeGreekEntities = false;

FCKConfig.AutoDetectLanguage = false ;
FCKConfig.DefaultLanguage    = "<?php echo _ULC_; ?>" ;

<?php
if($css != '')
echo "\n" . "FCKConfig.EditorAreaCSS       = '".$css."';" ."\n";

if($fckstyles != '')
echo "\n" . "FCKConfig.StylesXmlPath       = '".$fckstyles."';" ."\n";

if($fcktemplates != '')
echo "\n" . "FCKConfig.TemplatesXmlPath    = '".$fcktemplates."';" ."\n";

?>

function setCmsUrl(id,language,name) {
<?php
$tempLink = new CMSLink();
$tempLink->setCommand(_BIGACE_CMD_MENU);
$tempLink->setItemID('"+id+"');
$tempLink->setLanguageID('"+language+"');
echo '   SetUrl("'.LinkHelper::getUrlFromCMSLink($tempLink).'");  ';
?>

}
<?php

$imageURL = get_image_dialog_settings($MENU->getID(), $MENU->getLanguageID());
$linkURL = get_link_dialog_settings($MENU->getID(), $MENU->getLanguageID());

?>
FCKConfig.FlashBrowser = false ;

FCKConfig.ImageBrowser = true ;
FCKConfig.ImageBrowserURL = '<?php echo $imageURL['url']; ?>';
FCKConfig.ImageBrowserWindowWidth  = <?php echo $imageURL['width']; ?>;
FCKConfig.ImageBrowserWindowHeight = <?php echo $imageURL['height']; ?>;

FCKConfig.LinkBrowser = true ;
FCKConfig.LinkBrowserURL = '<?php echo $linkURL['url']; ?>';
FCKConfig.LinkBrowserWindowWidth    = <?php echo $linkURL['width']; ?>;
FCKConfig.LinkBrowserWindowHeight   = <?php echo $linkURL['height']; ?>;

FCKConfig.ImageUpload = false ;
FCKConfig.LinkUpload = false ;
FCKConfig.FlashUpload = false ;

FCKConfig.SpellChecker			= 'ieSpell' ;	// 'ieSpell' | 'SpellerPages'
