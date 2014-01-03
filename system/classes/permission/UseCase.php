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
 * @subpackage parser
 */

/**
 * Base class for UseCases checks. 
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage permission
 */
class UseCase
{

	private $userid = null;
	
    function UseCase() { 
    	if(isset($GLOBALS['_BIGACE']['SESSION']))
    		$this->userid = $GLOBALS['_BIGACE']['SESSION']->getUserID();	
    }
    
    function setUserID($id) {
    	$this->userid = $id;
    }
    
    function getUserID() {
    	return $this->userid;
    }
    
    function getItemRight($itemtype, $itemid) {
        if (!isset($GLOBALS['RIGHT_SERVICE'])) {
            import('classes.right.RightService');
            $GLOBALS['RIGHT_SERVICE'] = new RightService();
        }
        
        if (isset($GLOBALS['RIGHT_SERVICE']))
        	return $GLOBALS['RIGHT_SERVICE']->getItemRight($itemtype, $itemid, $this->userid);
        
        return null;
    }
    
    function checkFunctionalRight($frightName) {
    	return has_permission($frightName);
    }
    
	function isAllowed() {
		return false;
	}
}

?>