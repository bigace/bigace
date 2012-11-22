<?php
require_once(realpath(dirname(__FILE__).'/../../system/libs/').'/init_session.inc.php');

import('classes.util.LinkHelper');

require_once(_BIGACE_DIR_EDITOR.'editor_properties.php');

?>

FCKConfig.ToolbarSets["News"] = [
	['Source','Preview'],
	['Cut','Copy','Paste','PasteText','PasteWord'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	'/',
	['OrderedList','UnorderedList'],
	['Link','Unlink','Anchor'],
	['Image','Flash','Table','SpecialChar'],
	['Style','FontFormat']
] ;

function setUrl(id,language,name) {
<?php
$tempLink = new CMSLink();
$tempLink->setCommand(_BIGACE_CMD_MENU);
$tempLink->setItemID('"+id+"');
$tempLink->setLanguageID('"+language+"');
echo '   SetUrl("'.LinkHelper::getUrlFromCMSLink($tempLink).'");  ';
?>

}

FCKConfig.FlashBrowser = false ;

<?php

$passID = (isset($_GET['parent']) ? $_GET['parent'] : null);
$passLang = (isset($_GET['language']) ? $_GET['language'] : null);
$imageURL = get_image_dialog_settings($passID,$passLang);
$linkURL = get_link_dialog_settings($passID,$passLang);

?>

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

FCKConfig.SpellChecker = 'ieSpell' ;	// 'ieSpell' | 'SpellerPages'
