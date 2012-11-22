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
 * Class used for admin Menu wrapper.
 * Represents a Admin TOP LEVEL Menu.
 *
 * Note: There are two kind of Admin Menus, TOP Level and direct Childs.
 * There is no plan for the future to add deeper Menus Structures.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage administration
 */
class AdminMenuItem
{
    /**
     * @access private
     */
    var $values = array();
    /**
     * @access private
     */
    var $parent = null;

    /**
     * Initializes this Menu with the Settings of the given ID.
     */
    function AdminMenuItem($values, $parent)
    {
        $this->parent = $parent;

//        $id = $values['id'];

        if(!isset($values['pluginpath'])) {
            $values['pluginpath'] = '';
        }

        if(!isset($values['translate'])) {
            $values['translate'] = true;
        } else { 
            $values['translate'] = (bool)$values['translate'];
        }
        
        if(file_exists($values['pluginpath'] . $values['id']  . '/' . $values['id'] . '.php')) {
            $values['pluginpath'] = $values['pluginpath'] . $values['id'];
            $values['script'] = $values['id'] . '.php';
        } 
        else if(file_exists($values['pluginpath'] . $values['id']  . '/plugin.php')) {
            $values['pluginpath'] = $values['pluginpath'] . $values['id'];
            $values['script'] = 'plugin.php';
        }         
        else if(file_exists(_ADMIN_PLUGIN_DIRECTORY . $values['id'] . '.php')) {
           $values['pluginpath'] = _ADMIN_PLUGIN_DIRECTORY;
           $values['script'] = $values['id'] . '.php';
        }
        else if($parent != null && file_exists($parent->getPath() . $values['pluginpath'] . $values['id'])){
           $values['pluginpath'] = $parent->getPath() . $values['pluginpath'] . $values['id'];
        } 
        else if($parent != null && file_exists($parent->getPath() . $values['pluginpath'])) {
           $values['pluginpath'] = $parent->getPath() . $values['pluginpath'];
        }

        // set directory
        if(!isset($values['script'])) {
            if(file_exists($values['pluginpath'] . '/' . $values['id'] . '.php'))
                $values['script'] = $values['id'] . '.php';
            else 
                $values['script'] = 'plugin.php';
        }

        if(isset($values['permission']) && !isset($values['frights']))
            $values['frights'] = $values['permission'];

		// set empty frights if not submitted
        if(!isset($values['frights'])) {
            $values['frights'] = array();
        } else {
            if(!is_array($values['frights']))
                $values['frights'] = explode (",", trim($values['frights']));
        }

    	$this->values = $values;
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
	 * Returns whether this Menu (dynamic menus can use of this) should
	 * be shown in the Navigation.
	 */
    function isHidden() {
        if (isset($this->values['hide']))
        	return (bool)$this->values['hide'];
        return false;
    }

    /**
     * Get the File Name for this PlugIn. 
     */
    function getFileName() {
        return $this->getPath() . $this->values['script'];
    }

    /**
     * Get the Path of this Menu.
     * Can be used to find Includes and other Ressources within the File System.
     */
    function getPath() {
        return $this->values['pluginpath'] . '/';
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

    /**
     * This indicates whether this PlugIn is translated or not.
     * The Administration Framework loads the PlugIns translation File itself,
     * you do not have to care about that!
     *
     * Administration Main Menus are virtual directorys and therefor never
     * translated by own Files.
     */
    function isTranslated() {
        if (!isset($this->values['translate']))
            return false;
        return (bool)$this->values['translate'];
    }

    function loadTranslation() {
        Translations::loadGlobal($this->getID(), ADMIN_LANGUAGE, $this->getPath());
    }

    /**
     * This indicates whether this PlugIn should load the PlugIns translation File
     * for displaying the Menus Name and Mouse-Over description within the Navigation Frame.
     *
     * Administration Main Menus are virtual directorys and therefor never translated by own Files.
     */
	function loadTranslationForMenu() {
        if (!isset($this->values['menu.translate']))
            return false;
        return (bool)$this->values['menu.translate'];
    }

    function getPermissions() {
        return (isset($this->values['frights']) ? $this->values['frights'] : array());
    }

    function checkPermissions($userID) {
        return check_admin_permission($this->getPermissions(), $userID);
    }
  
}
