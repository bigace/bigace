<?php
/*
* Displays a Preview of all SubMenus
*
* Copyright (C) Kevin Papst.
*
* For further information go to http://www.bigace.de/
*
* @version $Id$
* @author Kevin Papst
* @package bigace.modul
*/

import('classes.modul.ModulService');
import('classes.modul.Modul');
import('classes.menu.MenuService');
import('classes.util.LinkHelper');

loadLanguageFile("bigace");

// fetch config
$config = array(
	'show_hidden'	 => false,
	'content_top'	 => true,
	'content_bottom' => false,
    'order'          => false
);
$modulService = new ModulService();
$modul = new Modul($GLOBALS['MENU']->getModulID());
$config = $modulService->getModulProperties($MENU, $modul, $config);



/* #########################################################################
 * ############################  Show Admin Link  ##########################
 * #########################################################################
 */
if ($modul->isModulAdmin())
{
    import('classes.util.links.ModulAdminLink');
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

// display content below submenus?!?
if($config['content_top'] && isset($GLOBALS['MENU']))
{
    echo $GLOBALS['MENU']->getContent();
}

    echo '<div id="preview">';

	$ir = new ItemRequest(_BIGACE_ITEM_MENU,$GLOBALS['MENU']->getID());
	$ir->setLanguageID($GLOBALS['MENU']->getLanguageID());
	if($config['show_hidden'])	{
		$ir->setFlagToExclude($ir->FLAG_ALL_EXCEPT_TRASH);
	}
    if($config['order'])  {
        $ir->setOrder($ir->_ORDER_DESC);
    }
	//$ir->setOrderBy($params['orderby']);
	//$ir->setOrder($params['order']);

	$menus = new SimpleItemTreeWalker($ir);

    for ($i=0; $i < $menus->count(); $i++)
    {
        $temp_menu = $menus->next();
        $url = LinkHelper::getUrlFromCMSLink( LinkHelper::getCMSLinkFromItem($temp_menu) );
        echo '<div class="nextLevelPreview">';
        echo '<h2><a href="'.$url.'" title="'.$temp_menu->getName().'">';
        echo $temp_menu->getName();
        echo '</a></h2><p>';

        if( strlen(trim($temp_menu->getDescription())) > 0 )
        	echo stripcslashes($temp_menu->getDescription());
        else
        	echo getTranslation('empty_description');

        echo ' <a href="'.$url.'" title="'.getTranslation('list_link_title').$temp_menu->getName().'"><img border="0" src="'._BIGACE_DIR_PUBLIC_WEB.'modul/images/3arrows.gif" alt="'.getTranslation('list_img_alt').'"></a>';
        echo '</p></div>';
    }

	echo '</div>';

// display content below submenus?!?
if($config['content_bottom'] && isset($GLOBALS['MENU']))
{
    echo $GLOBALS['MENU']->getContent();
}

?>