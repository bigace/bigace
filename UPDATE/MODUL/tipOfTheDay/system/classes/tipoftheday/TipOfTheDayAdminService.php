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
 * Adminservice for your rotating Tips.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage tipoftheday
 */
class TipOfTheDayAdminService 
{

    /**
    * Creates a new Tip-of-the-Day Entry
    *
    * @param    String  the Namespace
    * @param    String  the Title
    * @param    String  the Link
    * @param    String  the Tip
    * @return   int     the new generated Tip-of-the-Day Entry ID
    */
    function createEntry($namespace, $title, $link, $tip)
    {
	    $values = array( 'TITLE'     => $title,
	                     'NAMESPACE' => $namespace,
	                     'LINK'     => $link,
	                     'TIP'      => $tip );
        $sqlString = "INSERT INTO {DB_PREFIX}tipoftheday (cid, namespace, title, link, tip) VALUES ({CID}, {NAMESPACE}, {TITLE}, {LINK}, {TIP})";
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sqlString);
    }

    /**
    * This changes a single Tip-of-the-Day Entry
    *
    * @param    int     the Entry ID to change
    * @param    String  the Namespace
    * @param    String  the Title
    * @param    String  the Link
    * @param    String  the Tip
    * @return   Object  the DB Result for this UPDATE
    */
    function changeEntry($id, $namespace, $title, $link, $tip)
    {
	    $values = array( 'ID'        => $id,
	                     'NAMESPACE' => $namespace,
	                     'TITLE'     => $title,
	                     'LINK'      => $link,
	                     'TIP'       => $tip );
        $sqlString = "UPDATE {DB_PREFIX}tipoftheday SET namespace={NAMESPACE}, title={TITLE}, link={LINK}, tip={TIP} WHERE id={ID} AND cid={CID}";
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
    * Deletes a Tip-of-the-Day Entry
    *
    * @param    int     the Tip-of-the-Day Entry ID
    * @return   Object  the DB Result for this DELETE
    */
    function deleteEntry($id)
    {
	    $values = array( 'ID' => $id );
        $sqlString = "DELETE FROM {DB_PREFIX}tipoftheday WHERE id={ID} AND cid={CID}";
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }
    
    
}
