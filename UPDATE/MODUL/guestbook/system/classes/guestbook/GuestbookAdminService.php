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
 * @subpackage guestbook
 */
 
/**
 * Adminservice for your Guestbook, holding all functions for write access.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage guestbook
 */
class GuestbookAdminService 
{

    /**
    * Creates a new Guestbook Entry
    *
    * @param    String  the Visitors Name
    * @param    String  the Visitors EMail Adress
    * @param    String  the Visitors Homepage Adress
    * @param    String  the Visitors Comment
    * @return   int     the new generated Guestbook Entry ID
    */
    function createEntry($name, $email, $homepage, $comment)
    {
        $new_entry = strip_tags($comment);

	    $values = array( 'NAME'         => $name,
	                     'EMAIL'        => $email,
	                     'HOMEPAGE'     => $homepage,
	                     'COMMENT'      => $new_entry,
	                     'TIMESTAMP'    => time() );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('guestbook_create');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sqlString);
    }

    /**
    * This changes a single Guestbook Entry
    *
    * @param    int     the Entry ID to change
    * @param    String  the changed Name
    * @param    String  the changed Email Adress
    * @param    String  the changed Homerpage Adress
    * @param    String  the changed Comment
    * @param    Object  the changed Date
    * @return   Object  the DB Result for this UPDATE
    */
    function changeEntry($id, $name, $email, $homepage, $comment)
    {
	    $values = array( 'ENTRY_ID'     => $id,
	                     'NAME'         => $name,
	                     'EMAIL'        => $email,
	                     'HOMEPAGE'     => $homepage,
	                     'COMMENT'      => $comment );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('guestbook_change');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
    * Deletes a GuestbookEntry
    *
    * @param    int     the GuestbookEntry ID
    * @return   Object  the DB Result for this DELETE
    */
    function deleteEntry($id)
    {
	    $values = array( 'ENTRY_ID' => intval($id) );
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('guestbook_delete');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }
    
    
}


?>
