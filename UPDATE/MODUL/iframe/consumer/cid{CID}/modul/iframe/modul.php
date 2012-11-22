<?php
/**
 * IFrame plugin.
 *
 * Copyright (C) Kevin Papst
 *
 * For further information go to http://www.bigace.de/
 *
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.modul
 */

import('classes.item.ItemProjectService');

define('IFRAME_PROJECT_TEXT_WIDTH',  'iframe_width');
define('IFRAME_PROJECT_TEXT_HEIGHT', 'iframe_height');
define('IFRAME_PROJECT_NUM_BORDER',  'iframe_border');
define('IFRAME_PROJECT_TEXT_URL',    'iframe_url');

$modul          = new Modul($MENU->getModulID());
$projectService = new ItemProjectService(_BIGACE_ITEM_MENU);

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

    echo '<div align="left"><a onClick="openAdmin(); return false;" href="'.LinkHelper::getUrlFromCMSLink($mdl).'"><img src="'.$GLOBALS['_BIGACE']['DIR']['public'].'system/images/preferences.gif" border="0" align="top"> '.$modul->translate('admin').'</a></div>';
}


/* #########################################################################
 * ##################  Show List of all categorized Images  ################
 * #########################################################################
 */
$configured = false;
if($projectService->existsProjectText($MENU->getID(), $MENU->getLanguageID(), IFRAME_PROJECT_TEXT_URL)) {
    // get iframe url
    $IFRAME_URL = $projectService->getProjectText($MENU->getID(), $MENU->getLanguageID(), IFRAME_PROJECT_TEXT_URL);
    if(strlen(trim($IFRAME_URL)) > 0) {
        if (stripos($IFRAME_URL, 'http://') !== 0) {
            $IFRAME_URL = 'http://' . $IFRAME_URL;
        }
        $configured = true;
    }
}

if(!$configured)
{
    echo '<br><b>'.$modul->translate('unconfigured').'</b><br>';
}
else
{
    echo $GLOBALS['MENU']->getContent();

    // Get configured height of thumbnails
    if($projectService->existsProjectText($MENU->getID(), $MENU->getLanguageID(), IFRAME_PROJECT_TEXT_HEIGHT)) {
        $iframeHeight = $projectService->getProjectText($MENU->getID(), $MENU->getLanguageID(), IFRAME_PROJECT_TEXT_HEIGHT);
    } else {
        $iframeHeight = "100%";
    }
    if($iframeHeight == 0) $iframeHeight = "100%";


    if($projectService->existsProjectText($MENU->getID(), $MENU->getLanguageID(), IFRAME_PROJECT_TEXT_WIDTH)) {
        $iframeWidth = $projectService->getProjectText($MENU->getID(), $MENU->getLanguageID(), IFRAME_PROJECT_TEXT_WIDTH);
    } else {
        $iframeWidth = "100%";
    }
    if($iframeWidth == 0) $iframeWidth = "100%";

    if($projectService->existsProjectNum($MENU->getID(), $MENU->getLanguageID(), IFRAME_PROJECT_NUM_BORDER)) {
        $iframeBorder = $projectService->getProjectNum($MENU->getID(), $MENU->getLanguageID(), IFRAME_PROJECT_NUM_BORDER);
    } else {
        $iframeBorder = 0;
    }

    echo '<iframe width="'.$iframeWidth.'" height="'.$iframeHeight.'" src="'.$IFRAME_URL.'" frameborder="'.(int)$iframeBorder.'"></iframe>';

}
