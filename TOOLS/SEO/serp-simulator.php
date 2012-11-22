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
 * For further information visit http://www.bigace.de.
 */


/**
 * A rip-off from the SEO Sitemap Plugin.
 * Displays everything like Google would do (the HTML output is not the same!).
 * You can check your on-page optimization with this tool!
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 */


// ---------------------------- [CONFIGURATION] --------------------------------
define('SERPSIM_INCLUDE_HIDDEN',         true);              // Default: false.  Include hidden pages in the sitemap (true) or skip them (false).
define('SERPSIM_INCLUDE_HIDDEN_SUBTREE', true);              // Default: true.   Include child pages of a hidden parent (true) or skip children of a hidden parent (false).
define('SERPSIM_INCLUDE_REDIRECT',       false);             // Default: false.  Include pages that use the redirect template (true) or exclude them (false).
                                                        //                  If you choose true, check your Google Account, they might not like these redirect pages in sitemaps.
define('SERPSIM_START_ID',               -1);                // Default: -1.     The ID of the page to start with.
define('TOPLEVEL_UNIQUE_URL',       false);             // Default: false.  Whether top level should show its unique url (true) or the root path only (false). 
                                                        //                  Only applies when SERPSIM_START_ID == -1
define('SERPSIM_LANGUAGES_ALL',          true);              // Default: true.   Include all available languages (true) or only the default language (false).
define('SERPSIM_LANGUAGES_CHOOSEN',      '');                // Default: ''      Comma separated list of languages to be included in the sitemap (e.g. 'en,de')).
define('SERPSIM_DOWN_TO_LEVEL',          10);                // Default: 10.     How many level in the menu tree will be included in the sitemap.
define('SERPSIM_TITLE_LENGTH',           80);                // Default: 80.     Length of displayed menu titles.
define('SERPSIM_DESCRIPTION_LENGTH',     160);               // Default: 160.    Length of displayed menu descriptions.
// -----------------------------------------------------------------------------

// initialize bigace session
include_once(dirname(__FILE__) . '/system/libs/init_session.inc.php');

// load required classes
import('classes.item.ItemRequest');
import('classes.item.SimpleItemTreeWalker');
import('classes.menu.Menu');
import('classes.language.LanguageEnumeration');


function getNavi($id, $lang, $level)
{
	$ir = new ItemRequest(_BIGACE_ITEM_MENU);

	if(SERPSIM_INCLUDE_HIDDEN || SERPSIM_INCLUDE_HIDDEN_SUBTREE)
		$ir->setFlagToExclude($ir->FLAG_ALL_EXCEPT_TRASH);

	$ir->setID($id);
		
	if(!is_null($lang))
		$ir->setLanguageID($lang);
		
	$menu_info = new SimpleItemTreeWalker($ir);
	
	for($i=0; $i < $menu_info->count(); $i++)
	{
		$temp = $menu_info->next();

	    if((!$temp->isHidden() || SERPSIM_INCLUDE_HIDDEN) && ($temp->getLayoutName() != 'BIGACE-REDIRECT' || SERPSIM_INCLUDE_REDIRECT))
	    {
    		showItemXML($temp);
        }
        else
        {
            $GLOBALS['skipCounter']++;
        }

        // display child pages if item is not hidden, or we configured to show children of hidden pages
	    if(!$temp->isHidden() || SERPSIM_INCLUDE_HIDDEN_SUBTREE) 
        {
            // if there are still level left, recurse them    	 
		    if($level > 0) {
	       	    getNavi($temp->getID(), $lang, ($level-1));
	        }
	    }
	}
}

function showItemXML($item, $link = null) 
{
	if(is_null($link)) {
		$link = LinkHelper::getCMSLinkFromItem($item);
	}
		
    $GLOBALS['pageCounter']++;
    
    $url = LinkHelper::getUrlFromCMSLink( $link );
    
    $values = array(
        'description'   => $item->getDescription(),
        'title'         => $item->getName(),
        'url'           => $url
    );
    
    $values = Hooks::apply_filters('metatags', $values, $item);
    
    echo '<div class="entry">';
    echo '    <h3><a href="'.substr($values['url'], 0, SERPSIM_TITLE_LENGTH).'">'.$values['title'].'</a></h3>';
    echo '    <div class="s">'.substr($values['description'], 0, SERPSIM_DESCRIPTION_LENGTH).'</div>';
    echo '    <div class="url">'.str_replace('http://', '', $values['url']).'</div>';
    echo "</div>\n";
}

