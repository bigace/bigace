<?php
/**
* FOTOALBUM
* The Fotoalbum displays all Images of a choosen Category in a List.
* The List shows a resized version of the original Image on the left and
* the description on the right.
*
* This script needs at last one CSS entry like this:
*
* #fotoalbumEntry { margin-bottom:10px;border:1px dashed #cccccc;width:100%; }
*
* Copyright (C) Kevin Papst
*
* For further information go to http://www.bigace.de/
*
* @version $Id$
* @author Kevin Papst
* @package bigace.modul
*/

import('classes.image.ImageService');
import('classes.image.Image');
import('classes.category.Category');
import('classes.category.CategoryService');
import('classes.item.ItemProjectService');
import('classes.util.links.ThumbnailLink');

/**
* Configuration array for the Picture Gallery
*/
$_FOTOALBUM = array('PRE_WIDTH' => '150');

define('MODUL_ALBUM_IMG_CATEGORY', 'photoalbum_image_category');
define('MODUL_ALBUM_THUMB_HEIGHT', 'photoalbum_thumb_height_px');

$modul          = new Modul($MENU->getModulID());
$imgService    = new ImageService();
$projectService = new ItemProjectService(_BIGACE_ITEM_MENU);
$curCategory        = null;

/* #########################################################################
 * ############################  Show Admin Link  ##########################
 * #########################################################################
 */
if ($modul->isModulAdmin())
{
    import('classes.util.links.ModulAdminLink');
    import('classes.util.LinkHelper');
    $mdl = new ModulAdminLink();
    $mdl->setItemID($MENU->getID());
    $mdl->setLanguageID($MENU->getLanguageID());

    ?>
    <script type="text/javascript">
    <!--
    function openAdmin()
    {
        fenster = open("<?php echo LinkHelper::getUrlFromCMSLink($mdl); ?>","ModulAdmin","menubar=no,toolbar=no,statusbar=no,directories=no,location=no,scrollbars=yes,resizable=no,height=350,width=400,screenX=0,screenY=0");
        bBreite=screen.width;
        bHoehe=screen.height;
        fenster.moveTo((bBreite-400)/2,(bHoehe-350)/2);
    }
    // -->
    </script>
    <?php

    echo '<div align="left"><a onClick="openAdmin(); return false;" href="'.LinkHelper::getUrlFromCMSLink($mdl).'"><img src="'._BIGACE_DIR_PUBLIC_WEB.'system/images/preferences.gif" border="0" align="top"> '.getTranslation('album_admin').'</a></div>';
}

/* #########################################################################
 * ###########################  CHECK CONFIGURATION  #######################
 * #########################################################################
 */
if ($projectService->existsProjectNum($MENU->getID(), $MENU->getLanguageID(), MODUL_ALBUM_IMG_CATEGORY)) {
    $curCategory = $projectService->getProjectNum($MENU->getID(), $MENU->getLanguageID(), MODUL_ALBUM_IMG_CATEGORY);
	if ($projectService->existsProjectNum($MENU->getID(), $MENU->getLanguageID(), MODUL_ALBUM_THUMB_HEIGHT)) {
		$temp = $projectService->getProjectNum($MENU->getID(), $MENU->getLanguageID(), MODUL_ALBUM_THUMB_HEIGHT);
		if ($temp != 0) {
    		$_FOTOALBUM['PRE_WIDTH'] = $temp;
		}
	}
} else {
    echo '<br><b>'.getTranslation('album_unconfigured').'</b><br>';
}

/* #########################################################################
 * ###########################  Show selected image  #######################
 * #########################################################################
 */
if (isset($_GET['imageid'])) {
    $tempImage = $imgService->getClass(intval($_GET['imageid']));

	$imgLink = LinkHelper::itemUrl($tempImage);
    $url     = LinkHelper::itemUrl($GLOBALS['MENU']) . '#foto'.$tempImage->getID();

    echo '<div class="fotoEntrySingle">';
    echo '<h1>'.$tempImage->getName().'</h1>';
    echo '<div class="fotoImg"><a href="'.$imgLink.'" title="'.$tempImage->getName().'"><img src="'.$imgLink.'" alt="'.$tempImage->getName().'"></a></div>';
    echo '<div class="fotoDesc">'.nl2br($tempImage->getDescription()).'</div>';
    echo '<div class="fotoBacklink"><a href="'.$url.'" title="'.getTranslation('fotoalbum_showlist').'">'.getTranslation('fotoalbum_showlist').'</a></div>';
    echo '</div>';
}


/* #########################################################################
 * ##################  Show List of all categorized Images  ################
 * #########################################################################
 */
if (!isset($_GET['imageid']) && $curCategory !== null) {
    $catService = new CategoryService();
    $search      = $catService->getItemsForCategory($imgService->getItemtype(), $curCategory);

    ?>

    <p><?php echo $GLOBALS['MENU']->getContent(); ?></p>

    <?php

    if ($search->count() > 0) {
        while ($search->hasNext()) {
            $temp = $search->next();
            $image = $imgService->getClass($temp['itemid']);
            $url = LinkHelper::itemUrl($GLOBALS['MENU'], array('imageid' => $image->getID()));

            ?>
            <div class="fotoEntry" id="foto<?php echo $image->getID(); ?>">
            <table width="100%" cellpadding="2" cellspacing="2" align="center">
                <tr>
                    <td width="<?php echo $_FOTOALBUM['PRE_WIDTH']; ?>" align="center" valign="top">
                        <a href="<?php echo $url; ?>"><img src="<?php
                        	$imgLink = new ThumbnailLink();
                        	$imgLink->setWidth($_FOTOALBUM['PRE_WIDTH']);
                        	$imgLink->setItemID($image->getID());
                        	echo  LinkHelper::getUrlFromCMSLink($imgLink);
                        ?>" border="0" alt="<?php echo htmlspecialchars($image->getDescription()); ?>" width="<?php echo $_FOTOALBUM['PRE_WIDTH']; ?>"></a>
                    </td>
                    <td valign="top">
                        <p class="fotoTitle">
                            <a href="<?php echo $url; ?>" title="<?php echo getTranslation('fotoalbum_showpicture'); ?>"><?php echo $image->getName(); ?></a>
                        </p>
                        <div class="fotoDescription">
                        <?php echo nl2br($image->getDescription()); ?>
                        </div>
                    </td>
                </tr>
            </table>
            </div>
            <?php
        }
    }
    else
    {
        echo '<b>'.getTranslation('album_empty').'</b>';
    }
} // ($mode == $_FOTOALBUM['MODE_LIST'])
