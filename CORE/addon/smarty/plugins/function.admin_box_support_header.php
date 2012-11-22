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

/**
 * Dumps everything we need to activate Admin Box support for this page.
 * You need to call {admin_box_support_footer} at the end of the page!
 * 
 * See {admin_box_header} for a working example. 
 */
function smarty_function_admin_box_support_header($params, &$smarty)
{
    // load the class only in here, because it needs to be called only 
    // once before we start an admin page with box support
    import('classes.administration.AdminBoxes');
    
    echo AdminBoxes::get()->getPageHeader();
	return;
}
