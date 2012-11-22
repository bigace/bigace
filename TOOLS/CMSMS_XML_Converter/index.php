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
 * @package bigace.tools
 */

/**
 * This file contains the logic, required to convert a CMSMS Template into a BIGACE Design.
 */

define('CMSMS_FOR_BIGACE', 'CMSMS_FOR_BIGACE');

require_once(dirname(__FILE__).'/cmsms_classes.php');
require_once(dirname(__FILE__).'/cmsms_converter.php');


// ############################################################################################
// SET THE FILENAME, YOU ARE GOING TO CONVERT!
// You shouldn't have to touch anything else in here...
$convertFile = "templates/andreas01.xml";
// ############################################################################################

$imp = new CMSMS_TemplateImporter();
$imp->importFile($convertFile);

echo 'Name: ' . $imp->name->getValue();
echo '<br>';
echo 'DTD Version: ' . $imp->dtd->getValue();

$exp = new CMSMS_TemplateConverter($imp);
$exp->createExport(dirname(__FILE__));

?>
