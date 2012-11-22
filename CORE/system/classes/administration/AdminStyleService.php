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
 * @subpackage administration
 */

/**
* Needed for handling AdminStyles.
*/
loadClass('administration', 'AdminStyle');

/**
 * Class used for handling Administration Styles.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage administration
 */
class AdminStyleService
{
	function AdminStyleService() {
	}
	
	/**
	 * Returns the desired Style.
	 * @return AdminStyle the requested AdminStyle
	 */
	function loadStyle($name) 
	{
		$adminSytle = new AdminStyle($name);
		return $adminSytle;
	}

	/**
	 * Get all available Styles as an Array of AdminStyle Classes.
	 * @return array all Styles as AdminStyle
	 */
	function getAllStyles() 
	{
		$styles = array();
		$names = $this->getAvailableStyles();
		foreach($names AS $styleName)
		{
			array_push($styles, $this->loadStyle($styleName));
		}
		return $styles;
		
	}
	
	/**
	 * Return all availabel Style Names as Array.
	 * @return array all Style Names
	 */
	function getAvailableStyles() 
	{
        $names = array();
        if ($handle = opendir($GLOBALS['_BIGACE']['DIR']['php_public'].'system/style/')) {
            while (false !== ($file = readdir($handle))) { 
                if (is_dir($GLOBALS['_BIGACE']['DIR']['php_public'].'system/style/'.$file) && $file != "." && $file != ".." && $file != "CVS") { 
                	$names[$file] = $file;
                } 
            }
            closedir($handle); 
        }
		return $names;
	}
    
    /**
     * This returns the configured Default Admin Style.
     * @return AdminStyle the configured AdminStyle
     */
    function getConfiguredStyle() {
        $loadStyleName = ConfigurationReader::getConfigurationValue('admin', 'default.style');
        if ($loadStyleName == NULL)
            $loadStyleName = 'standard';
        return $this->loadStyle($loadStyleName); 
    }
    
}

?>