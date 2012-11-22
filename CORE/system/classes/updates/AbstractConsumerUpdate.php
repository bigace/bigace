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
 * @subpackage updates
 */
 
import('classes.updates.AbstractUpdate');

/**
 * This class is used as interface for Consumer Update Jobs!
 *
 * Your class will be first instantiated, then the install($cid) method 
 * will be executed, with the ConsumerID as argment.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage updates
 */
abstract class AbstractConsumerUpdate extends AbstractUpdate
{	
	function AbstractConsumerUpdate() {
	}
	
	/**
	 * Overwrite this method to perform your installation/update.
	 * Return an boolean, whether it was successful or not. 
	 * 
	 * @param int the Consumer ID
	 * @return boolean the result
	 */
	abstract function install($cid);
	
}

?>