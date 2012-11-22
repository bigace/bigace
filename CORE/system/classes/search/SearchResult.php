<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.<br>Copyright (C) Kevin Papst.
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

/**
 * This Class represents a SearchResult.
 *
 * Result-Columns should at least be:
 * - 'unique_name'
 * - 'text_5'
 * - 'text_1'
 * - 'catchwords'
 * - 'description'
 * - 'name'
 * - 'id'
 * - 'language'
 * - 'text_5'
 * - 'filename'
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage search
 */
class SearchResult
{

    /**
     * @access private
     */
    var $res;

    /**
     * Initialize the SearchResult, MUST NOT be called from outside this package!
     *
     * @param array the result
     * @access protected
     */
    function SearchResult($result)
    {
    	$this->res = $result;
    }

    /**
     * Gets a Column from the Search Result.
     * If this column is not set null is returned!
     *
     * @param String the Column Name to fetch
     * @return mixed the Column Value or null
     */
    function getResultColumn($columnName)
    {
    	if (isset($this->res[$columnName]))
    		return $this->res[$columnName];
    	return null;
    }

}
