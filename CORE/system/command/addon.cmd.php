<?php
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS for MySQL                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) Kevin Papst                                              |
// | Web           http://www.bigace.de                                     |
// | Sourceforge   http://sourceforge.net/projects/bigace/                  |
// +------------------------------------------------------------------------+
// | This source file is subject to version 2 or (at your option) any later |
// | version, of the GNU General Public License as published by the Free    |
// | Software Foundation, available at:                                     |
// | http://www.gnu.org/licenses/gpl.html                                   |
// +------------------------------------------------------------------------+
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// +------------------------------------------------------------------------+

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

/**
 * This Command is meant to include an Plugin, for the ease of creating full compatible links.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.command
 */

$filename = $GLOBALS['_BIGACE']['PARSER']->getItemID();
if($GLOBALS['_BIGACE']['PARSER']->getAction() != null) {
	$filename = $GLOBALS['_BIGACE']['PARSER']->getAction();
}

$addon = $GLOBALS['_BIGACE']['PARSER']->getItemID();

// TODO sanitize filenames
$fullname = _BIGACE_DIR_ADDON . $addon . '/' . $filename . '.php';

if(file_exists($fullname)) {
	require($fullname);
}
else
{
	import('classes.exception.ExceptionHandler');
	import('classes.exception.NotFoundException');
	ExceptionHandler::processCoreException( new NotFoundException(404, 'AddOn does not exist: ' . $addon . '/' . $filename) );
}

?>