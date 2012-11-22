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
 * @subpackage search
 */

import('classes.search.SearchResult');
import('classes.logger.LogEntry');

/**
 * Use this constant for searching in all languages (Language independent).
 *
 * @access public
 */
define('SEARCH_LANGUAGE_INDEPENDENT', -1);

/**
 * This Class is used for searching in Items.
 *
 * The Search is performed within the content (text_5) and the following DB Columns:
 * - name
 * - description
 * - catchwords
 *
 * Following columns are selected by default:
 * - id
 * - name
 * - language
 *
 * By calling <code>addResultColumn($column)</code>, you add columns to your SearchResult.
 * If you - for example - want to show the catchwords of an Item within the result screen,
 * call <code>addResultColumn('catchwords')</code> before executing <code>search('foo')</code>.
 *
 * Make sure the column name to add is existing, otherwise the search will fail
 * with an SQL exception!
 *
 * Hidden and deleted pages are not searched by default.
 * You can <code>resetIgnoreFlags()</code> to search everything or <code>addIgnoreFlag($flag)</code>
 * where $flag is one of <code>FLAG_TRASH</code> and <code>FLAG_HIDDEN</code> to exclude those
 * from the seared items.
 *
 * If you want to perform an language independet search, call
 * <code>$search->setSearchLanguageID(SEARCH_LANGUAGE_INDEPENDENT)</code> or
 * <code>$search->setSearchLanguageID(null)</code> or even simpler, call the constructor
 * without a language <code>new ItemSearch($itemtype, null)</code>.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage search
 */
class ItemSearch
{
    /**
     * Use this constant for searching in all languages (Language independent).
     * @deprecated use the constant SEARCH_LANGUAGE_INDEPENDENT instead.
     * @access public
     */
    var $LANGUAGE_INDEPENDENT   = SEARCH_LANGUAGE_INDEPENDENT;
    private $searchContentOnly  = false;
    private $limit              = 10;
    private $language           = -1;
    private $cols               = array();
    private $itemtypeid         = _BIGACE_ITEM_MENU;

    /**
     * Array of item flags to be excluded from the search.
     * @var array
     */
    public $FLAGS				= array(FLAG_TRASH);

    /**
     * Initialize the ItemSearch with Itemtype and language.
     * @param int the ItemtypeID to search in
     * @param int the LanguageID to search for
     */
    function ItemSearch($itemtype, $language = null)
    {
        $this->init($itemtype, $language);
    }

	/**
     * Resets the list of ignored items.
     */
	function resetIgnoreFlags()
	{
		$this->FLAGS = array();
	}

	/**
     * Adds a flag to the list of ignored items.
     * Possible values: FLAG_TRASH,FLAG_HIDDEN
     */
	function addIgnoreFlag($flag)
	{
		$this->FLAGS[] = $flag;
	}

    /**
     * @access protected
     */
    function init($itemtype, $language = null)
    {
    	if(is_null($language))
        	$this->setSearchLanguageID(SEARCH_LANGUAGE_INDEPENDENT);
    	else
        	$this->setSearchLanguageID($language);

        $this->setItemTypeID($itemtype);

        $hidden = get_option("search", "find.hidden", false);
        if($hidden === false)
            $this->addIgnoreFlag(FLAG_HIDDEN);

        // these will be selected automatically
        //$this->addResultColumn('language');
        //$this->addResultColumn('name');
        //$this->addResultColumn('id');
    }

    /**
     * Sets the Itemtype we search in.
     * @access private
     */
    function setItemTypeID($id)
    {
        $this->itemtypeid = $id;
    }

    /**
     * Return the Itemtype we are searching in.
     * @access private
     * @return int the Itemtype we search in
     */
    function getItemTypeID()
    {
        return $this->itemtypeid;
    }

    /**
     * Sets whether we search in Content only or in all available Columns.
     * @param boolean true to search in Content only, false to search all Columns
     */
    function setSearchContentOnly($bool)
    {
        $this->searchContentOnly = $bool;
    }

    /**
     * Set the Limit of Search Results.
     * @param int a Limit of max. Results
     */
    function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * Set the Start and End Limits of the Serach Results.
     * @param int the Start Value of Results
     * @param int the End Value of Results
     */
    function setLimiter($start, $end)
    {
        $this->limit = $start . ', ' . $end;
    }

    /**
     * Set the Searchable Language.
     * See Class Doc for information about Language independent Search!
     *
     * @param string|integer the LanguageID
     */
    function setSearchLanguageID($id)
    {
        $this->language = $id;
    }

    /**
     * Returns the language used to search.
     *
     * @return string|integer
     */
    function getSearchLanguageID()
    {
        return $this->language;
    }

    /**
     * Get the Additional Columns to be selected for all results.
     * @access private
     */
    function getSelectColumns()
    {
        return $this->cols;
    }

