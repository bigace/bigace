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

import('classes.util.html.Select');
import('classes.smarty.SmartyService');
import('classes.smarty.SmartyDesign');

/**
 * This class defines a HTML Select Box for all installed Smarty Designs.
 * You can choose which Design is preselected.
 * You can choose if Designs are sorted in alphabetical order.
 *  
 * Set all values and call <code>getHtml()</code>.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.formular
 */
class DesignSelect extends Select
{
    /**
     * @access private
     */
    var $preSelected = null; 
    /**
     * @access private
     */
    var $sortAlpha = true; 
    /**
     * @access private
     */
    var $showPreselected = false; 


    function DesignSelect() {
        parent::Select();
    }
	
	/**
	 * Sets the Name of the Design to be preselected.
	 * @param String id the Design ID
	 */
    function setPreSelected($template) {
        $this->preSelected = $template;
        $this->showPreselected = true;
    }
    
	/**
	 * Sets if the list will be sorted alphabetical.
	 * Default id TRUE.
	 * @param boolean sortAlphabetical if the list will be sorted or not
	 */
    function setSortAlphabetical($sortAlphabetical) {
    	$this->sortAlpha = $sortAlphabetical;
    }
    
    /**
     * @access private
     */
    function getHtml()
    {
        $temp = array();
        $service = new SmartyService();
        $all = $service->getAllDesigns();
        foreach($all AS $single)
        {
            $temp[$single->getName()] = $single->getName();
        }
        
        // sort the moduls in alphabetical order
		if($this->sortAlpha) {
	        $temp = array_flip($temp);
	        asort($temp);
	        reset($temp);
	        $temp = array_flip($temp);
		}
		
		$seenPreselected = false;
        
        // loop over the (sorted) array and create an Option for each entry
        foreach($temp AS $name => $name2) {
            $o = new Option();
            $o->setText($name);
            $o->setValue($name2);
            if($this->preSelected != null && $this->preSelected == $name) {
                $o->setIsSelected();
                $seenPreselected = true;
            }
            $this->addOption($o);
        }
        unset ($temp);
        
        // fallback if preselected 
        if(!$seenPreselected && $this->showPreselected) {
            $o = new Option();
            $o->setText($this->preSelected);
            $o->setValue($this->preSelected);
            $o->setIsSelected();
            $this->addOption($o);
        }
        
        return parent::getHtml();
    }

}

?>