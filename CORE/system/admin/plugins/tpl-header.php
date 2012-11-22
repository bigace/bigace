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
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.administration
 */

/**
 * Template file for the Administration Content Frame.
 */

check_admin_login();

    import('classes.language.LanguageEnumeration');
    import('classes.util.links.LogoutLink');
    import('classes.util.LinkHelper');

    $langToDisplay  = extractVar(_LANGUAGE_PARAM, ADMIN_LANGUAGE);
    $LANGUAGE       = new Language( $langToDisplay );
	header( "Content-Type:text/html; charset=" . $LANGUAGE->getCharset() );
    
    $logout = new LogoutLink();
    $logout->setItemID(_BIGACE_TOP_LEVEL);

    $languages = array();

    $ENUM = new LanguageEnumeration();

    for($i=0; $i < $ENUM->count(); $i++)
    {
        $temp = $ENUM->next();
        if($temp->isAdminLanguage()) {
            $tl = array();
            $tl['name'] = $temp->getName(ADMIN_LANGUAGE);
            $tl['locale'] = $temp->getShortLocale();
            $tl['link'] = createAdminLanguageLink(_ADMIN_ID_WELCOME, $temp->getShortLocale());
            $tl['selected'] = '';
            if(ADMIN_LANGUAGE == $temp->getShortLocale()) 
                $tl['selected'] = ' selected';
            $languages[] = $tl;
        }
    }

    $smarty = getAdminSmarty();
    
    if($GLOBALS['FRIGHT_SERVICE']->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'edit_own_profile')) {
        $smarty->assign('PROFILE_ADMIN', createAdminLink(_ADMIN_ID_USER_ADMIN, array('mode' => USERADMIN_MODE_EDIT_USER, 'data[id]' => $GLOBALS['_BIGACE']['SESSION']->getUserID())));
    }

    $sf = ServiceFactory::get();
    $ps = $sf->getPrincipalService();
    $atts = $ps->getAttributes($GLOBALS['_BIGACE']['SESSION']->getUser());
    $bg_tu = $GLOBALS['_BIGACE']['SESSION']->getUser();
    
    $smarty->assign("FIRSTNAME", (isset($atts['firstname']) ? $atts['firstname'] : ''));
    $smarty->assign("LASTNAME", (isset($atts['lastname']) ? $atts['lastname'] : ''));
    $smarty->assign("USERNAME", $bg_tu->getName());

    $smarty->assign("WEB_LINK", BIGACE_HOME);
    $smarty->assign("INDEX_LINK", createAdminLink(_ADMIN_ID_MAIN));
    $smarty->assign("ABOUT", createAdminLink('about'));
    $smarty->assign('STYLE_DIR', $GLOBALS['_BIGACE']['style']['DIR']);
    $smarty->assign('ADMIN_LANGUAGE', ADMIN_LANGUAGE);
    $smarty->assign('CHARSET', extractVar('adminCharset', $LANGUAGE->getCharset()));
    $smarty->assign('FORUM', 'http://forum.bigace.de/');
    $smarty->assign('MANUAL', 'http://wiki.bigace.de/bigace:manual');
    $smarty->assign('LOGOUT', LinkHelper::getUrlFromCMSLink($logout));
    $smarty->assign('LANGUAGES', $languages);
    $smarty->assign('SEARCH_URL', createAdminLink('search'));
    $smarty->display('AdminHeader.tpl');
    unset($smarty);
