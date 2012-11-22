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
 * @subpackage util.links
 */

loadClass('util', 'CMSLink');

/**
 * This class generates the URL to the Image Chooser Application.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.links
 */
class ImageChooserLink extends CMSLink
{

    function ImageChooserLink() {
        $this->setCommand('application');
        $this->setItemID($GLOBALS['_BIGACE']['PARSER']->getItemID());
        $this->setLanguageID($GLOBALS['_BIGACE']['PARSER']->getLanguage());
        $this->setAction('images');
        $this->setFilename('imagechooser.html');
    }

    /**
     * This defines the name of the Javascript function, which takes
     * one Parameter: the URL of the choosen Image.
     *
     * @param String the name of the Javascript function
     */
    function setJSNameForURL($name) {
        $this->addParameter('jsFunc', $name);
    }

    /**
     * This defines the name of the Javascript function, which takes
     * two Parameter: the ID and the Name of the choosen Image.
     *
     * @param String the name of the Javascript function
     */
    function setJSNameForInfos($name) {
        $this->addParameter('imgInfos', $name);
    }

}

?>