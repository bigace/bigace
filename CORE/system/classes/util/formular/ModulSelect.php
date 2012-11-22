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
 * @subpackage util.formular
 */

import('classes.modul.ModulService');
import('classes.util.html.Select');

/**
 * This class defines a HTML Select Box for all installed Moduls.
 * You can choose whether deactivated Moduls should be shown.
 * You can choose which Language is used to fetch Moduls information.
 * You can choose which Modul is preselected.
 * You can choose if Modul are sorted in alphabetical order.
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
class ModulSelect extends Select
{
    /**
     * @access private
     */
    var $preSelectedID = null;
    /**
     * @access private
     */
    var $showDeactivated = false;
    /**
     * @access private
     */
    var $showPreselectIfDeactivated = true;
    /**
     * @access private
     */
    var $modLanguage = null; 
    /**
     * @access private
     */
    var $sortAlpha = true; 


    function ModulSelect() {
        parent::Select();
        $this->modLanguage = _ULC_;
    }
	
	/**
	 * Sets the ID of the Modul that should be preselected.
	 * If this Modul is deactivated, it will be rendered nevertheless.
	 * This behaviour can be changed by calling setShowPreselectedIfDeactivated($show).
	 * @param String id the Modul ID
	 */
    function setPreSelectedID($id) {
        $this->preSelectedID = $id;
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
     * Sets whether deactivated Moduls are rendered or not.
     * Default is FALSE.
     * @param boolean showDeactivated whether deactivated Moduls will be rendered
     */
    function setShowDeactivated($showDeactivated) {
    	$this->showDeactivated = $showDeactivated;
    }

    /**
     * Sets the language the Moduls information will be shown with.
     * Default is the User environment language _ULC_.
     * @param String modulLanguage the Locale to use
     */
    function setModulLanguage($modulLanguage) {
    	$this->modLanguage = $modulLanguage;
    }
	
	/**
	 * Sets whether the Preselected Modul will be rendered if it is deactivated or not.
	 * Default is TRUE.
	 * Set FALSE if you do not want to display the preselected and deactivated Modul. 
	 * @param boolean show whether deactivated and preselected Modul will be rendered
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
        $modul_service = new ModulService();
        $modulEnum = $modul_service->getModulEnumeration();
        while($modulEnum->hasNext())
        {
            $tempMod = $modulEnum->next();
            if(($this->preSelectedID == $tempMod->getId() && $this->showPreselectIfDeactivated) 
                || $tempMod->isActivated() || $this->showDeactivated)
            {
                if(_ULC_ != $this->modLanguage) {
                    $tempMod->loadTranslation($this->modLanguage);
                }
                $temp[$tempMod->getName()] = $tempMod->getId();
            }
        }
        
        // sort the moduls in alphabetical order
		if($this->sortAlpha) {
	        $temp = array_flip($temp);
	        asort($temp);
	        reset($temp);
	        $temp = array_flip($temp);
		}
        
        // loop over the (sorted) array and create an Option for each Modul
        foreach($temp AS $name => $modulID) {
            $o = new Option();
            $o->setText($name);
            $o->setValue($modulID);
            if($this->preSelectedID != null && $this->preSelectedID == $modulID)
                $o->setIsSelected();
            $this->addOption($o);
        }
        unset ($temp);
        
        return parent::getHtml();
    }

}

?>