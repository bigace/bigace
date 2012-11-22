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
 * The Start Screen of the Administration, shows teaser of all Main- and Submenus.
 */

check_admin_login();
loadLanguageFile('index', ADMIN_LANGUAGE);

define('_PARAM_DELETE',     'sDfgzu87z6ujik');
define('_PARAM_METHOD',     'method');
define('_PARAM_SUBMENU',    'displaySubMenu');

define('_METHOD_INDEX',     'showIndex');
define('_METHOD_SUBMENU',   'showSubmenu');

$method = extractVar(_PARAM_METHOD, _METHOD_INDEX);

    import('classes.util.html.FormularHelper');

    admin_header();
    
	$filesToDelete = array(
		'install_dir'       => _BIGACE_DIR_ROOT . '/misc/install/',
	    'install_script'    => _BIGACE_DIR_ROOT . '/install_bigace.php',
	    'install_redirect'  => _BIGACE_DIR_ROOT . '/install.php',
	    'upgrade'           => _BIGACE_DIR_ROOT . '/upgrade.php',
	);

   	$hints = array();
   	$errors = array();

    if (isset($_GET[_PARAM_DELETE])) 
    {
    	import('classes.util.IOHelper');
		$fe = "";
	    foreach($filesToDelete AS $ix) {
	        if(file_exists($ix)) {
	            if(!IOHelper::deleteFile($ix))
	            	$fe .= '<br>' . $ix;
	        }
	    }
		if(strlen($fe)>0)
			$errors[] = getTranslation('could_not_delete_files') . ':' . $fe;
    }

    $smarty = getAdminSmarty();
    
    $submenuId = extractVar(_PARAM_SUBMENU, 'index');
    
    if(defined('_BIGACE_DEMO_VERSION')) {
	    $smarty->assign('HINT', $mods);
		$hints[] = getTranslation('demo_version_info');
    }

    // ################### CHECK DANGEROUS FILES ###################
    $filesFound = '';
    foreach($filesToDelete AS $ix => $checkFile) {
    	if(file_exists($checkFile))
        	$filesFound .= '<br/>' . $checkFile;
    }
        
    if($filesFound != '') {
        $filesFound .= ' <a href="'.createAdminLink($MENU->getID(), array(_PARAM_DELETE=>'true')).'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'delete.png" border="0"></a>';
		$errors[] = getTranslation('hint_security') . $filesFound;
    }
    // -------------------------------------------------------------


    // display smarty hint!        
    if(!ConfigurationReader::getConfigurationValue('system', 'use.smarty', false) && ConfigurationReader::getConfigurationValue('system', 'show.smarty.hint', true)) {
		$hints[] = getTranslation('hint_smarty');
    }

	$menus = array();
                
    // Show the TOP Level Menus
    $topLevelMenus = getAdminMenus();
    $a = -1;
    for($i=0; $i < count($topLevelMenus) / 3; $i++)
    {
        
        for ($b = 0; $b < 3; $b++) 
        {
            if ($a+1 < count($topLevelMenus)) 
            {
                $submenu = $topLevelMenus[++$a];

				// the menu itself ...
				$cMenu = array(
					'url'	=> createAdminLink($MENU->getID(), array(_PARAM_METHOD => _METHOD_SUBMENU, _PARAM_SUBMENU => $submenu->getID())),
					'id'	=> $submenu->getID(),
					'name'	=> $submenu->getName(),
					'desc'	=> $submenu->getDescription(),
					'child'	=> array()
				);

				// ... and its childs
		        $temp = $submenu->getChilds();
		        for ($ca=0; $ca < count($temp); $ca++)
		        {
		            $tempchild = $temp[$ca];
		            if (!$tempchild->isHidden() && check_admin_permission($tempchild->getPermissions())) 
		            {
						if($tempchild->isTranslated()){
							$tempchild->loadTranslation();
						}
						$csMenu = array(
							'url'	=> createAdminLink($tempchild->getID()),
							'id'	=> $tempchild->getID(),
							'name'	=> $tempchild->getName(),
							'desc'	=> $tempchild->getDescription()
						);
						$cMenu['child'][] = $csMenu;
		            }
		        }

				$menus[] = $cMenu;
            }
        }
    }

    
    $sf = ServiceFactory::get();
    $ps = $sf->getPrincipalService();
    $atts = $ps->getAttributes($GLOBALS['_BIGACE']['SESSION']->getUser());

	if(ConfigurationReader::getConfigurationValue('admin', 'display.latest.news', true))
	{

        $t = '<a href="'."%1\$s".'" target="_blank">'.getTranslation('no_incoming_links_title').'</a>
              <span>'."%2\$s".'</span><p>'.getTranslation('no_incoming_links_msg').'</p>';
        $empty2 = sprintf($t, "http://forum.bigace.de/cms-showcase/", date('F d, Y'));

        $feeds = array(
            array(
                'name'   => getTranslation('latest_news'),
                'url'    => 'http://feeds2.feedburner.com/Bigace-Admin',
                'html'   => 'Loading... ',
                'amount' => '4',
                'ajax'   => '',
                'empty'  => 'Sorry, this service is currently unavailable. Please come back soon!'
            ),
            array(
                'name'   => getTranslation('incoming_links'),
                'url'    => 'http://blogsearch.google.com/blogsearch_feeds?hl=en&scoring=d&ie=utf-8&num=10&output=rss&q=link:'.BIGACE_HOME,
                'html'   => 'Loading... ',
                'amount' => '10',
                'ajax'   => '',
                'empty'  => $empty2
            ),
        );
        
        define('MAGPIE_DIR', _BIGACE_DIR_ADDON.'magpierss/');
        define('MAGPIE_CACHE_ON', 2);
        define('MAGPIE_CACHE_DIR', $GLOBALS['_BIGACE']['DIR']['cache']);
        define('MAGPIE_CACHE_AGE', 43200); // 12 hours
        define('MAGPIE_OUTPUT_ENCODING','UTF-8');
        
        require_once(MAGPIE_DIR.'rss_fetch.inc');
	    $cache = new RSSCache( MAGPIE_CACHE_DIR, MAGPIE_CACHE_AGE );

	    $i=0;
	    
        foreach ( $feeds as &$myFeed ) {
	        $status = $cache->check_cache($myFeed['url'] . MAGPIE_OUTPUT_ENCODING);
	        if ( 'HIT' !== $status ) {
		        $myFeed['ajax'] = createAdminLink('dashboard-news',array('rss' => $i++));
	        }
	        else {
	            // this comes from the cache, so we can be sure its usable
	            $rss = fetch_rss($myFeed['url']);
                $items = array_slice($rss->items, 0, $myFeed['amount']);
                $html = '';
                foreach($items AS $item) 
                {
                    $iDate = '';
                    if(isset($item['date_timestamp'])) {
                        $iDate = date('F d, Y',$item['date_timestamp']);
                    } else if(isset($item['dc']['date'])) {
                        $iDate = date('F d, Y',strtotime($item['dc']['date']));
                    }
                    $myDate = '';
                    if(strlen($iDate) > 1) 
                        $myDate = '<span>'.$iDate.'</span>';
                    $html .= '<a href="'.$item['link'].'" target="_blank">' . $item['title'].'</a>'.$myDate.'
                                <p>' . $item['description'].'</p>';
                }
	            $myFeed['html'] = $html;

                // but we believe in murphy, so lets check it a last time
                if($myFeed['html'] == '') {
                    $myFeed['html'] = $myFeed['empty'];
                }
	        }
        }
        
        $smarty->assign("FEEDS", $feeds);
    }
    
    $bg_tu = $GLOBALS['_BIGACE']['SESSION']->getUser();
    
    $smarty->assign("FIRSTNAME", (isset($atts['firstname']) ? $atts['firstname'] : ''));
    $smarty->assign("LASTNAME", (isset($atts['lastname']) ? $atts['lastname'] : ''));
    $smarty->assign("USERNAME", $bg_tu->getName());
    $smarty->assign("FORM_ACTION", createAdminLink('styleSwitcher'));
    $smarty->assign("HOST", $_SERVER['HTTP_HOST']);
    $smarty->assign("CID", _CID_);
    $smarty->assign("STYLES", $STYLE_SERVICE->getAvailableStyles());
    $smarty->assign("STYLE_SELECT", createNamedSelectBox('style', $STYLE_SERVICE->getAvailableStyles(), $GLOBALS['_BIGACE']['style']['class']->getName(), 'changeLayout()'));
    $smarty->assign('MENUS', $menus);
    $smarty->assign('tips', $hints);
    $smarty->assign('error', $errors);
    $smarty->display('Dashboard.tpl');
    unset($smarty);

    admin_footer();
 