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
 * @subpackage exception
 */

loadClass('exception', 'AdministrationException');

/**
 * This represents a RequestResultException, 
 * that can be used to create a Exception depending on a AdminRequestResult.
 * 
 * We fetch the Error Code by calling: 
 * <code>$result->getValue('code')</code>
 * 
 * And the Message by calling:
 * <code>$result->getMessage()</code>
 * 
 * If none could be found, we use the Default Code 'Unknown'.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage exception
 */
class RequestResultException extends AdministrationException
{
	/**
	 * Identifies the Default Code 'Unknown';
	 */
	var $DEFAULT_CODE = 'Unknown';
	
    /**
     * Creates a new RequestResultException.
     * 
     * @param AdminRequestResult the AdminRequestResult to fill the Exception values
     * @param String the URL of this Exception (can be used for creating Back links)
     * @access public
     */
    function RequestResultException($result,$url = '') 
    {
		$code = $this->DEFAULT_CODE;
		if ($result->getValue('code') != null)
			$code = $result->getValue('code');
    	
    	$this->AdministrationException($code,$result->getMessage(),$url);
    }

}

?>