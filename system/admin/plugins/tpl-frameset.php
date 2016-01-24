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
 * Standard file for the Administration Frameset.
 */
check_admin_login();

header( "Content-Type:text/html; charset=UTF-8" );

$langToDisplay = extractVar(_LANGUAGE_PARAM, ADMIN_LANGUAGE);
$LANGUAGE      = new Language( $langToDisplay );
    
$tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("AdminFrameset.tpl.html", false, true);

$tpl->setCurrentBlock("frameset");
$tpl->setVariable('ICO_DIR', _BIGACE_DIR_PUBLIC_WEB.'system/images/');
$tpl->setVariable("HEADER_LINK", createAdminLanguageLink('tpl-header', ADMIN_LANGUAGE));
$tpl->setVariable("NAVIGATION_LINK", createAdminLanguageLink('tpl-navigation', ADMIN_LANGUAGE));
$tpl->setVariable("CONTENT_LINK", createAdminLanguageLink(_ADMIN_ID_MAIN, ADMIN_LANGUAGE));
$tpl->parseCurrentBlock("frameset");
    
$tpl->show();

unset($LANGUAGE);
