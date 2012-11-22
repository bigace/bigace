<?php

/**
* A simple demonstration of what a Layout could also be.
* This Layout was inspired by a design taken from SelfHTML.
*
* Copyright (C) Kevin Papst. 
*
* For further information go to {@link http://www.kevinpapst.de www.kevinpapst.de}.
*
* @version $Id$
* @author Kevin Papst 
*/

    import("classes.item.ItemTreeWalker");
    import("classes.util.applications");

    $SELECTED_MENUID = $MENU->getID();

    $APPS = new applications();
    $APPS->hide($APPS->HOME);
    $APPS->hide($APPS->STATUS);

    $APPS->setLinkClass('admin');
    $APPS->setAddPreDelim(true);
    $APPS->setAddPostDelim(true);
    $APPS->setDelimiter('&nbsp;::&nbsp;');

    if (!$USER_MENU_RIGHT->canWrite()) {
        $APPS->hide($APPS->EDITOR);
    }
    
    $GREENTABLE_DIR = _BIGACE_DIR_PUBLIC_WEB . 'greentable/';
    
?>
<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php

    $LANGUAGE = new Language($MENU->getLanguageID());

?>
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
    <link rel="stylesheet" href="<?php echo $GREENTABLE_DIR; ?>greentable.css" type="text/css">
</head>
    
