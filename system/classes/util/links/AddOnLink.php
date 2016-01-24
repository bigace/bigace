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

import('classes.util.CMSLink');

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
class AddOnLink extends CMSLink
{

    function AddOnLink($name = '', $file = '') {
        $this->setCommand('addon');
		if($name != '')
	        $this->setItemID($name);
		if($file != '')
	        $this->setAction($file);

        $this->setLanguageID($GLOBALS['_BIGACE']['PARSER']->getLanguage());
        $this->setFilename($file);
    }

    /**
     * This defines the name of the AddOn to load.
     * This is the directory name below /addon/.
     *
     * @param String the name of the AddOn
     */
    function setAddOnName($name) {
        $this->setItemID($name);
    }

    /**
     * This defines the PHP Script to load. The Script must have the extension .php
     *
     * @param String the script name without extension
     */
    function setScript($name) {
        $this->setAction($name);
        $this->setFilename($name);
    }

}

?>
