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
 * Fetch the rss feed to display the latest BIGACE related news.
 */

check_admin_login();
loadLanguageFile('index', ADMIN_LANGUAGE);

if(isset($_GET['rss']) && ConfigurationReader::getConfigurationValue('admin', 'display.latest.news', true))
{

    $t = '<a href="'."%1\$s".'" target="_blank">'.getTranslation('no_incoming_links_title').'</a>
          <span>'."%2\$s".'</span><p>'.getTranslation('no_incoming_links_msg').'</p>';
    $empty2 = sprintf($t, "http://forum.bigace.de/cms-showcase/", date('F d, Y'));

    $feeds = array(
        array(
            'url'    => 'http://feeds2.feedburner.com/Bigace-Admin',
            'amount' => '4',
            'empty'  => 'Sorry, this service is currently unavailable. Please come back soon!',
        ),
        array(
            'url'    => 'http://blogsearch.google.com/blogsearch_feeds?hl=en&scoring=d&ie=utf-8&num=10&output=rss&q=link:'.BIGACE_HOME,
            'amount' => '10',
            'empty'  => $empty2,
        ),
    );
    
    $url = null;
    $amount = 10;
    
    if (isset($feeds[$_GET['rss']])) {
        $url = $feeds[$_GET['rss']]['url'];
        $amount = $feeds[$_GET['rss']]['amount'];
        $empty = $feeds[$_GET['rss']]['empty'];
    }
        
    if(!is_null($url))
    {
        define('MAGPIE_DIR', _BIGACE_DIR_ADDON.'magpierss/');
        define('MAGPIE_CACHE_ON', 2);
        define('MAGPIE_CACHE_DIR', $GLOBALS['_BIGACE']['DIR']['cache']);
        define('MAGPIE_CACHE_AGE', 43200); // 12 hours
        define('MAGPIE_OUTPUT_ENCODING','UTF-8');
        
        require_once(MAGPIE_DIR.'rss_fetch.inc');
        $cache = new RSSCache( MAGPIE_CACHE_DIR, MAGPIE_CACHE_AGE );

        $rss = fetch_rss($url);
        if($rss !== false) 
        {
            if(count($rss->items) == 0) {
                echo $empty;
            }
            else {
                $items = array_slice($rss->items, 0, $amount);
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
                    echo '<a href="'.$item['link'].'" target="_blank">' . $item['title'].'</a>'.$myDate.'
                                <p>' . $item['description'].'</p>';
                }
            }
        }
        else {
              // TODO translate
              echo '<a href="http://wiki.bigace.de/bigace:manual:errors" target="_blank">ERROR: Feed could not be loaded!</a><span>'.date('F d, Y').'</span>
                        <p>Please follow the link to our wiki, to find out why this error message appears, how to fix the problem or how to turn off the feeds.<br/>
                        <b>Reason: '.magpie_error().'</b></p>';
        }
    }
    else
    {
        // TODO translate
        echo '<a href="http://wiki.bigace.de/bigace:manual:errors" target="_blank">ERROR: Feed could not be loaded.!</a><span>'.date('F d, Y').'</span>
                    <p>Please follow the link to our wiki, to find out why this error message appears, how to fix the problem or how to turn off the feeds.<br/>
                    <b>Reason: You requested a not existing feed.</b></p>';
    }
}
