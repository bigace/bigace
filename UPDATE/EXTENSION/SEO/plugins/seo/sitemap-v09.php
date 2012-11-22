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
 */


/**
 * Dumps a XML Sitemap in 0.9 version style. 
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.addon
 * @subpackage seo
 */


// ---------------------------- [CONFIGURATION] --------------------------------
define('SM_INCLUDE_HIDDEN',         true);              // Default: false.  Include hidden pages in the sitemap (true) or skip them (false).
define('SM_INCLUDE_HIDDEN_SUBTREE', true);              // Default: true.   Include child pages of a hidden parent (true) or skip children of a hidden parent (false).
define('SM_INCLUDE_REDIRECT',       false);             // Default: false.  Include pages that use the redirect template (true) or exclude them (false).
                                                        //                  If you choose true, check your Google Account, they might not like these redirect pages in sitemaps.
define('SM_START_ID',               -1);                // Default: -1.     The ID of the page to start with.
define('TOPLEVEL_UNIQUE_URL',       false);             // Default: false.  Whether top level should show its unique url (true) or the root path only (false). 
                                                        //                  Only applies when SM_START_ID == -1
define('SM_LANGUAGES_ALL',          true);              // Default: true.   Include all available languages (true) or only the default language (false).
define('SM_LANGUAGES_CHOOSEN',      '');                // Default: ''      Comma separated list of languages to be included in the sitemap (e.g. 'en,de')).
define('SM_DOWN_TO_LEVEL',          10);                // Default: 10.     How many level in the menu tree will be included in the sitemap.
define('SM_DEBUG',          		false);             // Default: false.  Whether to show some debug information in XML compatible comments.
// -----------------------------------------------------------------------------


// ----------------------------- [    TODO    ] --------------------------------
// 1. add permission check for the toplevel item
// -----------------------------------------------------------------------------


// initialize bigace session
include_once(dirname(__FILE__) . '/../../system/libs/init_session.inc.php');

// load required classes
import('classes.item.ItemRequest');
import('classes.item.SimpleItemTreeWalker');
import('classes.menu.Menu');
import('classes.language.LanguageEnumeration');

// xml http header
header("Content-type: text/xml");

// sitemap header
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

$languages = array();
$pageCounter = 0;
$skipCounter = 0;

if(SM_LANGUAGES_ALL)
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
    if(strlen(SM_LANGUAGES_CHOOSEN) > 0) 
    {
        $languages = explode(",",SM_LANGUAGES_CHOOSEN);
    }
    else
    {
        // only default language
        $languages[] = $GLOBALS['_BIGACE']['DEFAULT_LANGUAGE'];
    }
}

foreach($languages AS $locale)
{
	$topLevel = new Menu(SM_START_ID, ITEM_LOAD_FULL, $locale);

	// if this menu really exist, display it and its subtree 
	if($topLevel->exists()) 
	{
	    if(!$topLevel->isHidden() || SM_INCLUDE_HIDDEN || SM_INCLUDE_HIDDEN_SUBTREE) 
	    {
			if(SM_DEBUG)
		    	echo "<!-- Language: ".$locale." -->\n";
		    
		    if((!$topLevel->isHidden() || SM_INCLUDE_HIDDEN) && ($topLevel->getLayoutName() != 'BIGACE-REDIRECT' || SM_INCLUDE_REDIRECT))
		    {
		        if(SM_START_ID != _BIGACE_TOP_LEVEL || TOPLEVEL_UNIQUE_URL) {
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
	
            // TODO: don't we want to display toplevel children always?
	        if(!$topLevel->isHidden() || SM_INCLUDE_HIDDEN_SUBTREE) 
            {
    		    getNavi($topLevel->getID(), $locale, SM_DOWN_TO_LEVEL);
    		}
        }
	}
}

if(SM_DEBUG) {
	echo '<!-- Sitemap pages: '.$pageCounter.' -->'."\n";
	echo '<!-- Skipped pages: '.$skipCounter.' -->'."\n";
}

// sitemap footer
echo "\n".'</urlset>';

function getNavi($id, $lang, $level)
{
	$ir = new ItemRequest(_BIGACE_ITEM_MENU);

	if(SM_INCLUDE_HIDDEN || SM_INCLUDE_HIDDEN_SUBTREE)
		$ir->setFlagToExclude($ir->FLAG_ALL_EXCEPT_TRASH);

	$ir->setID($id);
		
	if(!is_null($lang))
		$ir->setLanguageID($lang);
		
	$menu_info = new SimpleItemTreeWalker($ir);
	
	for($i=0; $i < $menu_info->count(); $i++)
	{
		$temp = $menu_info->next();

	    if((!$temp->isHidden() || SM_INCLUDE_HIDDEN) && ($temp->getLayoutName() != 'BIGACE-REDIRECT' || SM_INCLUDE_REDIRECT))
	    {
    		showItemXML($temp);
        }
        else
        {
            $GLOBALS['skipCounter']++;
        }

        // display child pages if item is not hidden, or we configured to show children of hidden pages
	    if(!$temp->isHidden() || SM_INCLUDE_HIDDEN_SUBTREE) 
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
?>
  <url>
    <loc><?php echo LinkHelper::getUrlFromCMSLink( $link ); ?></loc>
    <lastmod><?php echo date("Y-m-d", $item->getLastDate()); ?></lastmod>
  </url>
<?php
}

?>