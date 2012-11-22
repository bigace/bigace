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
 * This Command is specialized for displaying default Applications that might
 * can used all over the System (from each Community).
 *
 * The Application Definition is kept in the File:
 * /system/application/application.ini
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.command
 */

/**
 * @access private
 */
define('_BIGACE_DIR_APPLICATIONS', _BIGACE_DIR_ROOT.'/system/application/');
/**
 * @access private
 */
define('APPLICATIONS_INI', _BIGACE_DIR_APPLICATIONS.'application.ini');

/**
 * @access private
 */
class CMSApplicationService
{

    private $settings = array();

    function CMSApplicationService() {
        $this->settings = parse_ini_file(APPLICATIONS_INI,TRUE);
    }

    function getApplication($name) {
        $settingsName = $name;
        // if name was a dummy name switch it
        if(isset($this->settings['application'][$name])) {
            $settingsName = $this->settings['application'][$name];
        }

        if(isset($this->settings[$settingsName]))
            return new CMSApplication($name, $this->settings[$settingsName]);

        return null;
    }
}

/**
 * @access private
 */
class CMSApplication
{
    private $settings = array();
    private $name = "";

    function CMSApplication($name, $applicationSettings) {
        $this->name = $name;
        $this->settings = $applicationSettings;
    }

    function getFullFilename($subAction = 'default') {
        return $this->settings[$subAction];
    }

    function getName() {
        return $this->name;
    }
}

$cmsAppService = new CMSApplicationService();
$app = $cmsAppService->getApplication( $GLOBALS['_BIGACE']['PARSER']->getAction() );

if($app != null)
{
	$appFilename = _BIGACE_DIR_APPLICATIONS.$app->getFullFilename();

    if($GLOBALS['_BIGACE']['PARSER']->getSubAction() != null) {
        $appFilename = _BIGACE_DIR_APPLICATIONS.$app->getFullFilename($GLOBALS['_BIGACE']['PARSER']->getSubAction());
    }

    if($appFilename != null && $appFilename != _BIGACE_DIR_APPLICATIONS && file_exists($appFilename)) {
    	require_once($appFilename);
    }
    else {
		import('classes.exception.ExceptionHandler');
		import('classes.exception.NotFoundException');
    	ExceptionHandler::processCoreException( new NotFoundException(404, 'Application does not exist: ' . $GLOBALS['_BIGACE']['PARSER']->getAction() . '/' . $GLOBALS['_BIGACE']['PARSER']->getSubAction()) );
    }
}
else
{
	import('classes.exception.ExceptionHandler');
	import('classes.exception.NotFoundException');

    ExceptionHandler::processCoreException( new NotFoundException(404, 'Application does not exist: ' . $GLOBALS['_BIGACE']['PARSER']->getAction() . '/' . $GLOBALS['_BIGACE']['PARSER']->getSubAction()) );
}

?>