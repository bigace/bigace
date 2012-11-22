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
 * Shows all available Languages....
 */

check_admin_login();
admin_header();

import('classes.language.LanguageEnumeration');

$tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("AdminLanguagesList.tpl.htm", false, true);

$ENUM = new LanguageEnumeration();

$cssClass = "row1";

for($i=0; $i < $ENUM->count(); $i++)
{
    $temp = $ENUM->next();

    $tpl->setCurrentBlock("row");
    $tpl->setVariable("LANG_ID", $temp->getID());
    $tpl->setVariable("LANG_NAME", $temp->getName(ADMIN_LANGUAGE));
    $tpl->setVariable("LANG_LOCALE", $temp->getShortLocale());
    $tpl->setVariable("LANG_LONGLOCALE", $temp->getFullLocale());
    $tpl->setVariable("LANG_CHARSET", $temp->getCharset());
    $tpl->setVariable("CSS", $cssClass);
    $tpl->parseCurrentBlock("row") ;

    $cssClass = ($cssClass == "row1") ? "row2" : "row1";
}

$tpl->show();

admin_footer();

?>
