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
import('classes.smarty.SmartyTemplate');

/**
 * This class defines a HTML Select Box for all installed SmartyTemplates.
 * You can choose whether deactivated (in work) Templates should be shown.
 * You can choose whether Includes should be shown or not.
 * You can choose which Template is preselected.
 * You can choose if Templates are sorted in alphabetical order.
 *  
 * Set all values and call <code>getHtml()</code>.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.formular
 */
class TemplateSelect extends Select
{
    /**
     * @access private
     */
    var $preSelected = null;
    /**
     * @access private
     */
    var $showDeactivated = false;
    /**
     * @access private
     */
    var $showSystemTemplates = false;
    /**
     * @access private
     */
    var $showPreselectIfDeactivated = true;
    /**
     * @access private
     */
    var $showIncludes = false; 
    /**
     * @access private
     */
    var $sortAlpha = true; 


    function TemplateSelect() {
        parent::Select();
    }
	
	/**
	 * Sets the Name of the Template that should be preselected.
	 * If this Template is deactivated, it will be rendered nevertheless.
	 * This behaviour can be changed by calling setShowPreselectedIfDeactivated($show).
	 * @param String id the Template Name
	 */
    function setPreSelected($template) {
        $this->preSelected = $template;
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
     * Sets whether deactivated Templates are rendered or not.
     * Default is FALSE.
     * @param boolean showDeactivated whether deactivated Templates will be rendered
     */
    function setShowDeactivated($showDeactivated) {
    	$this->showDeactivated = $showDeactivated;
    }
    
    /**
     * Sets whether System Templates will be shown or not.
     * Default is FALSE.
     * @param boolean showSystem whether System Templates will be rendered
     */
    function setShowSystemTemplates($showSystem) {
    	$this->showSystemTemplates = $showSystem;
    }
    
    /**
     * Sets whether Includes will be shown or not.
     * Default is FALSE.
     * @param boolean showDeactivated whether Includes will be rendered
     */
    function setShowIncludes($showIncludes) {
    	$this->showIncludes = $showIncludes;
    }	
    
	/**
	 * Sets whether the Preselected Template will be rendered if 
	 * it is deactivated or not. Default is TRUE.
	 * Set FALSE if you do not want to display the preselected and deactivated Template. 
	 * @param boolean show whether deactivated and preselected Template will be rendered
	 */
	function setShowPreselectedIfDeactivated($show) { 
    	$this->showPreselectIfDeactivated = $show;
	}


    /**
     * @access private
     */
    function getHtml()
    {
        $temp = array();
        $service = new SmartyService();
        $templateArray = $service->getAllTemplates($this->showIncludes);
        foreach($templateArray AS $template)
        {
            if(($this->showSystemTemplates && $template->isSystem()) || !$template->isSystem()) {
                if(!$template->isInWork() || ($template->isInWork() && $this->showDeactivated) || ($this->preSelected == $template->getName() && $this->showPreselectIfDeactivated)) {
                    $temp[$template->getName()] = $template->getName();
                }
            }
        }
        
        // sort in alphabetical order
		if($this->sortAlpha) {
	        $temp = array_flip($temp);
	        asort($temp);
	        reset($temp);
	        $temp = array_flip($temp);
		}
        
        // loop over the (sorted) array and create an Option for each Template
        foreach($temp AS $name => $name2) {
            $o = new Option();
            $o->setText($name);
            $o->setValue($name2);
            if($this->preSelected != null && $this->preSelected == $name)
                $o->setIsSelected();
            $this->addOption($o);
        }
        unset ($temp);
        
        return parent::getHtml();
    }

}
