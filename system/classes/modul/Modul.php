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
 * @subpackage modul
 */

define('MODUL_DEFAULT_VERSION', 0);

/**
 * This represents a BIGACE module.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage modul
 */
class Modul 
{
    private $values = array();
    private $path = null;
    private $id = null;
	private $isAdmin = null;
    private $loadedTranslation = false; // whether internal translations have been loaded
    
    function Modul($id)
    {
        $this->path = $GLOBALS['_BIGACE']['DIR']['modul'] . $id;
        $this->id = $id;
        $allConfig = array();
        // Plugin depend settings
        if (is_dir($this->path) && is_file($this->path.'/modul.ini')) {
            $ini = parse_ini_file($this->path.'/modul.ini', TRUE );
            $allConfig = array_merge($allConfig, $ini);
        }
        
        if(!isset($allConfig['version']))
        	$allConfig['version'] = MODUL_DEFAULT_VERSION;
        // old style before 2.5
        if(!isset($allConfig['translate']))
        	$allConfig['translate'] = false;
        // new style since 2.5 - only the translation flag is required to indicate the name
        if(isset($allConfig['translation']))
        	$allConfig['translate'] = true;
        
        $this->values = $allConfig;
        
    	if ($this->isTranslated()) {
    		// old style translations
	        if(!isset($allConfig['translation'])) {
		    	Translations::load('modul', _ULC_, 'modul_'.$this->getID(),$this->getPath().'/translation');
		    	Translations::loadGlobal('translation', _ULC_, $this->getPath().'/translation');
		    	$this->loadedTranslation = true;
	        }
	        else {
	        	Translations::loadGlobal($this->values['translation'],_ULC_,$this->getPath().'/translation');
	        }
    	}
    }
 
    /**
     * Loads the moduls translation for the given locale.
     * @access private
     */
    function loadTranslation($locale = null) {
	    if(is_null($locale))
	    	$locale = _ULC_;
	    if(isset($this->values['translation']))
			Translations::load($this->values['translation'],$locale,$this->getNamespace(),$this->getPath().'/translation');
		// TODO load old translation instead?!?!
		$this->loadedTranslation = true;
    }

    /**
     * Returns whether this Modul is translated.
     * If it is translated, the Language File "translation" will be 
     * automatically loaded into the Global Namespace.
     */
	function isTranslated() {
        return (bool)$this->values['translate'];
    }
    
    /**
     * Returns the modules translation object.
     * @since 2.5
     */
    function getTranslation($locale = null) {
    	if($this->isTranslated()) {
		    if(is_null($locale))
		    	$locale = _ULC_;
    		return Translations::get($this->values['translation'],$locale,$this->getNamespace(),$this->getPath().'/translation');
    	}
    	return null;
    }
    
    private function getNamespace() {
    	return 'modul_'.$this->getID();
    }

    private function getTranslationKey($title) {
        if(isset($this->values['translation']))
            return $title . '_' . $this->getID();
        return $title;
    }
    
    /**
     * Get a translation from the Moduls own translation.
     */
    function translate($key, $fallback = null) {
    	if($this->isTranslated()) {
    		if(!$this->loadedTranslation)
    			$this->loadTranslation(_ULC_);
        	return Translations::translate($this->getTranslationKey($key), $this->getNamespace(), $fallback);
    	}
        return $fallback;
    }
    
    /**
     * Returns the translated name of this module.
     * If you want to have the Name in a different language, call <code>loadTranslation($locale)</code> before.
     */
    function getName() {
        return $this->translate('name', $this->getID());
    }

    /**
     * Returns the translated title of this Modul.
     */
    function getTitle() {
        return $this->translate('title', $this->getID());
    }

    /**
     * Returns the translated description of this Modul.
     */
    function getDescription() {
        return $this->translate('description', "-");
    }
    
    /**
     * Get the Unique ID of this Modul.
     * @return String the ID of this Modul
     */
    function getID() {
        return $this->id;
    }

    /**
     * Get the Full URL of the Modul. This is the URL to the Moduls
     * PHP File.
     * @return String the full qualified URL to the File
     */
    function getFullURL() {
        return $this->getPath() . '/modul.php';
    }
    
    /**
     * Get the Path to this Modul.
     */
    function getPath() {
        return $this->path;
    }
    
    /**
     * Returns whether this Modul is activated for the current Community or not.
     * @return boolean whether this Modul can be used in Menus or not
     */
    function isActivated() {
        if(isset($this->values['activation']['cid'._CID_]))
            return (bool)$this->values['activation']['cid'._CID_];
        return true;
    }
    
    /**
     * Returns the Configuration of this Modul as Array.
     */
    function getConfiguration() {
        return $this->values;
    }

    /**
     * Returns the version of this Modul or MODUL_DEFAULT_VERSION
     */
    function getVersion() {
    	return $this->values['version'];
    }
    
    /**
     * Checks if the actual User is a potential Modul Admin.
     * This check depends on a Functional right check!
     */
    function isModulAdmin() 
	{
		// simple caching
		if(is_null($this->isAdmin)) 
		{
			// initialize it with false
			$this->isAdmin = false;
		    $frightsToCheck = array();
		    $frightsToCheck[] = 'module_all_rights';

		    if (isset($this->values['admin']['fright'])) {
		        $temp = explode (",", trim($this->values['admin']['fright']));
		        if(count($temp) > 0) {
		            $frightsToCheck = array_merge($frightsToCheck, $temp);
		        }
		    }

		    foreach ($frightsToCheck AS $fright) {
		        if (has_permission($fright)) {
					// if we found one matching fright, permission is given, break loop
		            $this->isAdmin = TRUE;
					break;
		        }
		    }
		}
	    return $this->isAdmin;
    }
}

?>