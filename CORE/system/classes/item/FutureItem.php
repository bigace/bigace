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
 * @subpackage item
 */

import('classes.item.DBItem');

/**
 * This represents a Future Version of an Item.
 * <br>
 * <b>ATTENTION:</b><br>
 * It will only be instantiated by the Core, you MAY NOT instantiate it directly!
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage item
 */
class FutureItem extends DBItem
{
    
    /**
     * Future Item constructor.
     */
	function FutureItem( $result ) 
    {
	    $this->_setItemValues( $result );
    	$this->initItemtype($result["itemtype"]);
	}
    
    /**
     * Get the Workflow Name of the Future Version.
     * 
     * @return String the Workflow Name
     */
    function getWorkflowName()
    {
        $val = $this->_getItemValues();
        return $val['workflowname'];
    }
    
    /**
     * Return the Activity ID of the Future Version (Workflow dependend!).
     * 
     * @return mixed the Activity ID
     */
    function getActivityID() 
    {
        $val = $this->_getItemValues();
        return $val['activity'];
    }
    
    /**
     * Get the User ID of the Assignee for the Future Version.
     * If there is no Assignee, it returns NULL.
     * 
     * @return int the User ID the Futue Version is assigned to, or NULL
     */
    function getAssignedUserID()
    {
        $id = $this->getItemNum('5');
        if ($id == NULL || strlen(trim($id)) == 0 || $id == 0)
            return NULL;
        return $id;
    }
    
    /**
     * Get the Workflow or NULL.
     */
    function getWorkflow()
    {
        $name = $this->getWorkflowName();
        if ($name == NULL || strlen(trim($name)) == 0)
            return NULL;
        $wf = new $name();
        $wf->init($this);
        return $wf;    
    }
    
    function getInitiatorID()
    {
        $val = $this->_getItemValues();
        return $val['initiator'];
    }

    /**
     * Wrapper method to help within the editor. 
     */
    function getLayoutName() {
        return $this->getItemText('4');
    }
}

?>