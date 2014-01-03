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
 * This class generates a Link to the HTML Menu Chooser Application.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.links
 */
class HtmlMenuChooserLink extends CMSLink
{

    function HtmlMenuChooserLink() {
        $this->setCommand('application');
        $this->setItemID($GLOBALS['_BIGACE']['PARSER']->getItemID());
        $this->setLanguageID($GLOBALS['_BIGACE']['PARSER']->getLanguage());
        $this->setAction('util');
        $this->setSubAction('htmltree');
        $this->setFilename('menuTree.html');
    }

    function setHiddenID($id) {
        $this->addParameter('hideTree', $id);
    }

    function setStartID($id) {
        $this->addParameter('data[id]', $id);
    }

    function setShowHidden($showHidden = 'true') {
        $this->addParameter('showHidden', $showHidden);
    }
}

?>