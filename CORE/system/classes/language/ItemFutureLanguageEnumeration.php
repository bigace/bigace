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
 * @subpackage language
 */

loadClass('item', 'Itemtype');
loadClass('language', 'Language');

/**
 * Returns all Languages, the specified Item has Future Versions of.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage language
 */
class ItemFutureLanguageEnumeration extends Itemtype
{
	/**
	 * @access private
	 */
	var $languages;
	
	function ItemFutureLanguageEnumeration($itemtype, $item_id)
	{
	    $this->initItemtype( $itemtype );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('item_select_future_language_versions');
        $values = array ( 'CID'       => _CID_, 
                          'ITEM_ID'   => $item_id );
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
        $this->languages = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
	}

	/**
     * Count the amount of available Future Language Versions.
     * @return int the amount of Languages
	 */
    function count()
	{
		return $this->languages->count();
	}

	/**
     * Return the next <code>Language</code> Instance.
     * @return Language the next Language for this Items Future Version
	 */
    function next()
	{
		$temp = $this->languages->next();
		return new Language($temp[0]);
	}

}

?>