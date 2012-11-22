<?php
/*
* Displays a language dependend Sitemap with configurable amount of levels.
*
* Copyright (C) Kevin Papst.
*
* For further information go to http://www.bigace.de/
*
* @version $Id$
* @author Kevin Papst
* @package bigace.modul
*/

function createMenuRecurse($startMenu, $level, $max)
{
    if($level < $max)
    {
        $childs = $startMenu->getChilds();
        if($childs->count() > 0) {
            echo '<ul>';
            for($i=0; $i < $childs->count(); $i++)
            {
                $tempMenu = $childs->next();
                echo '<li><a title="' . $tempMenu->getName() . '" href="'.LinkHelper::getUrlFromCMSLink(LinkHelper::getCMSLinkFromItem($tempMenu)).'">' . $tempMenu->getName() . "</a>\n";
                createMenuRecurse($tempMenu, $level+1, $max);
                echo '</li>' . "\n";
            }
            echo '</ul>';
        }
    }
}

import('classes.menu.MenuService');
import('classes.modul.ModulService');
import('classes.modul.Modul');

loadLanguageFile('bigace');

$modul = new Modul($MENU->getModulID());
$modulService = new ModulService();
$config = $modulService->getModulProperties($MENU, $modul);

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

    echo '<div class="modulAdminLink" align="left"><a onClick="openAdmin(); return false;" href="'.LinkHelper::getUrlFromCMSLink($mdl).'"><img src="'._BIGACE_DIR_PUBLIC_WEB.'system/images/preferences.gif" border="0" align="top"> '.getTranslation('modul_admin').'</a></div>';
}

$startID 	= $config['sitemap2_startID'];
$depth 		= $config['sitemap2_menuDepth'];
$useCss     = $config['sitemap2_useCss'];

//$TEMP_MENU_RIGHT = $RIGHT_SERVICE->getMenuRight($GLOBALS['_BIGACE']['SESSION']->getUserID(), $startID);
//if (!$TEMP_MENU_RIGHT->canRead()) {
//    $startID = _BIGACE_TOP_LEVEL;
//}
$MENU_SERVICE = new MenuService();
$SITEMAP_MENU = $MENU_SERVICE->getMenu($startID, $MENU->getLanguageID());

/*
 * Use internal CSS definitions.
 */
if ($useCss)
{
	?>
    <link rel="stylesheet" href="<?php echo _BIGACE_DIR_PUBLIC_WEB .'cid'._CID_.'/'; ?>css/sitemap2.css" type="text/css" media="screen">
	<?php
}

?>

<div id="sitemap">
	<?php

		$childs1 = $SITEMAP_MENU->getChilds();
		for($i=0; $i < $childs1->count(); $i++) {
			$tempMenu1 = $childs1->next();
			echo '<h2><a title="'.$tempMenu1->getName().'" href="'.LinkHelper::getUrlFromCMSLink(LinkHelper::getCMSLinkFromItem($tempMenu1)).'">'.$tempMenu1->getName().'</a></h2>' . "\n";
			createMenuRecurse($tempMenu1, 1, $depth);
		}
	?>
</div>
