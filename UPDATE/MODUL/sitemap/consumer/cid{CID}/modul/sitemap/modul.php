<?php
/*
* Displays a language dependend Sitemap with 3 Levels.
*
* Copyright (C) Kevin Papst.
*
* For further information go to http://www.bigace.de/
*
* @version $Id$
* @author Kevin Papst
* @package bigace.modul
*/

define('SITEMAP_LANGUAGE', 	$MENU->getLanguageID());
define('SITEMAP_PUBLIC',   	_BIGACE_DIR_PUBLIC_WEB . 'modul/sitemap/');

/*
    $TEMP_MENU_RIGHT = $RIGHT_SERVICE->getMenuRight($GLOBALS['_BIGACE']['SESSION']->getUserID(), $startID);
    if (!$TEMP_MENU_RIGHT->canRead()) {
        $startID = _BIGACE_TOP_LEVEL;
    }
*/

$displayString  = getTranslation('sitemap_title');
$PARAMS_ID      = 'startID';
$startID        = extractVar($PARAMS_ID, _BIGACE_TOP_LEVEL);
$MENU_SERVICE   = new MenuService();
$TOP_LEVEL_MENU = $MENU_SERVICE->getMenu(_BIGACE_TOP_LEVEL);
$SITEMAP_MENU   = $MENU_SERVICE->getMenu($startID);

