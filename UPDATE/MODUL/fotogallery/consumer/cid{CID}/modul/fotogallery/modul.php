<?php
/**
 * FOTOGALLERY
 *
 * The Fotoalbum displays all images of a choosen category.
 * The List displays thumbnails of the original images and opens a Javascript widget
 * on demand, showing the full size version.
 *
 * Copyright (C) Kevin Papst
 *
 * This script uses the Slimbox2. If you want to learn more, go to:
 * http://www.digitalia.be/software/slimbox2
 *
 * For further information go to:
 * http://wiki.bigace.de/bigace:extensions:modul:fotogallery
 *
 * @version $Id$
 * @author Kevin Papst
 */

function getGallery($CUR_CAT, $configs)
{
    $IMG_SERVICE    = new ImageService();
    $CAT_SERVICE    = new CategoryService();

    // ... and now fetch all linked images
    $search = $CAT_SERVICE->getItemsForCategory($IMG_SERVICE->getItemtype(), $CUR_CAT);

    if ($search->count() <= 0)
    {
        return '<b>'.getTranslation('gallery_empty').'</b>';
    }

    $allImages = array();
    $html = '';

    // ---------------------------------
    // sort images
    if($configs['fotogallery_sort_name'])
    {
        while ($search->hasNext()) {
            $temp = $search->next();
            $temp = $IMG_SERVICE->getClass($temp['itemid']);
            $allImages[$temp->getName()] = $temp;
        }
        if ($configs['fotogallery_sort_reverse']) {
            krsort($allImages, SORT_STRING);
        } else {
            ksort($allImages, SORT_STRING);
        }
    }
    else if($configs['fotogallery_sort_position'])
    {
        while ($search->hasNext()) {
            $temp = $search->next();
            $temp = $IMG_SERVICE->getClass($temp['itemid']);
            $allImages[$temp->getPosition()] = $temp;
        }
        if ($configs['fotogallery_sort_reverse']) {
            krsort($allImages, SORT_NUMERIC);
        } else {
            ksort($allImages, SORT_NUMERIC);
        }
    }
    else
    {
        while ($search->hasNext()) {
            $temp = $search->next();
            $temp = $IMG_SERVICE->getClass($temp['itemid']);
            $allImages[$temp->getID()] = $temp;
        }
        if ($configs['fotogallery_sort_reverse']) {
            krsort($allImages, SORT_NUMERIC);
        } else {
            ksort($allImages, SORT_NUMERIC);
        }
    }

    // ---------------------------------
    // render gallery
    $html .=  '<div id="'.$configs['fotogallery_css_id'].'">';

    foreach ($allImages AS $key => $temp_image)
    {
        $temp    = $search->next();
        $link    = LinkHelper::getCMSLinkFromItem($temp_image);
        $imgLink = LinkHelper::getUrlFromCMSLink($link);

        $thumbLink = new ThumbnailLink();
        $thumbLink->setHeight($configs['fotogallery_thumb_height_px']);
        $thumbLink->setItemID($temp_image->getID());
        $thumbLink->setLanguageID($temp_image->getLanguageID());
        $thumbUrl = LinkHelper::getUrlFromCMSLink($thumbLink);

        $html .= '
        <div class="thumbnail">
        <a class="imgLink" href="' . $imgLink . '" rel="lightbox-imggal" title="';

           if ($configs['fotogallery_show_description_popup']) {
               $html .=  $temp_image->getDescription();
           } else {
               $html .=  $temp_image->getName();
           }

        $html .= '"><img src="' . $thumbUrl . '" alt="' .$temp_image->getName() . '" height="'
               . $configs['fotogallery_thumb_height_px'] . '"></a>';

        if($configs['fotogallery_show_name'] || $configs['fotogallery_show_description'])
        {
            $html .= '<div class="caption">';
                if($configs['fotogallery_show_name']) {
                    $html .= '<a href="' . $imgLink . '" rel="lightbox-gal" class="thumnailLink" title="' . $temp_image->getName() . '">' . $temp_image->getName() . '</a>';
                }
                if($configs['fotogallery_show_description']) {
                    if ($configs['fotogallery_show_name']) {
                        $html .= '<br>';
                    }
                    $html .= $temp_image->getDescription();
                }
            $html .= '</div>';
        }
    $html .= '</div>';
    }
    $html .= '</div><br style="clear:both">';

    return $html;
}


