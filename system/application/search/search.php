<?php
/**
 * $Id$
 */

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

/**
 * Search Parameter:
 * - itemtype 	(default: 1)
 * - limit		(default: 5)
 * - search		(default: "")
 * - language	(default: SEARCH_LANGUAGE_INDEPENDENT)
 */
import('classes.item.Itemtype');
import('classes.util.IOHelper');
import('classes.util.LinkHelper');
import('classes.util.CMSLink');
import('classes.util.ApplicationLinks');
import('classes.menu.MenuService');
import('classes.search.ItemSearch');
import('classes.util.html.FormularHelper');

import('classes.smarty.BigaceSmarty');
import('classes.smarty.SmartyTemplate');

define('DEFAULT_LIMIT', 0);
define('DEFAULT_LANGUAGE', SEARCH_LANGUAGE_INDEPENDENT);
define('DEFAULT_ITEMTYPE', _BIGACE_ITEM_MENU);
define('DEFAULT_SEARCHTERM', '');

loadLanguageFile('search');

    // -----------------------------------------------------------------
    // definition of functions and classes
    // -----------------------------------------------------------------
    function dontTrustSearchterm($str) {
        $str = str_replace('"', '&quot;', $str);
        return addslashes($str);
    }

    function prepareSearchInput($str) {
        return stripslashes(stripslashes(str_replace('"', '&quot;', $str)));
    }

    function search($data)
    {
        $langid = $data['language'];
        $search = $data['search'];
        $limit    = $data['limit'];
        $itemtype = $data['itemtype'];

        $results = array();

        $columns = array(
        	'id'			=> 'id',
        	'name'			=> 'name',
        	'description'	=> 'description',
        	'language'		=> 'language',
        	'unique_name'	=> 'unique_name',
        	'content'		=> 'text_5',
        	'filename'		=> 'text_1'
        );

        // Only search if Search Term is not empty
        if ($search != '')
        {
            $searcher = new ItemSearch($itemtype, $langid);
            //$searcher->setSearchLanguageID( $langid );
            if($limit > 0)
            	$searcher->setLimit( $limit );

            foreach($columns AS $key => $value)
            	$searcher->addResultColumn($value);

            $searchResult = $searcher->search( dontTrustSearchterm($search) );

            $realItemtype = new Itemtype($itemtype);

            if (count($searchResult) > 0)
            {
                for ($i = 0; $i < count($searchResult); $i++)
                {
                	$resTemp = array();

                	foreach($columns AS $key => $value) {
                		$resTemp[$key] = $searchResult[$i]->getResultColumn($value);
                	}

			        $resultLink = new CMSLink();
			        $resultLink->setCommand($realItemtype->getCommand());
			        $resultLink->setItemID($resTemp['id']);
			        $resultLink->setLanguageID($resTemp['language']);
			        $resultLink->setUniqueName($resTemp['unique_name']);
			        if($realItemtype->getItemtypeID() != _BIGACE_ITEM_MENU)
			        	$resultLink->setFilename($resTemp['filename']);

			        $resTemp['url'] 		= LinkHelper::getUrlFromCMSLink($resultLink);
			        $resTemp['mimetype'] 	= getFileExtension($resTemp['filename']);

                    $results[] = $resTemp;
                }
            }

            $searcher->finalize();
        }

	    return $results;
    }

    // -----------------------------------------------------------------
    // html code and simple runtime logic follows
    // -----------------------------------------------------------------

    $data = array(
    	'itemtype' 	=> extractVar( 'itemtype', 	DEFAULT_ITEMTYPE), 	 // the itemtype to be searched
    	'limit' 	=> extractVar( 'limit', 	DEFAULT_LIMIT),		 // how many search results should be fetched
    	'search' 	=> extractVar( 'search', 	DEFAULT_SEARCHTERM), // the users searchterm
    	'language' 	=> extractVar( 'language', 	DEFAULT_LANGUAGE),	 // the language to be searched in
        'showInput' => extractVar( 'form',      'true')
    );
    $data['search'] = trim( $data['search'] );

    $LANGUAGE = new Language($GLOBALS['_BIGACE']['PARSER']->getLanguage());
	header("Content-Type:text/html; charset=" . $LANGUAGE->getCharset());

    $MENU_SERVICE = new MenuService();
	$MENU = $MENU_SERVICE->getMenu( $GLOBALS['_BIGACE']['PARSER']->getItemID(), $GLOBALS['_BIGACE']['PARSER']->getLanguage() );

	$tpl_head = new SmartyTemplate( ConfigurationReader::getConfigurationValue("templates", "application.header", "APPLICATIONS-HEADER") );
	$tpl_foot = new SmartyTemplate( ConfigurationReader::getConfigurationValue("templates", "application.footer", "APPLICATIONS-FOOTER") );

    $smarty = BigaceSmarty::getSmarty();

    $data['language'] = substr(dontTrustSearchterm($data['language']), 0, 2);
    $data['search']   = dontTrustSearchterm($data['search']);
    $data['limit']    = intval($data['limit']);

	$smarty->assign('CSS', _BIGACE_DIR_PUBLIC_WEB.'system/css/search.css');
    $smarty->assign('TPL_HEADER', 	$tpl_head->getFilename());
	$smarty->assign('TPL_FOOTER', 	$tpl_foot->getFilename());
	$smarty->assign('MENU', 		$MENU);
    $smarty->assign('USER', 		$GLOBALS['_BIGACE']['SESSION']->getUser());
	$smarty->assign('TITLE', 		getTranslation('search_frame','Search'));
	$smarty->assign('CHARSET', 		$LANGUAGE->getCharset());
	$smarty->assign('ACTION_URL', 	ApplicationLinks::getSearchURL($GLOBALS['_BIGACE']['PARSER']->getItemID()));
	$smarty->assign('itemtype', 	$data['itemtype']);
	$smarty->assign('searchTerm', 	$data['search']);
	$smarty->assign('limit', 		$data['limit']);
	$smarty->assign('language', 	$data['language']);
	$smarty->assign('SHOW_FORM', 	(strcasecmp($data['showInput'], 'true') == 0));

	$tpl = new SmartyTemplate( ConfigurationReader::getConfigurationValue("search", "template", "APPLICATION-SEARCH") );

    // if user submitted the search formular, perform the search!
	if (isset($_POST['search']) || isset($_GET['search']))
	{
		$results = search($data);
		$smarty->assign('results', $results);
	}

    $smarty->display( $tpl->getFilename() );
