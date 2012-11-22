<?php
/**
* -------------- BIGACE DESIGN 1 --------------
*
* Copyright (C) Kevin Papst.
*
* For further information go to http://www.bigace.de/
*
* @version $Id$
* @author Kevin Papst
*/

import('classes.item.ItemTreeWalker');
import('classes.util.applications');
import('classes.util.ApplicationLinks');

$SELECTED_MENUID = $MENU->getID();
$APPS = new applications();

$APPS->hide($APPS->STATUS);
if (!$USER_MENU_RIGHT->canWrite()) {
    $APPS->hide($APPS->EDITOR);
}

if ($MENU->getID()==_BIGACE_TOP_LEVEL) {
    $APPS->hide($APPS->HOME);
}

$LANGUAGE = new Language($MENU->getLanguageID());

$publicDir = _BIGACE_DIR_PUBLIC_WEB.'cid'._CID_.'/bigacedesign1/';

?>
<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $_SERVER['HTTP_HOST']; ?> :: <?php echo $MENU->getName(); ?> ::</title>
<meta name="generator" content="BIGACE">
<meta name="robots" content="index,follow">
<?php
    echo '<meta name="description" content="'.$MENU->getDescription().'">' . "\n";
    echo '<meta http-equiv="Content-Type" content="text/html; charset='.$LANGUAGE->getCharset().'">' . "\n";
    echo '<link rel="stylesheet" href="'.$publicDir.'design.css" type="text/css">' . "\n";
    echo $APPS->getAllJavascript();
    unset ($LANGUAGE);
?>
<script type="text/javascript">
<!--
function BlurLinks(){
    lnks=document.getElementsByTagName('a');
    for(i=0;i<lnks.length;i++){
        lnks[i].onfocus=new Function("if(this.blur)this.blur()");
    }
}

onload=BlurLinks;
-->
</script>
</head>
<body bgcolor="#e6e6e6" text="black" topmargin="8" marginheight="8"  leftmargin="8" marginwidth="8">
<a name="top"></a>
<div align="center">

<table bgcolor="#000000" cellspacing="0" cellpadding="0" border="0" width="100%" >
<tr>
<td width="100%" ><table cellspacing="1" cellpadding="0" border="0" width="100%">
<tr>
<td align="right"  width="100%" colspan="3" bgcolor="#646B84" class="leiste">
<table cellspacing="0" cellpadding="0" border="0" >
<tr>
<td valign="top" align="right" ><?php echo '&nbsp;B&nbsp;I&nbsp;G&nbsp;A&nbsp;C&nbsp;E&nbsp;&nbsp;v' . _BIGACE_ID . '&nbsp;'; ?></td>
</tr>
</table>

</td>
</tr>
<tr>
<td bgcolor="#ffffff" valign="top" width="140" rowspan="3" >
    <table  border="0" cellpadding="0" cellspacing="0" width="140">
    <tr>
    <td><img src="<?php echo $publicDir; ?>empty.gif" width="1" height="72" border="0" alt=""></td>
    </tr>
    </table>
<?php

 // Beginn Menue linke Seite


    $menu_info = $MENU_SERVICE->getTreeWalker(_BIGACE_TOP_LEVEL);

    for ($i=0; $i < $menu_info->count(); $i++)
    {
        $temp_menu = $menu_info->next();
        ?>
        <table width="140" border="0" cellpadding="0" cellspacing="0" id="menu" align="center" >
        <tr>
        <td class="rubrik"><?php echo '&nbsp;'.$temp_menu->getName(); ?>&nbsp;</td>
        </tr>
        <?php
        $menu_info1 = $MENU_SERVICE->getTreeWalker($temp_menu->getID());

        for ($a=0; $a < $menu_info1->count(); $a++)
        {
            $temp_menu1 = $menu_info1->next();
            echo '<tr><td><a href="' . createMenuLink($temp_menu1->getId()) . '" title="' . $temp_menu1->getName() . '">&nbsp;'.$temp_menu1->getName().'</a></td></tr>';
        }
        echo '</table>';
    }


 // Ende Menue linke Seite

$APPS->setShowText(true);
$APPS->setShowPicture(true);
?>
<table width="140" border="0" cellpadding="0" cellspacing="0" id="menu" align="center" >
<tr>
<td class="rubrik">&nbsp;Tools</td>
</tr>
<tr><td><?php echo $APPS->getAllLink(); ?></td></tr>
</table>

</td>
<td valign="middle" align="center" bgcolor="#ffffff" width="100%" height="72"><a href="<?php echo createMenuLink(_BIGACE_TOP_LEVEL); ?>" title=""><img src="<?php echo $publicDir; ?>banner.gif" width="468" height="60" border="0" alt="<?php echo getTranslation('home'); ?>"></a></td>
<td bgcolor="#ffffff" valign="top" width="175" rowspan="3" >

<!--Beginn Menue rechte Seite-->
<table  border="0" cellpadding="0" cellspacing="0"  width="140"  >
<tr>
<td><img src="<?php echo $publicDir; ?>empty.gif" width="1" height="72" border="0" alt=""></td>
</tr>
</table>

