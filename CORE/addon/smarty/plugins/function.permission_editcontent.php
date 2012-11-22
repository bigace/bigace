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

import('classes.permission.UseCaseEditContent');

/**
 * Checks if a User is allowed to edit a pages content
 * with one of the html editors.
 * 
 * Parameter:
 * - id
 * - assign
 */
function smarty_function_permission_editcontent($params, &$smarty)
{
	$id = (isset($params['id']) ? $params['id'] : $GLOBALS['_BIGACE']['PARSER']->getItemID());	

	$useCase = new UseCaseEditContent($id);

	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $useCase->isAllowed());
		return;
	}	

	return $useCase->isAllowed();
}