<body text="#344011" link="#7a0202" vlink="#666666" alink="#dec3a9" bgcolor="#dce0d3">

    <a name="top"> </a>
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr>
    <td rowspan="9" width="64">&nbsp;</td>
    <td colspan="3">
    </td>
    <td width="11">&nbsp;</td>
    <td rowspan="9" width="64">&nbsp;</td>
    </tr>
    <tr>
    <td bgcolor="#9ea985" colspan="2" valign="top" rowspan="2">
    <table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr>
    <td width="28%" height="132">
    <h1 align="left"><br>
    <a href="<?php echo createMenuLink('-1'); ?>"><img src="<?php echo $GREENTABLE_DIR; ?>logo.jpg" width="190" height="83" border="0" alt="Home"></a>
    <br>
    </h1>
    </td>
    <td width="72%" height="132">
    <h1 align="right"><?php echo $MENU->getName(); ?>&nbsp;::</h1>
    <p align="right"><b>
    <?php
    
        $topMenus = $MENU_SERVICE->getTreeWalker(_BIGACE_TOP_LEVEL);
        
        for ($i=0; $i < $topMenus->count(); $i++) 
        {
            $temp_menu = $topMenus->next();
            if ($MENU_SERVICE->isChildOf($temp_menu->getID(), $MENU->getID())) {
                $SELECTED_MENUID = $temp_menu->getID();
            }
            echo '<a href="' . createMenuLink($temp_menu->getId()) . '" title="' . $temp_menu->getName() . '">' . strtolower($temp_menu->getName()) . '</a> :: ';
        }
        
        if ($SELECTED_MENUID != _BIGACE_TOP_LEVEL)
        {
            $selectedMenu = $MENU_SERVICE->getMenu($SELECTED_MENUID);
            
            if ($selectedMenu->hasChildren()) 
            {
                echo '<br/>';
                $childs = $MENU_SERVICE->getTreeWalker($SELECTED_MENUID);
                for ($i=0; $i < $childs->count(); $i++) 
                {
                    $currentChild = $childs->next();
                    if ($currentChild->getId() == $MENU->getID()) {
                        $nextLevelID = $currentChild->getId();
                    }
                    echo '<a href="' . createMenuLink($currentChild->getId()) . '" title="' . $currentChild->getName() . '">' . strtolower($currentChild->getName()) . '</a> :: ';
                }
                unset ($currentChild);
                unset ($childs);
            }
            
            if (!isset($nextLevelID)) 
            {
                if ($MENU_SERVICE->countLevel($MENU->getID()) > 2) {
                    if ($MENU->hasChildren()) {
                        $nextLevelID = $MENU->getID();
                    } else {
                        $nextLevelID = $MENU->getParentID();
                    }
                }
            }
            
            if (isset($nextLevelID)) 
            {
                echo '<br/>';
                $childs = $MENU_SERVICE->getTreeWalker($nextLevelID);
                for ($i=0; $i < $childs->count(); $i++) 
                {
                    $currentChild = $childs->next();
                    echo '<a href="' . createMenuLink($currentChild->getId()) . '" title="' . $currentChild->getName() . '">' . strtolower($currentChild->getName()) . '</a> :: ';
                }
            }
        }
    ?>
    </b></p>
    </td>
    </tr>
    </table>
    </td>
    
    <td bgcolor="#9ea985" rowspan="2">
    <div align="right"><br>
    <br>
    <a href="<?php echo createMenuLink(_BIGACE_TOP_LEVEL, array(), 'impressum.html'); ?>">impressum</a>&nbsp;&nbsp;<br>
    <a href="<?php echo createMenuLink(_BIGACE_TOP_LEVEL, array(), 'feedback.html'); ?>">feedback</a>&nbsp;&nbsp;<br>
    <a href="<?php echo createMenuLink(_BIGACE_TOP_LEVEL, array(), 'guestbook.html'); ?>">guestbook</a>&nbsp;&nbsp;
    
    </div>
    </td>
    
    <td valign="top" width="11" height="20" style="background-image:url(<?php echo $GREENTABLE_DIR; ?>bg_schatten3.gif)"><img src="<?php echo $GREENTABLE_DIR; ?>bg_schatten2.gif" width="11" height="25" alt=""></td>
    </tr>
    <tr>
    <td style="background-image:url(<?php echo $GREENTABLE_DIR; ?>bg_schatten3.gif)" rowspan="2" width="11">&nbsp;</td>
    </tr>
    <tr>
    <td bgcolor="#bbc2a9" colspan="2" height="35">
    <?php
        // wayhome
        if (_BIGACE_TOP_LEVEL != $MENU->getID()) 
        {
            $AMENU = $MENU;
            $ahtml = '&nbsp;&nbsp;';
            echo $ahtml;
            while ($AMENU->getParentID() != _BIGACE_TOP_PARENT) {
                $ahtml = '&gt;&nbsp;<a href="' . createMenuLink($AMENU->getID()) . '">' . $AMENU->getName() . '</a>&nbsp;' . $ahtml;
                $AMENU = $AMENU->getParent();
            } 
            echo '<a href="' . createMenuLink( _BIGACE_TOP_LEVEL ) . '">Home</a>&nbsp;' . $ahtml;
            unset ($AMENU);
            unset ($ahtml);
        }
    ?>
    </td>
    
    <td bgcolor="#bbc2a9" height="35">
    <div align="right">
    <?php 
        echo $APPS->getAllLink();
        echo $APPS->getHomeLink('', 'home.gif');
    ?>
    </div>
    </td>
    
    </tr>
    <tr>
        <td style="background-image:url(<?php echo $GREENTABLE_DIR; ?>bg_schatten6.gif)"><img src="<?php echo $GREENTABLE_DIR; ?>bg_schatten5.gif" width="18" height="25" alt=""></td>
        <td style="background-image:url(<?php echo $GREENTABLE_DIR; ?>bg_schatten6.gif)" colspan="2">&nbsp;</td>
        <td width="11" valign="top"><img src="<?php echo $GREENTABLE_DIR; ?>bg_schatten7.gif" width="11" height="25" alt=""></td>
    </tr>
    <tr>
        <td colspan="3" bgcolor="#9ea985">&nbsp;</td>
        <td width="11" valign="top"><img src="<?php echo $GREENTABLE_DIR; ?>bg_schatten2.gif" width="11" height="25" alt=""></td>
    </tr>
    
    <tr>
        <td colspan="3" bgcolor="#9ea985" height="497" valign="top">
    <?php    
        
        include(_BIGACE_DIR_CID . 'include/loadModul.php');
    
    ?>    
        </td>
        <td style="background-image:url(<?php echo $GREENTABLE_DIR; ?>bg_schatten3.gif)" valign="top" rowspan="2" width="11">&nbsp;</td>
    </tr>
    <tr>
        <td bgcolor="#bbc2a9" colspan="2" height="25">&nbsp;Copyright <?php echo date('Y'); ?>
            &nbsp;&nbsp;&nbsp;<?php 
            $APPS->setShowPicture(false);
            $APPS->setShowText(true);
            echo $APPS->getLink($APPS->STATUS, '&#149;'); ?>
        </td>
        <td bgcolor="#bbc2a9" height="25">
            <div align="right"><a href="#top" ONFOCUS="this.blur()"><img src="<?php echo $GREENTABLE_DIR; ?>pfeil_ob.gif" width="30" height="14" border="0" alt=""></a></div>
        </td>
    </tr>
    <tr>
        <td style="background-image:url(<?php echo $GREENTABLE_DIR; ?>bg_schatten6.gif)" width="594"><img src="<?php echo $GREENTABLE_DIR; ?>bg_schatten5.gif" width="18" height="25" alt=""></td>
        <td style="background-image:url(<?php echo $GREENTABLE_DIR; ?>bg_schatten6.gif)" colspan="2">&nbsp;</td>
        <td width="11" valign="top"><img src="<?php echo $GREENTABLE_DIR; ?>bg_schatten7.gif" width="11" height="25" alt=""></td>
    </tr>
    </table>

<?php 
    import('classes.util.html.CopyrightFooter');
    CopyrightFooter::toString();
?>

</body>
</html>
