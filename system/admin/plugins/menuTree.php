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
 * For further information visit {@link http://www.bigace.de www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.administration
 */

    check_admin_login();

    $LANGUAGE = new Language( ADMIN_LANGUAGE );
    $treeLanguage = (isset($_GET['treeLanguage']) ? $_GET['treeLanguage'] : ADMIN_LANGUAGE);

    $smarty = getAdminSmarty();
    $smarty->assign("LANGUAGE_CHARSET", $LANGUAGE->getCharset());
    $smarty->assign("TREE_LINK", createAdminLanguageLink('jsMenuTree', ADMIN_LANGUAGE, array('treeLanguage' => $treeLanguage)));
    $smarty->assign("CONTENT_LINK", createAdminLanguageLink(_ADMIN_ID_MAIN, ADMIN_LANGUAGE));
    $smarty->display('MenuAdminFrameset.tpl');

    unset($smarty);
	unset($LANGUAGE);
