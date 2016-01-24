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
 * @package bigace.classes
 * @subpackage template
 */
 
if(!defined('SMARTY_DIR')) {
    define('SMARTY_DIR', _BIGACE_DIR_ADDON . 'smarty/');
    require_once(SMARTY_DIR . 'Smarty.class.php');
}

/**
 * Always load this class, when using Smarty Templates.
 * It takes care about loadig the Smarty Engine and about setting the proper environment.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage smarty
 */
class BigaceSmarty
{
	
    /**
     * Fetch a Smarty instance to be used in any Community context.
     * @return Smarty an proper initialized Smarty object to use in an Community environment
     */
    static function getSmarty() {
        $smarty = new Smarty();
        $smarty->default_resource_type = 'bigace';
        $smarty->compile_dir    = _BIGACE_DIR_CID . 'smarty/templates_c/';
        $smarty->config_dir     = _BIGACE_DIR_CID . 'smarty/configs/';
        $smarty->cache_dir      = _BIGACE_DIR_CID . 'smarty/cache/';
        $smarty->plugins_dir    = array( SMARTY_DIR . 'plugins/' );
        return $smarty;
     }

    /**
     * Fetch a Smarty instance to be used in any outside context.
     * @return Smarty an proper initialized Smarty object to use in an PlugIn environment
     */
    static function getPluginSmarty() {
        $smarty = new Smarty();
        $smarty->template_dir   = _BIGACE_DIR_ADDON . 'smarty/templates/';
        $smarty->compile_dir    = _BIGACE_DIR_ADDON . 'smarty/templates_c/';
        $smarty->config_dir     = _BIGACE_DIR_ADDON . 'smarty/configs/';
        $smarty->cache_dir      = _BIGACE_DIR_ADDON . 'smarty/cache/';
        $smarty->plugins_dir    = array( SMARTY_DIR . 'plugins/' );
        return $smarty;
     }     

    /**
     * Fetch a Smarty instance to be used in the Administration.
     * @return Smarty an proper initialized Smarty object to use in the Administration
     */
    static function getAdminSmarty() {
        $smarty = new Smarty();
        $smarty->template_dir   = _BIGACE_DIR_ADMIN . 'smarty/templates/';
        $smarty->compile_dir    = _BIGACE_DIR_ADMIN . 'smarty/templates_c/';
        $smarty->config_dir     = _BIGACE_DIR_ADMIN . 'smarty/configs/';
        $smarty->cache_dir      = _BIGACE_DIR_ADMIN . 'smarty/cache/';
        $smarty->plugins_dir    = array( SMARTY_DIR . 'plugins/' );
        $smarty->assign('CSRF_TOKEN', get_csrf_token());
        return $smarty;
     }
}

?>
