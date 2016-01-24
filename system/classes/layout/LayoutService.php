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
 * @subpackage layout
 */

import('classes.util.IOHelper');
import('classes.layout.Layout');

/**
 * This should be used to handle Layouts.
 * Receive all information about the known Layouts.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage layout
 */
class LayoutService
{
    private $layouts = array();

    /**
     * Instantiates a new LayoutService, which is used for fetching Layout object (PHP Templates).
     */
    function LayoutService() {
    }
    
    /**
     * Gets an Array with all known Definition Names.
     * @return array all known definition names
     */
    function getDefinitionNames()
    {
        $filenames = array();
        if ($handle = opendir(_BIGACE_DIR_CID . 'presentation/definition/')) {
            while (false !== ($file = readdir($handle))) { 
                if ($file != "." && $file != "..") { 
                    array_push($filenames, getNameWithoutExtension($file));
                } 
            }
            closedir($handle); 
        }
        return $filenames;
    }
    
    /**
     * Returns the Layout for the given Defintion file and key (if provided).
     * This methods uses a caching mechanism, better do not instantiate 
     * Layouts directly!
     * 
     * @return Layout the required Layout.
     */
    function getLayout($name, $key = '') 
    {
        $cacheKey = $name . '#_#' . $key;
        if (!isset($this->layouts[$cacheKey]))
        {
            $this->layouts[$cacheKey] = new Layout();
        }
        return $this->layouts[$cacheKey];
    }

}
