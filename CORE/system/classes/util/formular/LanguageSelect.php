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

import('classes.language.LanguageEnumeration');
import('classes.util.html.Select');

/**
 * This class defines a HTML Select Box to select a Language.
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
class LanguageSelect extends Select
{
    /**
     * @access private
     */
    var $preSelected = null; 
    /**
     * @access private
     */
    var $displayLocale = null; 

    function LanguageSelect($displayLocale = null) {
        parent::Select();
        $this->displayLocale = $displayLocale;
    }
	
	/**
	 * Sets the Locale of the Language to be preselected.
	 * @param String locale the Languages locale
	 */
    function setPreSelected($locale) {
        $this->preSelected = $locale;
    }

    /**
     * Sets the locale, in which the Language names will be displayed.
     *
     * @param String the Languages locale
     */
    function setDisplayLocale($locale)
    {
        $this->displayLocale = $locale;
    }
    
    /**
     * @access private
     */
    function getHtml()
    {
    	$langEnum = new LanguageEnumeration();
    	for($i = 0; $i < $langEnum->count(); $i++)
    	{
    		$lang = $langEnum->next();
            $o = new Option();
            $o->setText($lang->getName($this->displayLocale));
            $o->setValue($lang->getLocale());
            if($this->preSelected != null && $this->preSelected == $lang->getLocale())
                $o->setIsSelected();
            $this->addOption($o);
    	}
        unset ($langEnum);
        
        return parent::getHtml();
    }

}

?>