<table width="140" border="0" cellpadding="0" cellspacing="0" id="menu" align="center" >
<tr>
<td align="right" class="rubrik">WAY HOME&nbsp;</td>
</tr>
<?php


    if (_BIGACE_TOP_LEVEL != $MENU->getID())
    {
        $allowed = true;
        $AMENU = $MENU;
        $ahtml = '';
        while ($AMENU->getParentID() != _BIGACE_TOP_PARENT)
        {
            $temp_right = $RIGHT_SERVICE->getMenuRight($GLOBALS['_BIGACE']['SESSION']->getUserID(), $AMENU->getId());
            if ($temp_right->canRead())
            {
                $ahtml = '<tr><td align="right"><a href="' . createMenuLink($AMENU->getID()) . '">' . $AMENU->getName() . '</a></td></tr>' . $ahtml;
            }
            else
            {
                $allowed = false;
            }
            $AMENU = $AMENU->getParent();
        }

        if ($allowed) {
            echo '<tr><td align="right"><a href="' . createMenuLink( _BIGACE_TOP_LEVEL ) . '">Home</a></td></tr>' . $ahtml;
        } else {
            echo '<tr><td align="right"><a href="' . createMenuLink( $MENU->getID() ) . '">'.$MENU->getName().'</a></td></tr>';
        }
        unset ($allowed);
        unset ($temp_right);
        unset ($AMENU);
        unset ($ahtml);
    }
?>

</table>

<?php
    if (_BIGACE_TOP_LEVEL != $MENU->getID())
    {
        $AMENU = $MENU;
        while ($AMENU->getParentID() != _BIGACE_TOP_PARENT)
        {
            $temp_right = $RIGHT_SERVICE->getMenuRight($GLOBALS['_BIGACE']['SESSION']->getUserID(), $AMENU->getId());
            if ($temp_right->canRead())
            {
                ?>
                <table width="140" border="0" cellpadding="0" cellspacing="0" id="menu" align="center" >
                <tr>
                <td align="right" class="rubrik"><?php echo $AMENU->getName(); ?>&nbsp;</td>
                </tr>
                <?php
                    $menu_infox = $MENU_SERVICE->getTreeWalker($AMENU->getID());

                    for ($a=0; $a < $menu_infox->count(); $a++)
                    {
                        $temp_menux = $menu_infox->next();
                        echo '<tr><td align="right"><a href="' . createMenuLink($temp_menux->getId()) . '" title="' . $temp_menux->getName() . '">'.$temp_menux->getName().'&nbsp;</a></td></tr>';
                    }
                ?>
                </table>
                <?php
            }
            $AMENU = $AMENU->getParent();
        }
        unset ($temp_right);
        unset ($AMENU);
    }
?>
<!--Ende Menue rechte Seite-->

</td>

</tr>
<tr>
<td width="100%" align="center" bgcolor="#6e7591" class="leiste"><?php

        echo $MENU->getName();

?></span></td>






</tr>
<tr>
<td valign="top" bgcolor="#e6e6e6"  width="100%" height="450" >
<br>

<table border="0" cellpadding="0" cellspacing="0"  width="100%" >
<tr>
<td width="20"><img src="<?php echo $publicDir; ?>empty.gif" width="20" height="500" border="0" alt=""></td>
<td valign="top" width="100%">
<?php include(_BIGACE_DIR_CID.'include/loadModul.php'); ?>
</td>
<td width="20" ><img src="<?php echo $publicDir; ?>empty.gif" width="20" height="1" border="0" alt=""></td>
</tr>
</table>

</td>
</tr>
<tr>
  <td bgcolor="#646B84" class="leiste">&nbsp;
  <?php
    $APPS->setShowPicture(false);
    $APPS->setShowText(true);
    $APPS->setLinkClass('footer');
    echo $APPS->getLink($APPS->STATUS);
  ?>
  </td>
  <td align="center" bgcolor="#646B84" class="leiste">
    <a href="<?php echo BIGACE_HOME; ?>" class="footer"><?php echo $_SERVER['HTTP_HOST']; ?></a>
  </td>
  <td bgcolor="#646B84" class="leiste" align="right" valign="middle">
    <?php
        $lid = (_ULC_ == 'de') ? 'en' : 'de';
        $templang = new Language($lid);
    ?>
        <a href="<?php echo ApplicationLinks::getChangeSessionLanguageURL($lid); ?>">
        <?php
            echo '<img align="middle" border="0" src="'.$publicDir.$templang->getLocale().'.gif">';
            unset ($templang);
            unset ($lid);
        ?>
        </a>
        <a href="#top" onMouseOver="window.status='ach oben'; return true" onMouseOut="window.status=''">
            <img src="<?php echo $publicDir; ?>top.gif" width="12" height="12" border="0" alt="" align="middle">
        </a>
    </td>
</tr>
</table></td>
</tr>
</table>
</div>

</body>
</html>