<?php
/**
* BIGACE DESIGN 2
*
* Copyright (C) Kevin Papst. 
*
* For further information go to http://www.bigace.de/ 
*
* @version $Id$
* @author Kevin Papst 
*/

$INCLUDE_SUB_DIR = 'bigacedesign2/';

define('DESIGN_INC_DIR', _BIGACE_DIR_CID . 'include/bigacedesign2/');

// needed for menu creation
loadClass('item', 'ItemTreeWalker');

require_once(DESIGN_INC_DIR.'environment.php');
loadClass('util', 'applications');

// ---- [start] get Portlets for this Page ----
$portlets = array();
$services = ServiceFactory::get();
$portletService = $services->getService('portlet');
$portlets['left'] = $portletService->getPortlets(_BIGACE_ITEM_MENU, $MENU->getID(), $MENU->getLanguageID(), 'left');
$portlets['right'] = $portletService->getPortlets(_BIGACE_ITEM_MENU, $MENU->getID(), $MENU->getLanguageID(), 'right');
unset($portletService);
unset($services);
// ---- [stop] get Portlets for this Page ----

$SELECTED_MENUID = $MENU->getID();
$APPS = new applications();
$APPS->setShowPicture(true);

$APPS->hide($APPS->STATUS);
$APPS->hide($APPS->HOME);

$APPS->setAddPreDelim(false);
$APPS->setAddPostDelim(false);

if (!$USER_MENU_RIGHT->canWrite()) {
    $APPS->hide($APPS->EDITOR);
}

?>
<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <?php include(DESIGN_INC_DIR.'htmlhead.php'); ?>
    <?php
    foreach($portlets AS $column)
    {
        foreach($column AS $currentPortlet) {
            if ($currentPortlet->needsJavascript()) {
                echo $currentPortlet->getJavascript() . "\n";
            }
        }
    }
    ?>
</head>
<body>
    <?php include(DESIGN_INC_DIR.'topmenu.php'); ?>
    <table class="mainTable" summary="Design Tabelle" cellspacing="0">
        <tr>
            <td class="leftMenu">
                <?php 
                include(DESIGN_INC_DIR.'pagetools.php');
                
                include(DESIGN_INC_DIR.'leftnavi.php');
                
                foreach($portlets['left'] AS $currentPortlet)
                {
                    echo '<h2 class="toolTitle">'.$currentPortlet->getTitle().'</h2>';
                    echo $currentPortlet->getHtml();
                }
                ?>
            </td>
            <td valign="top">
                <div id="content">
                <?php include(DESIGN_INC_DIR.'wayhome.php'); ?>
                <?php include(_BIGACE_DIR_CID . 'include/loadModul.php'); ?>
                </div>
            </td>
            <td class="rightMenu">
                <?php

                //include(DESIGN_INC_DIR.'quicksearch.php'); 
                //include(DESIGN_INC_DIR.'last_edited_items.php');

                foreach($portlets['right'] AS $currentPortlet)
                {
                    echo '<h2 class="toolTitle">'.$currentPortlet->getTitle().'</h2>';
                    echo $currentPortlet->getHtml();
                }
                
                ?>
            </td>
        </tr>
    </table>
    <?php 
    include(DESIGN_INC_DIR.'menufooter.php');
    include(DESIGN_INC_DIR.'footer.php'); 

    import('classes.util.html.CopyrightFooter');
    CopyrightFooter::toString();
    ?>
</body>
</html>