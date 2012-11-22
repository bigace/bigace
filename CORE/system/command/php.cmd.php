<?php
//
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
//

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

/**
 * This Command is specialized for loading simple PHP Files from
 * the Consumer Directory at /consumer/cid{CID}/php/.
 *
 * PHP Script can be called by using the ScriptLink:
 * import('classes.util.ScriptLink');
 * $link = new ScriptLink('helloworld.php');
 * <a href="<?php echo LinkHelper::getUrlFromCMSLink($link); ?>">Hello World</a>
 *
 * Files will be loaded by a simple "include"!
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.command
 */

// TODO sanitize

if(file_exists(_BIGACE_DIR_CID . 'php/' . $GLOBALS['_BIGACE']['PARSER']->getItemID()))
{
    include(_BIGACE_DIR_CID . 'php/' . $GLOBALS['_BIGACE']['PARSER']->getItemID());
}
else
{
	loadClass('exception', 'ExceptionHandler');
	loadClass('exception', 'NotFoundException');

    ExceptionHandler::processCoreException( new NotFoundException(404, 'File not found: ' . $GLOBALS['_BIGACE']['PARSER']->getItemID()) );
}

?>