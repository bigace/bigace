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
 * @subpackage workflow
 */

/**
 * Superclass for all Workflows.
 * All implementing classes should overwrite the methods marked with:
 * <br>
 * TO BE OVERWRITTEN!
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage workflow
 */
class Workflow
{
    /**
     * @access private
     */
    var $futureItem = NULL;
    
    /**
     * Creates a new Workflow Instance.
     */
    function Workflow() {
        //default constructor
    }
    
    /**
     * This method is called by the WorkflowService.
     * <br> 
     * DO NOT OVERWRITE!
     * @access private
     */
    function init($futureItem) 
    {
        $this->futureItem = $futureItem;
        $this->initWorkflow();
    }
    
    /**
     * Get the Item for this Workflow instance. 
     * <br> 
     * DO NOT OVERWRITE!
     */
    function getItem() 
    {
        return $this->futureItem;
    }
    
    /**
     * Get the current Step of this Workflow.
     * If the Workflow has just started and no Activity has performed
     * it returns null.
     * Might be overwritten...
     */
    function getActivityID() 
    {
        $item = $this->getItem();
        if ($item->getActivityID() == NULL || strlen(trim($item->getActivityID())) == 0)
            return NULL;
        return $item->getActivityID();
    }
    
    // -----------------------------------------------------------------------------
    // --------------------- [START] Methods to be overwritten ---------------------
    // -----------------------------------------------------------------------------

    /**
     * Return the Name of this Workflow. 
     * The Name should be a short String.
     * <br>
     * TO BE OVERWRITTEN!
     */
    function getName() 
    {
        return 'overwrite getName()...';
    }

    /**
     * Return the Description of this Workflow. 
     * <br>
     * TO BE OVERWRITTEN!
     */
    function getDescription() 
    {
        return 'overwrite getDescription()...';
    }
    
    /**
     * Will be called when the WorkflowService instantiates a new Object of this Type.
     * Overwrite this method to do some initialization work within your Workflow.
     * <br>
     * TO BE OVERWRITTEN!
     */
    function initWorkflow()
    {
    }
    
    /**
     * Return whether the requested Activity could be performed.
     * <br>
     * TO BE OVERWRITTEN!
     */
    function performActivity($activityID)
    {
        return FALSE;
    }
    
    /**
     * Return the next possible Activities.
     * If there are no more Activities the Array has a length of 0.
     * <br>
     * TO BE OVERWRITTEN!
     * 
     * @return array an Array of <code>WorkflowActivity</code> instances
     */
    function getPossibleActivities() 
    {
        return array();
    }
    
}

?>