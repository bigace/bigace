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
 */ 

/**
 * Copyright (C) Kevin Papst. 
 *
 * For further information go to http://www.bigace.de/ 
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.modul
 */

import('classes.modul.ModulService');
import('classes.modul.Modul');

define('FAQ_SEC_DEFAULT', '1');

$modulService = new ModulService();
$modul = new Modul($MENU->getModulID());
$config = $modulService->getModulProperties($MENU, $modul, array());

$tplName = (isset($config['faq_template']) && strlen(trim($config['faq_template'])) > 0) ? trim($config['faq_template']) : "FAQ-Default";
$adminUrl = null;

/* #########################################################################
 * ############################  Show Admin Link  ##########################
 * #########################################################################
 */
if ($modul->isModulAdmin())
{
    import('classes.util.links.ModulAdminLink');
    import('classes.util.LinkHelper');
    $mdl = new ModulAdminLink();
    $mdl->setItemID($MENU->getID());
    $mdl->setLanguageID($MENU->getLanguageID());
	$adminUrl = LinkHelper::getUrlFromCMSLink($mdl);
}

$section = isset($config['faq_section']) ? $config['faq_section'] : FAQ_SEC_DEFAULT;
if(isset($config['faq_get_param']) && strlen(trim($config['faq_get_param'])) > 0 && isset($_GET[$config['faq_get_param']])) {
	$section = $_GET[$config['faq_get_param']];
}
$autoHide = isset($config['faq_auto_hide']) ? $config['faq_auto_hide'] : false;

// if we are not in a smarty context, create it
if(!isset($smarty))
{
	import('classes.smarty.SmartyDesign');
	import('classes.smarty.BigaceSmarty');
	$smarty = BigaceSmarty::getSmarty();
}

$smarty->assign('MENU', 	 $MENU);
$smarty->assign('CONFIG', 	 $config);
$smarty->assign('ADMIN_URL', $adminUrl);
$smarty->assign('HIDE_ANSWERS', $autoHide);

$tpl = new SmartyTemplate($tplName); 
$smarty->assign("SECTION", $section);
$smarty->display( $tpl->getFilename() );

?>