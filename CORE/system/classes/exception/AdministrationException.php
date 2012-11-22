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
 * @subpackage exception
 */

loadClass('exception', 'CoreException');

/**
 * This represents a AdministrationException.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage exception
 */
class AdministrationException extends CoreException
{
	 private $url = '';
	 
    /**
     * Creates a new AdministrationException.
     * 
     * @param int the Error Code
     * @param String the Error Message
     * @param String the URL of this Exception (can be used for creating Back links)
     * @access public
     */
    function AdministrationException($code,$message,$url = '') 
    {
    	$this->CoreException($code,$message);
    	$this->setURL($url);
    	$this->setNamespace(LOGGER_NAMESPACE_AUDIT);
    }
    
    /**
     * Sets the URL of this Exception. Mostly used for showing Back Links!
     * @param String the URL to go back after displaying the Exception
     */
    function setURL($url) 
    {
    	$this->url = $url;
    }
    
    /**
     * Gets the URL of this Exception.
     * @return String thr URL of this Exception
     */
    function getURL() 
    {
    	return $this->url;
    }

}

?>