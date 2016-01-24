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

import('classes.modul.Modul');
import('classes.modul.ModulEnumeration');
import('classes.configuration.IniHelper');
import('classes.util.IOHelper');

define('MODUL_PROJECT_SMARTY_TPL',      'tpl_inc');
define('MODUL_PROJECT_TYPE_STRING',     'String');
define('MODUL_PROJECT_TYPE_CATEGORY',   'Category');
define('MODUL_PROJECT_TYPE_INTEGER',    'Integer');
define('MODUL_PROJECT_TYPE_BOOLEAN',    'Boolean');
define('MODUL_PROJECT_TYPE_TEXT',    	'Text');
define('MODUL_PROJECT_TYPE_SQL_LIST',   'SQL_List');

define('MODUL_SETTINGS_NAME',         	'name');		// key for the properties name
define('MODUL_SETTINGS_TYPE',         	'type');		// key for the properties type
define('MODUL_SETTINGS_OPTIONAL',       'optional');	// key that defines if property is optional
define('MODUL_SETTINGS_DEFAULT',        'default');	    // key that defines the default value of a property
define('MODUL_SETTINGS_SQL',        	'sql');	    	// key that defines an sql query

/**
 * This should be used to handle any kind of BIGACE Module.
 * Receive an Enumeration of all available Modules or manipulate
 * the existing ones.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage modul
 */
class ModulService
{

	function ModulService() {
	}

    /**
     * @return ModulEnumeration a ModuleEnumeration above all existing Modules
     */
	function getModulEnumeration()
	{
		$dirName = $GLOBALS['_BIGACE']['DIR']['modul'];
		$modules = array();
		// Loop to find all Modules
		$handle=opendir( $dirName );
		while (false !== ($dir = readdir ($handle))) {
			if(is_dir($dirName . '/' . $dir) && $dir != "." && $dir != ".." && $dir != "CVS" ) {
				array_push($modules, $dir);
			}
		}
		closedir($handle);

		return new ModulEnumeration($modules);
	}

    /**
     * Activates the Modul for the given Consumer.
     */
    function activateModul($modulID, $cid = null)
    {
        if($cid == null) {
            $cid = _CID_;
        }
        $mod = new Modul($modulID);
        $conf = $mod->getConfiguration();
        $conf['activation']['cid'.$cid] = 1;
        return $this->saveModulConfig($modulID, $conf);
    }

    /**
     * Deactivates the Modul for the given Consumer.
     */
    function deactivateModul($modulID, $cid = null)
    {
        if($cid == null) {
            $cid = _CID_;
        }
        $mod = new Modul($modulID);
        $conf = $mod->getConfiguration();
        $conf['activation']['cid'.$cid] = 0;
        return $this->saveModulConfig($modulID, $conf);
    }

	function createModul($name)
	{
		$newDir = $GLOBALS['_BIGACE']['DIR']['modul'].$name;
		if(file_exists($newDir))
			return false;

		if(!is_writeable($GLOBALS['_BIGACE']['DIR']['modul']))
			return false;

		import('classes.util.IOHelper');
		if(!IOHelper::createDirectory($newDir))
			return false;

		$this->saveModulConfig($name, array('translate' => TRUE));

		$content = "<?php
// BIGACE modul - created by API
// This script is pure PHP, so enjoy its power :)

echo 'Hello World';

?>";
		return IOHelper::write_file($newDir.'/modul.php', $content);
	}

    /**
     * @access private
     */
    function saveModulConfig($modulID, $modulConfig)
    {
        $mod = new Modul($modulID);
        $filename = $mod->getPath() . '/modul.ini';
        return IniHelper::write_ini_file($filename, $modulConfig, $this->getConfigComments($modulID), FALSE);
    }

    /**
     * @access private
     */
    function getConfigComments($modulID)
    {
        $mod = new Modul($modulID);

        $comment = array();
        $comment[] = "";
        $comment[] = 'INI File for the Modul: '.$mod->getName().' ('.$modulID.') ';
        $comment[] = "";
        $comment[] = 'For further information go to http://www.bigace.de/.';
        $comment[] = "";
        $comment[] = "This file was was saved at " . date("Y/m/d H:i:s");
        $comment[] = "by " . $GLOBALS['_BIGACE']['SESSION']->getUser()->getName() . ".";
        $comment[] = "\n";

        return $comment;
    }

    /**
     * Get an Array with all configured Properties (which can be set by the ModulAdmin Application),
     * mentioned in the Moduls Ini File.
     *
     * If you pass null as Modul, the Menus configured Modul will be used!
     *
     * You can set default values by passing the $props Array with correct keys.
     * If a value does not exist, and is also not passed in the fallback Array
     * $props, it will at least be looked-up in the Ini File.
     * When the value can still not be found, it is defined to be null!
     *
     * @return array the Modul Configuration with all intialized keys
     */
    function getModulProperties($menu, $modul = null, $props = array())
    {
		import('classes.item.ItemProjectService');
    	$projectService = new ItemProjectService(_BIGACE_ITEM_MENU);

    	if(is_null($modul)) {
    		$modul = new Modul($menu->getModulID());
    	}

    	$propNames = array();
        $conf = $modul->getConfiguration(); // load configured properties
        if(isset($conf['properties'])) {
        	$propNames = explode(',', $conf['properties']);
        }

    	// loop all configured property names
    	foreach($propNames AS $propKey) {

           	if(isset($conf[$propKey]) && is_array($conf[$propKey]))
           	{
	           	$settings = $conf[$propKey];	// all settings of this property

           		switch($settings[MODUL_SETTINGS_TYPE])
           		{
               		case MODUL_PROJECT_TYPE_INTEGER:
    	                $save_key = 'num';
    	                break;
               		case MODUL_PROJECT_TYPE_CATEGORY:
    	                $save_key = 'num';
    	                break;
               		case MODUL_PROJECT_TYPE_STRING:
    	                $save_key = 'text';
    		                break;
               		case MODUL_PROJECT_TYPE_TEXT:
    	                $save_key = 'text';
    	                break;
               		case MODUL_PROJECT_TYPE_BOOLEAN:
    	                $save_key = 'num';
    	                break;
    	            default:
    	                $save_key = 'text';
    	                break;
          		}

    			if($save_key == 'num' && $projectService->existsProjectNum($menu->getID(), $menu->getLanguageID(), $propKey)) {
    			    $props[$propKey] = $projectService->getProjectNum($menu->getID(), $menu->getLanguageID(), $propKey);
        		} else if ($save_key == 'text' && $projectService->existsProjectText($menu->getID(), $menu->getLanguageID(), $propKey)) {
    			    $props[$propKey] = $projectService->getProjectText($menu->getID(), $menu->getLanguageID(), $propKey);
        		}
                else {
        			if(!isset($props[$propKey])) {
        				if (isset($conf[$propKey]['default']))	// read configured default value from ini
        					$props[$propKey] = $conf[$propKey]['default'];
        				else
        					$props[$propKey] = null; // no value could be fond, set to null
        			}
        		}
            }
           	else {
				$GLOBALS['LOGGER']->logError('Configured Modules "'.$modul->getName().'" Property "'.$propKey.'" is invalid, check Ini File!');
           	}
    	} // foreach
    	return $props;
    }

}

?>