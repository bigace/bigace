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
 * @package addon.smarty
 * @subpackage function
 */ 

import('classes.util.FAQForm');

/** 
 * Fetches the latest FAQs from ALL categories.
 *
 * Parameter:
 * - assign
 * - order (default: ASC - possible ASC/DESC)
 * - start (default: 0 - possible int)
 * - end (default: 10 - possible int)
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 */
function smarty_function_faq_latest($params, &$smarty)
{	
	if(!isset($params['assign'])) {
		$smarty->trigger_error("faq_latest: missing 'assign' attribute");
		return;
	}
	
	$FORM = new FAQForm();
	$entries = $FORM->get_generic_entries_last($params);
	    
    $smarty->assign($params['assign'], $entries);
}

?>