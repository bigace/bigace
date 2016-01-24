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
 * @subpackage parser
 */

import('classes.permission.UseCase');

/**
 * This UseCase checks whether a User is allowed to edit a pages
 * content in one of the Editors. 
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage permission
 */
class UseCaseEditContent extends UseCase 
{
	
	var $menuID = null;

    function UseCaseEditContent($pageID) {
    	parent::UseCase();
    	$this->menuID = $pageID;	
    }
    
    function isAllowed() {
        // check write right settings on the page!
        $t = $this->getItemRight(_BIGACE_ITEM_MENU, $this->menuID);
        if(!$t->canWrite()) {
            return false;
        }
            
        $accessEditor = 
           ($this->checkFunctionalRight(_BIGACE_FRIGHT_USE_EDITOR) || 
            $this->checkFunctionalRight('edit_menus') ||   
            $this->checkFunctionalRight(_BIGACE_FRIGHT_ADMIN_MENUS));
            
		return $accessEditor; 
    }
}

?>