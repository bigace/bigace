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

import('classes.category.CategoryTreeWalker');
import('classes.util.html.Select');

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
class CategorySelect extends Select
{
    /**
     * @access private
     */
    var $preSelectedID = array();
    /**
     * @access private
     */
    var $hideID = null;
    /**
     * @access private
     */
    var $created = false;
    /**
     * @access private
     */
	var $startID = _BIGACE_TOP_LEVEL;

    function CategorySelect() {
        parent::Select();
    }

	/**
	 * Set an Start ID to create the Tree.
	 * This NEEDS to be called before getHtml().
	 */
    function setStartID($startID) {
        $this->startID = $startID;
    }
    
    function getHtml() {
    	if(!$this->created) {
        	$this->createRecurseCategoryTree($this->startID, 0);
    	}
        return parent::getHtml();
    }

    /**
     * You can pass an array of IDs (if you use this as multiple select box, or you pass 
     * only one ID if using single select mode. 
     */
    function setPreSelectedID($id) {
    	if(!is_array($id))
        	$this->preSelectedID[] = $id;
        else
        	$this->preSelectedID = $id;
    }
    
    /**
     * Sets the ID for a Tree to be hidden.
     * @param int id the Start ID of the Tree to be hidden
     */
    function setHideID($id) {
        $this->hideID = $id;
    }

    /**
     * @access private
     */
    function createRecurseCategoryTree($startID, $level)
    {
        if($this->hideID == null || $this->hideID != $startID) {
	        $ctw = new CategoryTreeWalker($startID);
	
	        for($i=0; $i < $ctw->count(); $i++)
	        {
	            $t = $ctw->next();
	            if($this->hideID == null || $this->hideID != $t->getID()) {
		            $name = '';
		            for($a = 0; $a < $level; $a++)
		                $name .= '- ';
		            $o = new Option();
		            $o->setText($name . $t->getName());
		            $o->setValue($t->getID());
		            if($this->preSelectedID != null && count($this->preSelectedID) > 0 && in_array($t->getID(), $this->preSelectedID))
		                $o->setIsSelected();
		            $this->addOption($o);
		            if($t->hasChilds()) { // category
		                $this->createRecurseCategoryTree($t->getID(), $level+1);
		            }
	            }
	        }
        }
    	$this->created = true;
    }

}

?>