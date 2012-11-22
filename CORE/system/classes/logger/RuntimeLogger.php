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
 * @subpackage logger
 */

import('classes.logger.Logger');

/**
 * This Logger saves all messages within an internal array. You may fetch these
 * messages at the end of the call and use them for whatever you want (output in html,
 * save to file...).
 *
 * They are lost within the end of the call, but you can display them by calling
 * RuntimeLogger::dumpMessages($mode)
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage logger
 */
class RuntimeLogger extends Logger
{
	/**
	 * @access private
	 */
    var $LOGMSG         = array();
	/**
	 * @access private
	 */
    var $countMsg       = array();

    function Logger()
    {
        $this->countMsg[E_DEBUG]        = 0;
        $this->countMsg[E_USER_NOTICE]  = 0;
        $this->countMsg[E_USER_ERROR]   = 0;

        $this->LOGMSG[E_DEBUG]          = array();
        $this->LOGMSG[E_USER_NOTICE]    = array();
        $this->LOGMSG[E_USER_ERROR]     = array();
    }


    function log($mode, $msg)
    {
        $this->countMsg[$mode]++;
        $this->LOGMSG[$mode][$this->countMsg[$mode]] = $msg;
    }

    function dumpMessages ($mode, $pre = '<!-- ', $past = ' -->', $showDesc = true)
    {
         $temp = $this->LOGMSG[$mode];
         $desc = '';
         if ($showDesc) {
            $desc = '['.$this->getDescriptionForMode($mode).'] ';
         }
       	 for ($i=0; $i < count($temp); $i++) {
       	    echo $pre . $desc . $temp[$i+1] . $past . "\n";
       	 }
    }

    function countLog($mode)
    {
        return $this->countMsg[$mode];
    }

    function finalize()
    {
        // Show Debug if enabled
        if ($this->isDebugEnabled()) {
            echo "\n\n";
    	    $this->dumpMessages($this->ERROR_LEVEL[E_DEBUG]);
            echo "\n";
        }
        // Show all Error Messages
     	$this->dumpMessages($this->ERROR_LEVEL[E_ERROR]);
    }

}