?>
<style type="text/css">
#sitemap {
	margin-bottom:20px;
}
#sitemap td {
	margin:0px;
}
#sitemap table
{
	line-height:2px;
	border:0px solid #000000;
}
</style>
<div id="sitemap">

    <?php
    if ($startID != _BIGACE_TOP_LEVEL)
    {
        ?>
        <table cellspacing="0" cellpadding="0" width="100%" align="left">
            <tr>
                <td>
                    <img height="1" alt="" src="<?php echo SITEMAP_PUBLIC; ?>empty.gif" width="20">
                    <?php
                        $AMENU = $SITEMAP_MENU;
                        $WAY_HOME = '&nbsp;&nbsp;';
                        while ($AMENU->getParentID() != _BIGACE_TOP_PARENT) {
                            $WAY_HOME = '&gt;&nbsp;<a href="' . LinkHelper::itemUrl($MENU, array($PARAMS_ID=>$AMENU->getID())) . '">' . $AMENU->getName() . '</a>&nbsp;' . $WAY_HOME;
                            $AMENU = $AMENU->getParent();
                        }
                        echo '<a href="' . LinkHelper::itemUrl($MENU, array($PARAMS_ID=>$AMENU->getID())) . '">'.$TOP_LEVEL_MENU->getName().'</a>&nbsp;' . $WAY_HOME;
                        unset ($AMENU);
                    ?>
                </td>
            </tr>
        </table>
        <?php
    }
    ?>

    <br />

    <table cellspacing="0" cellpadding="0" width="100%">
        <tr>
            <td><img height="1" alt="" src="<?php echo SITEMAP_PUBLIC; ?>empty.gif" width="9"></td>
            <td width="18"><img height="1" alt="" src="<?php echo SITEMAP_PUBLIC; ?>empty.gif" width="18"></td>
            <td><img height="1" alt="" src="<?php echo SITEMAP_PUBLIC; ?>empty.gif" width="39"></td>
            <td><img height="1" alt="" src="<?php echo SITEMAP_PUBLIC; ?>empty.gif"></td>
            <td width="50%"><img height="1" alt="" src="<?php echo SITEMAP_PUBLIC; ?>empty.gif" width="10"></td>
        </tr>
        <tr>
            <td><img height="1" alt="" src="<?php echo SITEMAP_PUBLIC; ?>empty.gif" width="1"></td>
            <td width="18"><img height="33" alt="" src="<?php echo SITEMAP_PUBLIC; ?>dl_start.gif" width="18"></td>
            <td colspan="2"><?php echo $SITEMAP_MENU->getName() ?></td>
            <td align="right">[<a href="<?php echo LinkHelper::itemUrl($SITEMAP_MENU); ?>"><?php echo $displayString; ?></a>]</td>
        </tr>
        <?php

        $menu_info = $MENU_SERVICE->getLightTreeForLanguage($startID, SITEMAP_LANGUAGE);

        for ($i=0; $i < $menu_info->count(); $i++)
        {
            $temp_menu = $menu_info->next();

            if ($temp_menu->hasChildren())
            {
                ?>
                <tr>
                    <td><img height="1" alt="" src="<?php echo SITEMAP_PUBLIC; ?>empty.gif" width="1"></td>
                    <td width="18"><img height="28" alt="" src="<?php echo SITEMAP_PUBLIC; ?>dl_e1_1_m.gif" width="18"></td>
                    <td width="39"><img height="28" alt="" src="<?php echo SITEMAP_PUBLIC; ?>dl_e1_2_m.gif" width="39"></td>
                    <td noWrap>
                    <?php
                        $templink = $temp_menu->getName();
                        if ($temp_menu->hasChildren()) {
                            $templink = '<a href="'.LinkHelper::itemUrl($MENU, array($PARAMS_ID => $temp_menu->getID())).'" title="'.$templink.'">' . $templink . '</a>';
                        }
                        echo $templink;
                    ?>
                    </td>
                    <td align="right">
                        [<a href="<?php echo LinkHelper::itemUrl($temp_menu); ?>"><?php echo $displayString; ?></a>]
                    </td>
                </tr>
                <?php
                    $menu_info1 = $MENU_SERVICE->getLightTreeForLanguage($temp_menu->getID(), SITEMAP_LANGUAGE);

                    for ($a=0; $a < $menu_info1->count(); $a++)
                    {
                        $temp_menu1 = $menu_info1->next();
                        ?>
                        <tr>
                            <td><img height="1" alt="" src="<?php echo SITEMAP_PUBLIC; ?>empty.gif" width="1"></td>
                            <?php

                            if ($a < $menu_info1->count()-1)
                            {
                                ?>
                                <td width="18"><img height="19" alt="" src="<?php echo SITEMAP_PUBLIC; ?>dl_e2_1_m.gif" width="18"></td>
                                <td width="39"><img height="19" alt="" src="<?php echo SITEMAP_PUBLIC; ?>dl_e2_2_m.gif" width="39"></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td width="18"><img height="19" alt="" src="<?php echo SITEMAP_PUBLIC; ?>dl_e2_1_m.gif" width="18"></td>
                                <td width="39"><img height="19" alt="" src="<?php echo SITEMAP_PUBLIC; ?>dl_e1_1_m_e1.gif" width="39"></td>
                                <?php
                            }
                            ?>
                            <td align="left" noWrap><img height="1" alt="" src="<?php echo SITEMAP_PUBLIC; ?>empty.gif" width="10">
                            <?php
                                $my_sitemap_link = $temp_menu1->getName();
                                if ($temp_menu1->hasChildren()) {
                                    $my_sitemap_link = '<a href="'.LinkHelper::itemUrl($MENU, array($PARAMS_ID => $temp_menu1->getID())).'" title="'.$my_sitemap_link.'">' . $my_sitemap_link . '</a>';
                                }
                                echo $my_sitemap_link;
                            ?>
                            </td>
                            <td align="right">
                                [<a href="<?php echo LinkHelper::itemUrl($temp_menu1); ?>"><?php echo $displayString; ?></a>]
                            </td>
                        </tr>
                        <?php
                }
            }
            else
            {
                ?>
                <tr>
                    <td><img height="1" alt="" src="<?php echo SITEMAP_PUBLIC; ?>empty.gif" width="1"></td>
                    <td width="18"><img height="33" alt="" src="<?php echo SITEMAP_PUBLIC; ?>dl_e1_1_s.gif" width="18"></td>
                    <td width="39"><img height="33" alt="" src="<?php echo SITEMAP_PUBLIC; ?>dl_e1_2_s.gif" width="39"></td>
                    <td noWrap>
                    <?php
                        $templink = $temp_menu->getName();
                        if ($temp_menu->hasChildren()) {
                            $templink= '<a href="'.LinkHelper::itemUrl($MENU, array($PARAMS_ID => $temp_menu->getID())).'" title="'.$templink.'">' . $templink . '</a>';
                        }
                        echo $templink;
                    ?>
                    </td>
                    <td align="right">
                        [<a href="<?php echo LinkHelper::itemUrl($temp_menu); ?>"><?php echo $displayString; ?></a>]
                    </td>
                </tr>
                <?php
            }
        }
        ?>
    </table>
</div>