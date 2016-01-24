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
 * Script for reading Statistics
 */

check_admin_login();
admin_header();

import('classes.statistic.StatisticService');
import('classes.statistic.StatisticConnection');

define('_TABLE_STATISTIC', 	    '{DB_PREFIX}statistics');
define('_STAT_DISPLAY_FLASH',   false);
define('_PARAM_STAT_MODE', 		'mode');

define('_MODE_INDEX', 		'index');
define('_MODE_LAST7', 		'last7');
define('_MODE_OS', 			'os');
define('_MODE_BROWSER', 	'browser');
define('_MODE_VISITOR', 	'visitors');
define('_MODE_REFERER', 	'references');
define('_MODE_YEAR', 	    'byYear');
define('_MODE_URL', 	    'byUrl');
define('_MODE_BOTS', 	    'bots');

// -------------------------------------------------
// Some style information
define('_COLOR_1',         'red');          // Windows Color
define('_COLOR_2',         'blue');         // Other Color
define('_COLOR_BG',        '#C0C0C0');      // Background color for the value bar
define('_TABLE_STYLE',     'border:1px solid #000000');
define('_SPACER',          'width:40px');
// -------------------------------------------------

/**
 * Definitions for the Statistics Plugin.
 * Here the most common values like Browser Types, Search Engine names ...
 * can be found and customized!
 * To enhance Statistics simply add new Entrys to the following Arrays.
 */

    class StatisticType {
        
        var $name = '';
        var $definition;
        
        function StatisticType($name,$definition) {
            $this->name = $name;
            $this->definition = $definition;
        }
        
        function getName() {
            return $this->name;
        }

        function getDefinition() {
            return $this->definition;
        }
    }

    class BotStatisticType extends StatisticType {
        
        var $link;

        function BotStatisticType($name,$definition, $link = '') {
            $this->StatisticType($name, $definition);
            $this->link = $link;
        }
        
        function getLink() {
            return $this->link;
        }

    }

    /*
     * Defines which Links and Commands should NOT be taken for Statistics.
     * TODO change links unused for statistics 
     */
    $_FILTER = array();
    $_FILTER['LINKS']   = array('/stats/', '_tERROR', '_tADMIN', '_tCMD', '/logout/', '/login/', '/editor/');
    $_FILTER['COMMANDS']= array('stats', 'login', 'editor', 'logout', 'admin', 'download');

    /*
     * Definition of Operating Systems and how they might be identified. 
     */
	$_OS = array();
	$_OS['win']         = new StatisticType('Windows',          'win');
	$_OS['mac']         = new StatisticType('Macintosh',        'mac');
	$_OS['linux']       = new StatisticType('Linux',            'linux');
	$_OS['unix']        = new StatisticType('Unix',             array('unix','SunOS','FreeBSD','IRIX','HP-UX','OSF','AIX'));
	$_OS['craw']        = new StatisticType('Search Engines',   array('spider','bot'));
	
    /*
     * Definition of Browser and how they might be identified. 
     */
	$_BROWSER = array();
	$_BROWSER['msie']   = new StatisticType('Internet Explorer',    'MSIE');
	$_BROWSER['ffox']   = new StatisticType('Firefox',              'Firefox');
	$_BROWSER['oper']   = new StatisticType('Opera',                'Opera');
	$_BROWSER['icab']   = new StatisticType('iCab',                 'iCab');
	$_BROWSER['lynx']   = new StatisticType('Lynx',                 'Lynx');
	$_BROWSER['konq']   = new StatisticType('Konqueror',            'Konqueror');

    /*
     * Definition of Bots/Search Engines and how they might be identified. 
     */
	$_BOTS = array();
	$_BOTS['yahoo']     = new BotStatisticType('Yahoo (Slurp)',         'Slurp',        'http://help.yahoo.com/help/us/ysearch/slurp');     // Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)
	$_BOTS['google']    = new BotStatisticType('Google (Googlebot)',    'Googlebot',    'http://www.google.com/bot.html');                  // Googlebot/2.1 (+http://www.google.com/bot.html)
	$_BOTS['msn']       = new BotStatisticType('MSN (Microsoft) Bot',   'msnbot',       'http://search.msn.com/msnbot.htm');                // msnbot/1.0 (+http://search.msn.com/msnbot.htm)
	$_BOTS['seekbot']   = new BotStatisticType('SeekBot',               'seekbot',      'http://www.seekbot.net/bot.html');                 // Seekbot/1.0 (http://www.seekbot.net/bot.html) HTTPFetcher/0.3
	$_BOTS['mirago']    = new BotStatisticType('HeinrichderMiragoRobot', 'miragorobot', 'http://www.miragorobot.com/scripts/deinfo.asp');   // HeinrichderMiragoRobot (http://www.miragorobot.com/scripts/deinfo.asp)
	$_BOTS['walhello']  = new BotStatisticType('Walhello (appie)',      'walhello',     'http://www.walhello.com');                         // appie 1.1 (www.walhello.com)
	$_BOTS['girafa']    = new BotStatisticType('girafa',                'Girafabot',    'http://www.girafa.com');                           // Mozilla/4.0 (compatible; MSIE 5.0; Windows NT; Girafabot; girafabot at girafa dot com; http://www.girafa.com)
	$_BOTS['turnitin']  = new BotStatisticType('Turnitin',              'TurnitinBot',  'http://www.turnitin.com/robot/crawlerinfo.html');  // TurnitinBot/2.0 http://www.turnitin.com/robot/crawlerinfo.html
	$_BOTS['teoma']     = new BotStatisticType('Teoma',                 'Jeeves/Teoma', 'http://sp.ask.com/docs/about/tech_crawling.html'); // Mozilla/2.0 (compatible; Ask Jeeves/Teoma; +http://sp.ask.com/docs/about/tech_crawling.html)
	$_BOTS['almaden']   = new BotStatisticType('Almaden',               'almaden',      'http://www.almaden.ibm.com/cs/crawler');           // http://www.almaden.ibm.com/cs/crawler   [bc21]
	$_BOTS['zyborg']    = new BotStatisticType('ZyBorg - Dead Link Checker', 'ZyBorg',  'http://www.WISEnutbot.com');                       // Mozilla/4.0 compatible ZyBorg/1.0 Dead Link Checker (wn.dlc@looksmart.net; http://www.WISEnutbot.com)
	$_BOTS['exabot']    = new BotStatisticType('Exabot',                'Exabot', '');                                                      // Exabot NG/MimeLive Client (convert/http/0.173) 
	$_BOTS['whois']     = new BotStatisticType('Whois Source',          'SurveyBot', '');                                                   // SurveyBot/2.3 (Whois Source)
	$_BOTS['mmcrawler'] = new BotStatisticType('Yahoo-MMCrawler',       'MMCrawler', '');                                                   // Yahoo-MMCrawler/3.x (mms dash mmcrawler dash support at yahoo dash inc dot com)