// ----------------------------------------------------------
// Module code starts here
import('classes.util.LinkHelper');
import('classes.image.Image');
import('classes.image.ImageService');
import('classes.category.Category');
import('classes.category.CategoryService');
import('classes.modul.ModulService');
import('classes.util.ImageLink');
import('classes.util.links.ThumbnailLink');

define('LIGHTBOX_WEB',    _BIGACE_DIR_ADDON_WEB.'fotogallery/');
define('FOTOGALLERY_CSS', _BIGACE_DIR_PUBLIC_WEB .'cid'._CID_.'/css/fotogallery.css');

/**
 * Default values for every gallery.
 */
$defaultGallery = array(
    'fotogallery_show_name'              => true,
    'fotogallery_show_description'       => false,
    'fotogallery_show_description_popup' => true,
    'fotogallery_thumb_height_px'        => 100,
    'fotogallery_sort_position'          => false,
    'fotogallery_sort_name'              => false,
    'fotogallery_sort_reverse'           => false,
    'fotogallery_css_id'                 => 'fgallery',
    'fotogallery_shortcode'              => ''
);

loadLanguageFile("bigace");

$modul          = new Modul($MENU->getModulID());
$modService     = new ModulService();
$configs        = $modService->getModulProperties($MENU, $modul, $defaultGallery);
$CUR_CAT        = isset($configs['fotogallery_image_category']) ? $configs['fotogallery_image_category'] : null;

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

    echo '<div class="modulAdminLink" align="left"><a onClick="openAdmin(); return false;" href="'.LinkHelper::getUrlFromCMSLink($mdl).'">'.getTranslation('modul_admin').'</a></div>';
}


/* #########################################################################
 * ##################  Show List of all categorized Images  ################
 * #########################################################################
 */
if ($CUR_CAT === null)
{
    echo '<br><b>'.getTranslation('gallery_unconfigured').'</b><br>';
}
else
{

    // configured height of thumbnails
    if (!isset($configs['fotogallery_thumb_height_px']) || $configs['fotogallery_thumb_height_px'] < 1) {
        $configs['fotogallery_thumb_height_px'] = 100;
    }

    // show name of images?
    if (!isset($configs['fotogallery_show_name'])) {
        $configs['fotogallery_show_name'] = true;
    }

    // show description of images in listing
    if (!isset($configs['fotogallery_show_description'])) {
        $configs['fotogallery_show_description'] = false;
    }

    // show description of images in lightbox
    if (!isset($configs['fotogallery_show_description_popup'])) {
        $configs['fotogallery_show_description_popup'] = true;
    }

    // order by name
    if (!isset($configs['fotogallery_sort_name'])) {
        $configs['fotogallery_sort_name'] = true;
    }

    // order by position
    if (!isset($configs['fotogallery_sort_position'])) {
        $configs['fotogallery_sort_position'] = false;
    }

    // reverse order
    // order by position
    if (!isset($configs['fotogallery_sort_reverse'])) {
        $configs['fotogallery_sort_reverse'] = false;
    }


    ?>

    <link rel="stylesheet" href="<?php echo FOTOGALLERY_CSS; ?>" type="text/css" media="screen">
    <link rel="stylesheet" href="<?php echo LIGHTBOX_WEB; ?>css/slimbox2.css" type="text/css" media="screen">
    <script type="text/javascript" src="<?php echo _BIGACE_DIR_ADDON_WEB; ?>jquery/jquery.js"></script>
    <script type="text/javascript" src="<?php echo LIGHTBOX_WEB; ?>js/slimbox2.js"></script>

    <?php

    $gallery = getGallery($CUR_CAT, $configs);

    if (!empty($configs['fotogallery_shortcode']))
    {
        echo '<div>';
        echo str_replace($configs['fotogallery_shortcode'], $gallery, $MENU->getContent());
        echo '</div>';
    }
    else
    {
        echo '<div>';
        echo $MENU->getContent();
        echo '</div>';
        echo $gallery;
    }


}