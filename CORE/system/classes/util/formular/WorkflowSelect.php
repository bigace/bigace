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
 * @subpackage util.formular
 */

import('classes.workflow.WorkflowService');

/**
 * This class defines a HTML Select Box ...
 * Set all values and call <code>getHtml()</code>.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.formular
 */
class WorkflowSelect extends Select
{
    /**
     * @access private
     */
    var $preSelectedID = null;
    /**
     * @access private
     */
    var $ready = false;
    
    function WorkflowSelect() {
        parent::Select();
    }

    function setPreSelectedID($id) {
        $this->preSelectedID = $id;
    }
    
    /**
     * @access private
     */
    function getHtml()
    {
        if(!$this->ready)
        {
	        $all = WorkflowService::getAllWorkflowTypes();
	        $temp = array('' => '');
	        foreach($all AS $wf) {
	        	$clNa = get_class($wf);
	        	$o = new Option();
	        	$o->setText($wf->getName());
	        	$o->setValue($clNa);
		        if($this->preSelectedID != null && $this->preSelectedID == $clNa)
		        	$o->setIsSelected();
		    	$this->addOption($o);
	        }
	        $this->ready = true;
        }
        return parent::getHtml();
    }

}

?>