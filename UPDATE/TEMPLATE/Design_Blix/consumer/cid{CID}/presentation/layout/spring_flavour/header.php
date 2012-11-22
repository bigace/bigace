<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst.
 * -------------------------------------------------
 * The BLIX Layout for BIGACE.
 * 
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $_BLIX['LANGUAGE']->getLocale(); ?>" lang="<?php echo $_BLIX['LANGUAGE']->getLocale(); ?>">
<head>
    <title><?php echo $MENU->getName(); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_BLIX['LANGUAGE']->getCharset(); ?>" />
    <link rel="stylesheet" href="<?php echo _BLIX_WEB_DIR; ?>style.css" type="text/css" media="screen, projection" >
    <meta name="generator" content="BIGACE <?php echo _BIGACE_ID; ?>">
    <style type="text/css">
    .CopyrightFooter { margin-top:10px; }
    .copyright { font-size: 12px; color: #444444; }
    </style>
    <?php
    foreach($_BLIX['PORTLETS'] AS $currentPortlet)
    {
        if ($currentPortlet->needsJavascript())
            echo $currentPortlet->getJavascript() . "\n";
    }
    ?>
</head>

<body><div id="container"<?php if(IS_SINGLE_COLUMN) echo ' class="singlecol"'; ?>>

<div id="header">
    <h1><a href="<?php echo createMenuLink( _BLIX_HOME ); ?>"><?php echo $_BLIX['TOP_LEVEL']->getName(); ?></a></h1>
    <div class="languages"><a href="<?php echo $appLinks->getChangeSessionLanguageURL('en'); ?>"><img src="<?php echo _BLIX_WEB_DIR; ?>en.gif" /></a>&nbsp;<a href="<?php echo $appLinks->getChangeSessionLanguageURL('de'); ?>"><img src="<?php echo _BLIX_WEB_DIR; ?>de.gif" /></a></div>
</div>

<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
    <td>
        <div id="navigation">
            <ul>
                <?php 
                $class = '';
                if(_BLIX_HOME_IN_MENU)
                {
                    if (_BLIX_HOME == $MENU->getID())
                        $class = ' class="selected"';
                    echo "<li".$class."><a href=\"".createMenuLink( _BLIX_HOME )."\">".$_BLIX['TOP_LEVEL']->getName()."</a></li>\n";
                }
                
                $menu_info = $MENU_SERVICE->getLightTreeForLanguage(_BLIX_TOP_MENU, $GLOBALS['_BIGACE']['PARSER']->getLanguage());
                
                for ($i=0; $i < $menu_info->count(); $i++) 
                {
                    $class = '';
                    $temp_menu = $menu_info->next();
                    if ($temp_menu->getID() == $MENU->getID() || $MENU_SERVICE->isChildOf($temp_menu->getID(), $MENU->getID())) {
                        $class = ' class="selected"';
                    }
                    echo "<li".$class."><a href=\"".createMenuLink($temp_menu->getID())."\">".$temp_menu->getName()."</a></li>\n";
                }
                ?>
            </ul>
        
        </div>
    </td>
    <td valign="top">
        <div id="navigation">
            <form action="<?php echo createMenuLink( $MENU->getID() . '_tBLIX_ksearchresult' ); ?>" method="post">
                <fieldset>
                    <input type="text" name="search" value="" id="s" />
                    <input type="hidden" name="langid" value="<?php echo $MENU->getLanguageID(); ?>" />
                    <input type="hidden" name="limit" value="5" />
                    <input type="submit" value="<?php echo getTranslation('search_button'); ?>" id="searchbutton" name="searchbutton" />
                </fieldset>
            </form>
        </div>
    </td>
</tr>
</table>

<hr class="low" />
