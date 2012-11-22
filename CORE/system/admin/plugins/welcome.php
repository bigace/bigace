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
 * Welcome Screen for the BIGACE Administration Panel.
 */

check_admin_login();

loadLanguageFile('welcome', ADMIN_LANGUAGE);

$langToDisplay  = extractVar(_LANGUAGE_PARAM, ADMIN_LANGUAGE);
$LANGUAGE       = new Language($langToDisplay);

$tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("WelcomeScreen.tpl.htm", false, true);

$tpl->setVariable("LANGUAGE_CHARSET", $LANGUAGE->getCharset());
$tpl->setVariable("FRAMESET_LINK", createAdminLanguageLink('tpl-frameset', ADMIN_LANGUAGE));
$tpl->setVariable("HEADER_LINK", createAdminLanguageLink('tpl-header', ADMIN_LANGUAGE));
$tpl->setVariable("NAVIGATION_LINK", createAdminLanguageLink('tpl-navigation', ADMIN_LANGUAGE));
$tpl->setVariable("CONTENT_LINK", createAdminLanguageLink(_ADMIN_ID_MAIN, ADMIN_LANGUAGE));
$tpl->setVariable("CONTENT_LINK", createAdminLanguageLink(_ADMIN_ID_MAIN, ADMIN_LANGUAGE));
//$tpl->setVariable("TIP", 'BIGACE lets you manage multiple Websites with one installation. Use the Community functions, easy and fast -one click- setup!');

$tpl->show();

unset($LANGUAGE);

?>