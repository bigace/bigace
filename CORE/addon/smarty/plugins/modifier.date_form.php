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

/*
 * Smarty modifier
 * -------------------------------------------------------------
 * File:     modifier.date_form.php
 * Purpose:  Encodes the timestamp as RFC 2822 compatible date
 *           or by using the date function.
 * $Id$
 * -------------------------------------------------------------
 */
function smarty_modifier_date_form($str, $format = 'r')
{
    return date($format, $str);
}

