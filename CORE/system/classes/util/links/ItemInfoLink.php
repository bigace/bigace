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
 * @subpackage util.links
 */

loadClass('util', 'CMSLink');

/**
 * This class generates a Link to the Item Information Screen.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.links
 */
class ItemInfoLink extends CMSLink
{

    function ItemInfoLink() {
        $this->setCommand('application');
        $this->setItemID(_BIGACE_TOP_LEVEL);
        $this->setLanguageID($GLOBALS['_BIGACE']['PARSER']->getLanguage());
        $this->setAction('util');
        $this->setSubAction('iteminfo');
        $this->setFilename('information.html');

        // application specific
        $this->setInfoItemtype(_BIGACE_ITEM_MENU);
        $this->setInfoItemID($GLOBALS['_BIGACE']['PARSER']->getItemID());
        $this->setInfoItemLanguage($GLOBALS['_BIGACE']['PARSER']->getLanguage());
    }

    function setInfoItemtype($itemtype)
    {
        $this->addParameter('data[itemtype]', $itemtype);
    }

    function setInfoItemLanguage($locale)
    {
        $this->addParameter('data[language]', $locale);
    }

    function setInfoItemID($id)
    {
        $this->addParameter('data[id]', $id);
    }
}

?>