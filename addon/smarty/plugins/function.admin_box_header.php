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
 * Creates the header of a (toggable) Admin Box.
 * 
 * If you want to create an admin plugin and keep it in BIGACE Look&Feel,
 * you should use an "Admin Box" to structure you pages content.
 * 
 * Write an Smarty Template and add something like this:
 * =======================================================
 * {admin_box_support_header}
 * 
 *  <p>Administrate the "Foo Bar" Plugin here:</p>
 *
 *  {admin_box_header title="Your Box Title" toggle=false}
 *    <form action="" method="post">
 *          ... And in between your formular ...
 *          This Box is not toggable!
 *    </form>
 *  {admin_box_footer}
 *  
 *  <p>Administrate the "Foo Bar2" Plugin here:</p>
 *  
 *  {admin_box_header title="Your Box Title 2"}
 *    <form action="" method="post">
 *          ... And in between your formular ...
 *          This Box is toggable!
 *    </form>
 *  {admin_box_footer}
 *  
 *  <p>Thanks for using the great "Foo Bar" Plugin!</p>
 *  
 * {admin_box_support_footer}
 */
function smarty_function_admin_box_header($params, &$smarty)
{
    echo AdminBoxes::get()->getBoxHeader($params);
    return;
}
