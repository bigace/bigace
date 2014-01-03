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

loadClass('workflow', 'WorkflowActivity');
loadClass('item', 'ItemFutureService');

if (!defined('TERMINATING_ACTIVITY_ID')) {
    /**
     * @access private
     */
    define('TERMINATING_ACTIVITY_ID', 'TerminatingActivity');
}

/**
 * The TerminatingActivity deletes a Future Language Version.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage workflow
 */
class TerminatingActivity extends WorkflowActivity
{
    /**
     * Get the ID of this Activity.
     */
    function getID() 
    {
        return TERMINATING_ACTIVITY_ID;
    }

    /**
     * Get the Description of this Activity.
     */
    function getDescription() 
    {
        return getTranslation('terminating_description');
    }
    
    /**
     * Get the Name of this Activity.
     */
    function getName() 
    {
        return getTranslation('terminating_name', 'Delete changes');
    }

    /**
     * Deletes the Future Language Version.
     */
    function performActivity($itemtype, $itemid, $languageid)
    {
        $ifs = new ItemFutureService($itemtype);
        if ($ifs->hasFutureVersion($itemid, $languageid))
        {
            return $ifs->deleteFutureVersion($itemid, $languageid); 
        }
        return FALSE;
    }

}

?>