    /**
     * Adds a Column (from the Item table) that will be selected when searching.
     * Make sure the Column exists, otherwise the Search will fail!
     * @param String the Column Name to select
     */
    function addResultColumn($column)
    {
        array_push($this->cols, $column);
    }

    /**
     * Perform the Search with the configured Options.
     * An Array of SearchResult Objects is returned.
     * If no result could be found, an empty array will be returned.
     *
     * @param String the String to search for
     * @return array an array of SearchResult Objects
     */
    function search($searchterm)
    {
        // define search table
        $TABLE      = '';

        // define extended search parameter
        $EXTENSION  = " ";
        if ($this->language != $this->LANGUAGE_INDEPENDENT) {
            $EXTENSION .= " AND a.language='".$this->language."'";
        }

        // Define search limit
        $LIMIT = $this->limit;

        // Define search column
        $SEARCH_COLUMN = 'a.name,a.description,a.catchwords,a.text_5';
        if ($this->searchContentOnly) {
            $SEARCH_COLUMN = 'text_5';
        }

        if(is_array($this->FLAGS) && count($this->FLAGS) > 0) {
            $EXTENSION .= " AND a.num_3 NOT IN (";
            for($i=0; $i < count($this->FLAGS); $i++) {
                $EXTENSION .= " ".$this->FLAGS[$i]." ";
                if($i < count($this->FLAGS)-1)
                	$EXTENSION .= ",";
            }
            $EXTENSION .= ") ";
        }

        // Define selected columns
        $COLUMNS = '';
        $temp_cols = $this->getSelectColumns();
        for ($i=0; $i < count($temp_cols); $i++)
        {
            $COLUMNS .= 'a.'.$temp_cols[$i].',';
        }

        $result = $this->_performSearch($searchterm, $EXTENSION, $COLUMNS, $SEARCH_COLUMN, $LIMIT);

        $le = new LogEntry(LOGGER_LEVEL_INFO,'Found ' . count($result) . ' result(s) for "'.$searchterm.'" in ItemType '.$this->getItemTypeID(),LOGGER_NAMESPACE_SEARCH);
		$GLOBALS['LOGGER']->logEntry($le);

        return $result;
    }

    protected function _performSearch($searchterm, $EXTENSION, $COLUMNS, $SEARCH_COLUMN, $LIMIT)
    {
    	$db = get_db();

        $sqlString = "
	        SELECT DISTINCT a.id, a.language, a.name, a.description, a.catchwords, ".$COLUMNS."
		    MATCH(".$SEARCH_COLUMN.") AGAINST ({CONDITION} IN BOOLEAN MODE)
			AS fulltextmatch
			FROM
			    {DB_PREFIX}item_".$this->getItemTypeID()." a, {DB_PREFIX}group_right b, {DB_PREFIX}user_group_mapping c
			WHERE
				a.cid = {CID}
			 	AND b.itemtype='".$this->getItemTypeID()."' AND b.cid={CID} AND b.itemid=a.id
				AND (c.cid={CID} AND c.userid={USER} AND c.group_id = b.group_id AND b.value > {PERMISSION})
			    ".$EXTENSION."
			AND
			    MATCH(".$SEARCH_COLUMN.") AGAINST ({CONDITION} IN BOOLEAN MODE)
			ORDER BY fulltextmatch DESC
			LIMIT ".$db->escape($LIMIT).";";

        if ($GLOBALS['USER']->isSuperUser())
        {
        	$sqlString = "
				SELECT DISTINCT a.id, a.language, a.name, a.description, a.catchwords, ".$COLUMNS."
				    MATCH(".$SEARCH_COLUMN.") AGAINST ({CONDITION} IN BOOLEAN MODE)
				AS fulltextmatch
				FROM
				    {DB_PREFIX}item_".$this->getItemTypeID()." a
				WHERE
					a.cid = {CID}
				    ".$EXTENSION."
				AND
				    MATCH(".$SEARCH_COLUMN.") AGAINST ({CONDITION} IN BOOLEAN MODE)
				ORDER BY fulltextmatch DESC
				LIMIT ".$db->escape($LIMIT).";";
        }

        $res = array();

        $values = array( 'CID'         => _CID_,
                         'CONDITION'   => $searchterm,
                         'USER'        => $GLOBALS['_BIGACE']['SESSION']->getUserID(),
                         'PERMISSION'  => _BIGACE_RIGHTS_NO );

        $sqlString = $db->prepareStatement($sqlString, $values, true);
        $temp = $db->execute( $sqlString );

        for ($i = 0; $i < $temp->count(); $i++)
        {
            $res[$i] = new SearchResult( $temp->next() );
        }

        $res = Hooks::apply_filters('search', $res, $searchterm, $this);

    	return $res;
    }

    /**
     * Call this to make sure all resources are freed.
     */
    function finalize()
    {
    }

}