/**
 * -------------------------------
 * END - Defintions
 * -------------------------------
 */

$_MODES = array(
                _MODE_INDEX     => 'stats_mode_index',
                _MODE_LAST7     => 'stats_mode_last7',
                _MODE_OS        => 'stats_mode_os',
                _MODE_BROWSER   => 'stats_mode_browser',
                _MODE_BOTS      => 'stats_mode_bots',
                _MODE_VISITOR   => 'stats_mode_visitor',
                _MODE_REFERER   => 'stats_mode_references',
                _MODE_YEAR      => 'stats_mode_by_year',
                _MODE_URL       => 'stats_mode_by_url',
          );


function graphic($a, $b, $desc)
{
    $blueval     = $a[0];
    $blueNum     = $a[1];
	$bluePercent = round($blueval*100, 1);
	$blue        = ($blueval * 200);

    $redval      = $b[0];
    $redNum      = $b[1];
	$redPercent  = round($redval*100, 1);
	$red         = ($redval * 200);

	$total       = ($red+$blue);

	echo "\n".'<table width="100%">'."\n";
	echo '<tr>';
	/*
	echo '<td align="right">100.0%</td>';
	echo '<td><img src="'._BIGACE_DIR_PUBLIC_WEB.'system/images/empty.gif" height="5" width="'.$total.'" style="background-color:'._COLOR_0.'"></td>';
	echo '<td align="right"></td>';
	echo '</tr><tr>';
	*/
	echo '<td align="right">'.$redPercent.'%</td>';
	echo '<td>';
	echo getScaledImageHtml($red,_COLOR_1);
	echo getScaledImageHtml(($total-$red),_COLOR_BG);
	echo '</td>';
	echo '<td align="right">' . $blueNum . ' ' . $desc . '</td>';
	echo '</tr>'."\n".'<tr>';
	echo '<td align="right">'.$bluePercent.'%</td>';
	echo '<td>';
	echo getScaledImageHtml($blue,_COLOR_2);
	echo getScaledImageHtml(($total-$blue),_COLOR_BG);
	echo '</td>';
	echo '<td align="right">' . $redNum . ' ' . $desc . '</td>';
	echo '</tr></table>'."\n";
}

