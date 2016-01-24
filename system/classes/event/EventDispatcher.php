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
 * @subpackage event
 */

import('classes.event.Event');
import('classes.util.ObjectHelper');

/**
 * Broadcast messages through the system to registered listener usig this class.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage event
 */
class EventDispatcher
{
	
	
	//TODO add a simple - static - way of registering event listener with the database
	
	/**
 	 * Call EventDispatcher::propagateEvent('foo', array()); for sending the event 'foo' with no parameter.
	 * Call EventDispatcher::propagateEvent('bar', array('id' => 1)); for sending the event 'bar' with the parameter id = 1.
	 *
	 * @param String $type the event type to broadcast
	 * @param Array $params the parameter to send along
	 */
	function propagateEvent($type, $params = array()) {
		$event = new Event($type, $params);
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('events_load_type');
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array('EVENT' => $type), true);
	    $events = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	    for($i = 0; $i < $events->count(); $i++) {
	    	$listener = $events->next();
	    	$listClass = createInstance($listener['class']);
	    	if(!$event->isCanceled()) {
				if(ObjectHelper::is_class_of($listClass, "EventListener")) {
                    try {
					$listClass->handleEvent($event);
                    } catch (Exception $e) {
    					$GLOBALS['LOGGER']->logError('EventListener '.$listener['class'].' crashed for event "'.$type . 'with message: ' . $e->getMessage());
                    }
				} else {
					$GLOBALS['LOGGER']->logError('EventListener '.$listener['id'].' ('.$listener['class'].') for type "'.$type.'" is not subclass of EventListener!' );
                }
}
	    }
	}
}
