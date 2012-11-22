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

/**
 * Representation of a single Tip.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage tipoftheday
 */
class TipOfTheDay
{
    /**
     * @access private
     */
    var $tip;

    /**
     * Initialize the Tip with the given ID.
     * Make sure the passed ID exists!
     */
    function TipOfTheDay($id)
    {
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('tipOfTheDay_loadTip');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array('CID' => _CID_, 'ID' => $id));
	    $this->tip = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	    $this->tip = $this->tip->next();
    }
    
    /**
     * Returns the ID for this Tip.
     */
    function getID()
    {
        return $this->tip["id"];
    }

    /**
     * Returns the Title for this Tip.
     */
    function getName()
    {
        return $this->tip["title"];
    }
    
    /**
     * Returns the URL for this Tip.
     */
    function getLink()
    {
        return $this->tip["link"];
    }
    
    /**
     * Returns the Tip Content.
     */
    function getTip()
    {
        return $this->tip["tip"];
    }
    
}

?>