// -----------------------------------------------------------------------------
// START OUTPUT
// -----------------------------------------------------------------------------

header( "Content-Type:text/html; charset=UTF-8" );

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>BIGACE - SERP Simulator</title>
    <meta name="robots" value="noindex"/>
    <style type="text/css">
    body,h3,div {font-size:12px;font-family:Verdana,Arial;}
    h1 {font-size:20px;font-family:Verdana,Arial;border-top:4px solid #000;border-bottom:1px solid #000;margin-top:30px;}
    h1.intro {border-width:0px;}
    h3 {margin:0px;font-size:16px;font-weight:normal;}
    h3 a {margin:0px;font-size:16px;font-weight:normal;}
    .entry {margin-top:10px;}
    #footer {padding-top:20px;margin-top:20px;border-top:1px solid blue;}
    .url {color:green;}
    </style>
</head>
<body>
<h1 class="intro">Google SERP Simulator</h1>
<p>Display your pages like a site search in Google would do. This script is just an experience, to give you a feeling of your on-page-optimization state.</p>
<p>It is known to display results not in the correct way currently: Line break for description and real word splitting is missing. If you feel dedicated,
improve this script and leave a reply with your code in the <a href="http://forum.bigace.de">BIGACE Forum</a>!</p>
<?php	   

$languages = array();
$pageCounter = 0;
$skipCounter = 0;

if(SERPSIM_LANGUAGES_ALL)
{
    // fetch all available languages 
    $enum = new LanguageEnumeration();
    for($i = 0; $i < $enum->count(); $i++) {
    	$lang = $enum->next();
    	$languages[] = $lang->getLanguageID();
    }
}
else
{
    if(strlen(SERPSIM_LANGUAGES_CHOOSEN) > 0) 
    {
        $languages = explode(",",SERPSIM_LANGUAGES_CHOOSEN);
    }
    else
    {
        // only default language
        $languages[] = $GLOBALS['_BIGACE']['DEFAULT_LANGUAGE'];
    }
}

foreach($languages AS $locale)
{
	$topLevel = new Menu(SERPSIM_START_ID, ITEM_LOAD_FULL, $locale);

	// if this menu really exist, display it and its subtree 
	if($topLevel->exists()) 
	{
	    if(!$topLevel->isHidden() || SERPSIM_INCLUDE_HIDDEN || SERPSIM_INCLUDE_HIDDEN_SUBTREE) 
	    {

		    echo "<h1>Language: ".$locale." </h1>\n";
		    
		    if((!$topLevel->isHidden() || SERPSIM_INCLUDE_HIDDEN) && ($topLevel->getLayoutName() != 'BIGACE-REDIRECT' || SERPSIM_INCLUDE_REDIRECT))
		    {
		        if(SERPSIM_START_ID != _BIGACE_TOP_LEVEL || TOPLEVEL_UNIQUE_URL) {
		            showItemXML($topLevel);
		        }
		        else
		        {
		            // display top level with domain only, do not append the unique url
		            $link = LinkHelper::getCMSLinkFromItem($topLevel);
		            $link->setUniqueName("/");
		            showItemXML($topLevel,$link);
		        }
		    }
            else
            {
                $GLOBALS['skipCounter']++;
            }
	
	        if(!$topLevel->isHidden() || SERPSIM_INCLUDE_HIDDEN_SUBTREE) 
            {
    		    getNavi($topLevel->getID(), $locale, SERPSIM_DOWN_TO_LEVEL);
    		}
        }
	}
}

echo '<div id="footer">Included pages: '.$pageCounter.'<br/>'."\n" . 'Skipped pages: '.$skipCounter.'</div>'."\n";

?>
</body>
</html>
