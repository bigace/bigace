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
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.administration
 */

/**
 * Script used for the administration of Images.
 */

check_admin_login();
admin_header();

import('classes.image.Image');
import('classes.image.ImageAdminService');

$_ITEMTYPE = _BIGACE_ITEM_IMAGE;
$_ADMIN = new ImageAdminService();

include_once(_ADMIN_INCLUDE_DIRECTORY . 'item_listing.php');
include_once(_ADMIN_INCLUDE_DIRECTORY . 'item_main_default.php');
include_once(_ADMIN_INCLUDE_DIRECTORY . 'item_main.php');

// execute whatever was done by the user
propagateAdminMode();

// show the footer
admin_footer();

?>