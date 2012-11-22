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
 * @subpackage tipoftheday
 */

import('classes.tipoftheday.TipOfTheDay');

/**
 * Tip of the Day Service Implementation.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage tipoftheday
 */
class TipOfTheDayService
{
    /**
     * @access private
     */
    var $tips;

    /**
     * Create a new instance of the Tip Service.
     */
    function TipOfTheDayService()
    {
    }
    
    /**
     * Get the Tip with the highest ID.
     */
    function getMaxTip()
    {
        return $this->getAny('max(id)');
    }
    
    /**
     * Get the Tip with the lowest ID.
     */
    function getMinTip() 
    {
        return $this->getAny('min(id)');
    }
    
    /**
     * @access private
     */
    function getAny($what) 
    {
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('tipOfTheDay_selectAny');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array('CID' => _CID_, 'WHAT' => $what));
	    $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
		$temp = $temp->next();
		return $temp[0];
    }
    
    /**
     * Checks if the Tip with the given ID exists.
     */
    function existsTip($id)
    {
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('tipOfTheDay_loadTip');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array('CID' => _CID_, 'ID' => $id));
	    $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
		if ($temp && $temp->next() > 0) {
		    return true;
		}
        return false;
    }

    /**
     * @access private
     */
    function make_seed() {
        list($usec, $sec) = explode(' ', microtime());
        return (float) $sec + ((float) $usec * 100000);
    }
    
    /**
     * Get a Random number between Minimum and Maximum.
     * @access private
     */
    function getRandomNumber($min, $max)
    {
        srand($this->make_seed());
        return rand($min, $max);
    }

    /**
     * Get a random tip from all available. 
     */
    function getRandomTip()
    {
        $min = $this->getMinTip();
        $max = $this->getMaxTip();
        
        if ($min >= 0 && $max > 0 && $max >= $min) {
            do {
                $id = $this->getRandomNumber($this->getMinTip(), $this->getMaxTip());
            } while ( !$this->existsTip($id) );

            return $this->getTip($id);
        }
        return null;
    }
    
    /**
     * Gets a Tip from the System.
     */
    function getTip($id)
    {
        return new TipOfTheDay($id);
    }

}

?>