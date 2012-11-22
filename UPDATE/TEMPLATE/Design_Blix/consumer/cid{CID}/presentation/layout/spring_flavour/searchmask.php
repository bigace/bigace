<?php

/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst.
 * -------------------------------------------------
 * The BLIX Layout for BIGACE.
 * 
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 */

/**
 * Parameter:
 * - langid
 * - limit
 * - search
 *
 * Translations:
 * - msg_no_result
 * - search_results
 * - amount_results
 * - msg_empty_search
 * - search_title
 * - search_for
 * - search_language
 * - limit_results
 * - search
 */

import('classes.search.ItemSearch');
import('classes.util.html.FormularHelper');

define('SEARCH_PARAM_LANGUAGE',     'langid');
define('SEARCH_PARAM_AMOUNT',       'limit');
define('SEARCH_PARAM_SEARCHTERM',   'search');

$sessionLang    = _ULC_;
$searchLanguage = (isset($_POST[SEARCH_PARAM_LANGUAGE]))     ? $_POST[SEARCH_PARAM_LANGUAGE]  : $sessionLang;
$searchLimit    = (isset($_POST[SEARCH_PARAM_AMOUNT]))       ? $_POST[SEARCH_PARAM_AMOUNT]    : 10;
$searchTerm     = (isset($_POST[SEARCH_PARAM_SEARCHTERM]))   ? $_POST[SEARCH_PARAM_SEARCHTERM]: '';


if ($GLOBALS['_BIGACE']['PARSER']->getSubAction() == 'searchresult')
{
    $results = array();

    // Only search if Search Term is not empty
    if ($searchTerm != '') 
    {
        
        $searcher = new ItemSearch(_BIGACE_ITEM_MENU, SEARCH_LANGUAGE_INDEPENDENT);
        if ($searchLanguage != SEARCH_LANGUAGE_INDEPENDENT) {
            $searcher->setSearchLanguageID( $searchLanguage );
        }
        $searcher->setLimit( $searchLimit );
        $searcher->addResultColumn('description');
        $results = $searcher->search($searchTerm);

        if (count($results) < 1) {
            echo '<p><b>' . getTranslation('msg_no_result','Could not find any Page matching your Search Phrase.') . '</b></p>';
        } 
        else
        {
            ?>
            <fieldset>
                <legend><?php echo getTranslation('search_results', 'Your search for ').' "'.$searchTerm.'"'; ?></legend>
                <table border="0" width="100%">
                    <tr>
                        <td colspan="2"><i>...<?php echo sprintf(getTranslation('amount_results', 'found %s results.'), count($results)); ?></i></td>
                    </tr>
                <?php
                
                for ($i = 0; $i < count($results); $i++) 
                {
                    $res = $results[$i];
                    $link  = '<a href="' . createMenuLink( $res->getResultColumn('id') ) . '">' . $res->getResultColumn('name') . '</a>';
                    echo '<tr><td width="50%">' . $link . '</td><td>' . $res->getResultColumn('description') . '</td></tr>';
                }
                ?>
                </table>
            </fieldset>
            <?php
        }
            

    } 
    else {
            echo '<p><b>' . getTranslation('msg_empty_search','Search Term is empty!<br>Please submit any non empty Phrase.') . '</b></p>';
    }

}

// ----------------------------------------------------------------------------------
// --------------------------- [START] Search input mask ----------------------------
$cres = array( '5' => '5', '10' => '10', '15' => '15', '20' => '20');
$lang = array( 
               'Aktuelle ('.$sessionLang.')'    => $sessionLang,
               'Alle'           => '-1' 
);

$config = array(
            'width'         =>  '100%',
            'image'         => 'search.gif',
            'align'         =>  array (
                                    'table'     =>  'center',
                                    'left'      =>  'left'
                                ),
            'title'         =>  '<b>'.getTranslation('search_title', 'BIGACE Search').'</b>',
            'form_action'   =>  createMenuLink( $MENU->getID() . '_tBLIX_ksearchresult' ),
            'form_method'   =>  'post',
            'form_hidden'   =>  array(),
            'entries'       =>  array(
                                    getTranslation('search_for', 'Search for')    => createNamedTextInputType('search', $searchTerm, 100),
                                    getTranslation('search_language', 'Language') => createNamedSelectBox(SEARCH_PARAM_LANGUAGE, $lang, $searchLanguage),
                                    getTranslation('limit_results', 'Results')    => createNamedSelectBox(SEARCH_PARAM_AMOUNT, $cres, $searchLimit)
                            ),
            'form_submit'   =>  true,
            'submit_label'  =>  getTranslation('search', 'Search')
);
echo '<br />' . createTable($config);
// ---------------------------- [STOP] Search input mask ----------------------------
// ----------------------------------------------------------------------------------

if(_BLIX_SEARCH_WITH_GOOLGE)
{
    // ----------------------------------------------------------------------------------
    // --------------------------- [START] Google mask ----------------------------
    $config = array(
                'width'         =>  '100%',
                'image'         => 'search.gif',
                'align'         =>  array (
                                        'table'     =>  'center',
                                        'left'      =>  'left'
                                    ),
                'title'         =>  '<b>Mit Google suchen</b>',
                'form_action'   =>  'http://www.google.de/search',
                'form_method'   =>  'get',
                'form_hidden'   =>  array( 'sitesearch' => $_SERVER['HTTP_HOST'] ),
                'entries'       =>  array(
                                        '<img border="0" align="middle" alt="Google" src="http://www.google.de/logos/Logo_25wht.gif"/>' => createNamedTextInputType('q', $searchTerm, 100)
                                ),
                'form_submit'   =>  true,
                'form_tarbet'   =>  '_blank',
                'submit_label'  =>  'Google-Suche'
    );
    echo '<br />' . createTable($config);
    
    // ---------------------------- [STOP] Google mask ----------------------------
    // ----------------------------------------------------------------------------------
}

?>