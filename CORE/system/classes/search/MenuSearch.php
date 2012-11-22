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

loadClass('search', 'ItemSearch');

/**
 * This Class is used for searching in Menus!
 *
 * The Search is performed within the Content and the default Columns of the ItemSearch.
 *
 * CAUTION: If you do not call <code>setSearchLanguageID(languageIdToSearch)</code>,
 * an language independet Search will be performed!
 *
 * Therefor the following columns are selected (by default):
 * - language
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage search
 */
class MenuSearch extends ItemSearch
{

    function MenuSearch()
    {
        $this->init(_BIGACE_ITEM_MENU, SEARCH_LANGUAGE_INDEPENDENT);
        $this->addResultColumn('language');
    }

}