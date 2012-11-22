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
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.smarty
 * @subpackage function
 */

import('classes.configuration.ConfigurationReader');

/**
 * Returns the configured sitename.
 * 
 * Parameter:
 * - default	= 
 */
function smarty_function_sitename($params, &$smarty)
{
	$default = (isset($params['default']) ? $params['default'] : '');
	return ConfigurationReader::getConfigurationValue('community', 'sitename', $default);
}