function getScaledImageHtml($width, $color) {
	return '<img src="'._BIGACE_DIR_PUBLIC_WEB.'system/images/empty.gif" height="5" width="'.$width.'" style="background-color:'.$color.'">';
}

function getScaleImage($total, $value) {
    $percent = 100 / ($total / $value);
    $length = 450 * $percent / 100;   // 450px ist momentan die standard laenge fuer 100%
    return getScaledImageHtml($length,_COLOR_2) . ' ' . (int)$percent . '%';
}

function createStatisticLink($mode, $params = array())
{
    $params[_PARAM_STAT_MODE] = $mode;
    return createAdminLink($GLOBALS['MENU']->getID(), $params);
}

// display info if statistics are disabled
if(!ConfigurationReader::getConfigurationValue('system', 'write.statistic', false))
{
	displayError( getTranslation('stats_disabled') );
}


$mode = extractVar(_PARAM_STAT_MODE, _MODE_INDEX);

// Generate the Menu
echo "\n".'<div id="darkBackground">'."\n";
    echo '<form name="" action="'.createAdminLink($GLOBALS['MENU']->getID()).'" method="POST">'."\n";
    echo '<a href="'.createAdminLink($GLOBALS['MENU']->getID(), array(_PARAM_STAT_MODE => $mode)).'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'refresh.png" border="0" align="top" alt="RELOAD" /></a>';
    echo ' <select name="'._PARAM_STAT_MODE.'" onChange="this.form.submit()">'."\n";
    foreach ($_MODES AS $tempMode => $tempName)
    {
        echo '<option value="'.$tempMode.'"';
        if ($mode == $tempMode) {
            echo ' selected';
        }
        echo '>'.getTranslation($tempName).'</option>'."\n";
    }
    echo '</select>'."\n";
    echo '&nbsp;&nbsp;';
    echo '<noscript><button type="submit">'.getTranslation('show').'</button></noscript>';
    echo '</form>'."\n";
echo '</div>'."\n";

$conn = new StatisticConnection();
$helper = new SQLHelper($conn);

$STAT_SERVICE = new StatisticService();
$STAT_SERVICE->setSQLHelper($helper);

$seenInclude = FALSE;

// TODO sanitize path
$filename = _ADMIN_INCLUDE_DIRECTORY.'statistics/' . $_MODES[$mode] . '.php';
if (isset($_MODES[$mode])) {
    if ( file_exists($filename) ) {
    	echo '<h2>'.getTranslation($_MODES[$mode]).'</h2>';

        include_once($filename);
        $seenInclude = TRUE;
    }
}

if (!$seenInclude)
{
    displayError('Requested Mode does not exist: ' . $mode . '<br>'.$filename);
}

admin_footer();

?>