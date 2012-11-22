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
 * @subpackage administration
 */

import('classes.util.Translations');

/**
 * This represents a Admin Menu, a type of Admin Category!
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage administration
 */
class AdminMenu
{
    /**
     * @access private
     */
    var $values = array();
    /**
     * @access private
     */
    var $childs = array();
    

    /**
     * Initializes this Menu with the Settings of the given ID.
     */
    function AdminMenu( $values )
    {
    	$this->values = $values;
        if(!isset($this->values['childs']))
            $this->values['childs'] = array();
    }

	/**
	 * Returns the Path of the Menu.
	 */
    function getPath() {
        return $this->values['pluginpath'];
    }

	/**
	 * Returns whether the Menu uses dynamic Items or not.
	 */
    function isDynamic() {
        return (isset($this->values['dynamic']) && ((bool)$this->values['dynamic']));
    }

    /**
     * Get the translated Menu Name.
     */
    function getName() {
        return getTranslation('menu_' . $this->getID());
    }

    /**
     * Get the translated Title for this Menu. Title is most often a one-sentence
     * info message about this Administration PlugIn.
     */
    function getTitle() {
        return getTranslation('title_' . $this->getID());
    }

    /**
     * Get the translated Description for this Menu. This can be a longer text, exactly
     * saying what this PlugIn is meant for and other real important usage information.
     * The Description should not be longer than about 200 to 300 Character.
     */
    function getDescription() {
        return getTranslation('description_' . $this->getID());
    }

    /**
     * Returns the Menu ID. Used to build Administration Links.
     */
    function getID() {
        return $this->values['id'];
    }

    /**
     * Returns an Array of the Menu Child IDs.
     */
    function getChilds() {
        return $this->childs;
    }

    function addChild($child) {
        return $this->childs[] = $child;
    }

    function getChildNames() {
        return $this->values['childs'];
    }

    /**
     * Returns whether this Menu has Childs or not.
     */
    function hasChilds() {
        return (count($this->childs) > 0);
    }

    /**
     * Returns the Config setting of this Admin Plugin.
     * The settings are configured in the Plugin INI File.
     * Returns null if the key could not be found.
     */
    function getConfig($key) {
        if (isset($this->values[$key]))
        	return $this->values[$key];
        return null;
    }

}

?>
