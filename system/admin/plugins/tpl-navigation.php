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
 * Administration Navigation Frame.
 */

check_admin_login();

    import('classes.util.MenuLink');
    import('classes.util.LinkHelper');

    define('_PARAM_METHOD', 'method');
    define('_PARAM_SUBMENU', 'displaySubMenu');
    define('_METHOD_SUBMENU', 'showSubmenu');


    // fetch all available top menu names
    $childs = getAdminMenus();

    /* ---------------------------------
    ------------- NAVIGATION -----------
    ------------------------------------*/

    $langToDisplay  = extractVar(_LANGUAGE_PARAM, ADMIN_LANGUAGE);
    $LANGUAGE       = new Language( $langToDisplay );
	header( "Content-Type:text/html; charset=" . $LANGUAGE->getCharset() );
    
    setBigaceTemplateValue('STYLE_CSS', $GLOBALS['_BIGACE']['style']['class']->getCSS());
    
    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("AdminNavigate.tpl.html", false, true);

    for ($i=0; $i < count($childs); $i++)
    {
        $child = $childs[$i];

        if ($child->hasChilds()) // admin menu
        {
            $temp = $child->getChilds();
            foreach($temp AS $tempchild)
            {
            	if(!$tempchild->isHidden()) {	
					if($tempchild->loadTranslationForMenu())
						$tempchild->loadTranslation();
					$tpl->setCurrentBlock("plugin") ;
	                $tpl->setVariable("PLUGIN_ID", $tempchild->getID());
	                $tpl->setVariable("PLUGIN_LINK", createAdminLink($tempchild->getID(), array(), '', 'content'));
	                $tpl->setVariable("PLUGIN_NAME", $tempchild->getName());
	                $tpl->parseCurrentBlock("plugin") ;
            	}
            }

            $tpl->setCurrentBlock("pluginloop") ;
            $tpl->setVariable("MENU_LINK", createAdminLink(_ADMIN_ID_MAIN, array(_PARAM_METHOD => _METHOD_SUBMENU, _PARAM_SUBMENU => $child->getID()), '', 'content'));
            $tpl->setVariable("MENU_TITLE", $child->getName());
            $tpl->setVariable("MENU_ID", $child->getID());
            $tpl->parseCurrentBlock("pluginloop") ;
        }
    }

    import('classes.util.html.CopyrightFooter');

    $tpl->setCurrentBlock("footer");
    $tpl->setVariable("COPYRIGHT", CopyrightFooter::get());
    $tpl->parseCurrentBlock("footer") ;

    $tpl->show();

    unset($LANGUAGE);
