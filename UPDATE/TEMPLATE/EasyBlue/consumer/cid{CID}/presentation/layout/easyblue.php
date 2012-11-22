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
 * For further information visit {@link http://www.kevinpapst.de www.kevinpapst.de}.
 *
 * @version $Id$
 * @author Kevin Papst
 */


/**
 * The Easy Blue Layout brings a fresh Look to your Website.
 */

define('IMPRESSUM_ID', _BIGACE_TOP_LEVEL);

// --------------------------------------------------------------

import('classes.item.ItemTreeWalker');
import('classes.util.applications');
import('classes.util.ApplicationLinks');

define('PUBLIC_THEME_DIR', _BIGACE_DIR_PUBLIC_WEB . 'easyblue/');

// --------------------------------------------------------------

$SELECTED_MENUID = $MENU->getID();
$APPS = new applications();

$APPS->hide($APPS->STATUS);

$APPS->setAddPreDelim(true);
$APPS->setAddPostDelim(true);
$APPS->setDelimiter('&nbsp;|&nbsp;');
if (!$USER_MENU_RIGHT->canWrite()) {
    $APPS->hide($APPS->EDITOR);
}
$EBENE = 0;

?><!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php

    $LANGUAGE = new Language($MENU->getLanguageID());

?>
    <base href="<?php echo _BIGACE_DIR_PUBLIC_WEB.'cid'._CID_.'/'; ?>">
    <title>.:: <?php echo $MENU->getName(); ?> ::.</title>
    <meta name="description" content="<?php echo $MENU->getDescription(); ?>">
    <meta name="generator" content="BIGACE <?php echo _BIGACE_ID; ?>">
    <meta name="robots" content="index,follow">
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $LANGUAGE->getCharset(); ?>">
    <style type="text/css">
    .CopyrightFooter { margin-top:10px; }
    .copyright { font-size: 10px; color: #444444; letter-spacing: -1px; }
    </style>
<?php

    unset ($LANGUAGE);

    echo $APPS->getAllJavascript();

?>
    <link rel="stylesheet" href="<?php echo PUBLIC_THEME_DIR; ?>default.css" type="text/css">
</head>
<body>

    <table border="0" width="100%" align="center">
    <tr>
    <td colspan="3" align="center">
    <br>
    <img src="../system/images/empty.gif" width="1" height="10" border="0" hspace="0" vspace="0">
    </td>
    </tr>
    <tr>
    <td width="10%">&nbsp;</td>
    <td align="center">
            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-style:solid;border-width:1px;border-color:#000000;background-color:#F0F6FB">
                <tr>
                <td align="center">
                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                    <td align="left">
                        <img src="../system/images/empty.gif" width="1" height="5" border="0" hspace="0" vspace="0">
                        <a href="<?php echo createMenuLink( _BIGACE_TOP_LEVEL ); ?>"><img src="../system/images/logosmall.gif" border="0"></a>
                    </td>
                    <td align="right" valign="top" style="font-size:11px">
<?php
                    // Create Way Home if not TOP_LEVEL is shown

                    if (_BIGACE_TOP_LEVEL == $MENU->getID()) {
                        echo '&nbsp;B&nbsp;I&nbsp;G&nbsp;A&nbsp;C&nbsp;E&nbsp;&nbsp;v' . _BIGACE_ID . '&nbsp;';
                    } else {
                        $ahtml = '';
                        $AMENU = $MENU;
                        while ($AMENU->getParentID() != _BIGACE_TOP_PARENT) {
                            $EBENE++;
                            $ahtml = '&gt;&nbsp;<a href="' . createMenuLink($AMENU->getID()) . '">' . $AMENU->getName() . '</a>&nbsp;' . $ahtml;
                            $AMENU = $AMENU->getParent();
                        }

                        $TOP_LEVEL = $MENU_SERVICE->getMenu(_BIGACE_TOP_LEVEL, $MENU->getLanguageID());
                        $ahtml = '<a href="' . createMenuLink( _BIGACE_TOP_LEVEL ) . '">'.$TOP_LEVEL->getName().'</a>&nbsp;' . $ahtml;
                        echo $ahtml;
                        unset ($AMENU);
                        unset ($ahtml);
                    }
?>
                    </td>
                    </tr>
                    </table>
                </td>
                </tr>
                <tr>
                <td align="center">
                    <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
<?php
            /* Start tabbed Menu */

            $menu_info = $MENU_SERVICE->getLightTreeForLanguage(_BIGACE_TOP_LEVEL, $GLOBALS['_BIGACE']['PARSER']->getLanguage());

                for ($i=0; $i < $menu_info->count(); $i++)
                {
                    $temp_menu = $menu_info->next();
                    $class = (($temp_menu->getID() == $MENU->getID())) ? 'taba' : 'tab';
                    if ($MENU_SERVICE->isChildOf($temp_menu->getID(), $MENU->getID())) {
                        $SELECTED_MENUID = $temp_menu->getID();
                        $class = 'taba';
                    }
                    echo '<td bgcolor="yellow" width="100" height="22" align="center" class="'.$class.'">';
                    echo '<img src="../system/images/empty.gif" width="100" height="1" border="0" hspace="0" vspace="0"><br>';
                    echo '<a href="' . createMenuLink($temp_menu->getId()) . '" title="' . $temp_menu->getName() . '" class="menu">'.$temp_menu->getName().'</a>';
                    echo '</td>';
                }

            /* End tabbed Menu */
?>
                    </tr>
                    </table>
                </td>
                </tr>
<?php

    /* show second level menu if available */
    $child = $MENU_SERVICE->getMenu($SELECTED_MENUID, $MENU->getLanguageID());

    if ($child->hasChildren() && ($child->getID() != _BIGACE_TOP_LEVEL))
    {
        $c = 2;
?>
                <tr>
                <td align="center" bgcolor="#708FBE">
                    <table cellpadding="0" cellspacing="0" border="0" width="100%" height="22" class="header">
                        <tr>
                            <td align="center" width="15"> </td>
                            <?php

                                //$temp_child_info = $child->get Childs();
                                $temp_child_info = $MENU_SERVICE->getLightTreeForLanguage($child->getID(), $child->getLanguageID());
                                for ($ii=0; $ii < $temp_child_info->count(); $ii++)
                                {
                                    $temp_menu = $temp_child_info->next();
                                    echo '<td align="center"><span style="color:#705A46">|</span></td>';
                                    echo '<td align="center" width="10%"><a href="' . createMenuLink($temp_menu->getId()) . '" title="' . $temp_menu->getName() . '" class="menu1">' . $temp_menu->getName() . '</a></td>';
                                    $c = $c + 2;
                                    unset($temp_menu);
                                }
                                if ($c > 2) {
                                    echo '<td align="center"><span style="color:#705A46">|</span></td>';
                                    $c++;
                                }
                                unset ($temp_child_info);
                            ?>
                            <td align="center" width="15"> </td>
                        </tr>
                        <tr style="background-color:#000000;">
                            <td colspan="<?php echo $c; ?>"></td>
                        </tr>
                    </table>
                </td>
                </tr>
<?php
    }
?>
                <tr>
                <td>
                    <table border="0" width="100%" style="background-color:#FFFFFF">
<?php

    if (($EBENE == 2 && $MENU->hasChildren()) || $EBENE > 2)
    {
        $tableMenuID = $MENU->getID();
        if (!$MENU->hasChildren()) {
               $tableMenuID = $MENU->getParentID();
        }
        ?>
        <tr>
         <td>
           <table width="100%">
            <tr>
            <?php
            $temp_child_info = $MENU_SERVICE->getLightTreeForLanguage($tableMenuID, $MENU->getLanguageID());
            for ($ii=0; $ii < $temp_child_info->count(); $ii++)
            {
                $temp_menu = $temp_child_info->next();
                ?>
                <td align="left" valign="top">
                    <table cellSpacing="1" cellpadding="2" width="100%" align="center" bgColor="#000000" border="0">
                        <tr><td bgColor="#87a3b5">
                        <?php
                            echo '<a href="' . createMenuLink($temp_menu->getId()) . '" title="' . $temp_menu->getName() . '" class="menu1">';
                            echo $temp_menu->getName() . '</a>';
                        ?>
                        </td>
                      </tr>
                      <tr>
                        <td bgColor="#F0F6FB">
                        <?php
                                echo '<table cellspacing="1" width="100%" border="0">';
                                echo '<tr><td>' . $temp_menu->getDescription() . '</td></tr>';
                                echo '</table>';
                                unset($temp_menu);
                        ?>
                        </td>
                      </tr>
                    </table>
                </td>
                <?php
            }
            ?>
            </tr>
           </table>
         </td>
        </tr>
            <?php
            echo '';
    }
?>
                    <tr>
                    <td align="left">
                    <br>
<?php include(_BIGACE_DIR_CID . 'include/loadModul.php'); ?>
                    <br>
                    </td>
                    </tr>
                    </table>
                </td>
                </tr>
                <tr>
                <td style="border-width:0px;border-top:1px;border-style:solid" align="center">
                    <table border="0" width="100%" align="center">
                    <tr>
                    <td align="center">
                    <?php
                        echo $APPS->getAllLink();
                    ?>
                    </td>
                    </tr>
                    </table>
                </td>
                </tr>
        </table>
    </td>
    <td width="10%">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3" align="center" class="footer">
        <?php
            $lid = (_ULC_ == 'de') ? 'en' : 'de';
            $templang = new Language($lid);
        ?>
        |&nbsp;Copyright <?php echo date('Y'); ?> |
        <a href="<?php echo createMenuLink(IMPRESSUM_ID); ?>">Impressum</a> |
        <a href="<?php echo ApplicationLinks::getChangeSessionLanguageURL($lid); ?>">
            <?php echo '<img border="0" src="../system/images/'.$templang->getLocale().'.gif">'; ?>
        </a> |
        <?php
            $APPS->setShowPicture(false);
            $APPS->setShowText(true);
            echo $APPS->getLink($APPS->STATUS) . '&nbsp;|';
        ?>
    </td>
    </tr>
    </table>
<?php
    import('classes.util.html.CopyrightFooter');
    CopyrightFooter::toString();
?>
</body>
</html>
