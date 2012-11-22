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
 * @package bigace.presentation
 * @subpackage definition
 */

$DEFINITION['BLIX'] = array (

    'NAME'          => 'BLIX',
    'TITLE'         => 'BLIX - Spring Flavour',
    'STANDARD'      => 'spring_flavour/blix.php',
    'DESCRIPTION'   => 'BLIX - Spring Flavour',
    'KEYS'          => array (
                        'searchmask'    => 'spring_flavour/search.php',
                        'searchresult'  => 'spring_flavour/search.php'
                       ),
    'PUBLIC'        => true,
    'portlet'       => array( 
                        'ToolPortlet', 
                        'NavigationPortlet', 
                        'LastEditedItemsPortlet',
                        'LoginMaskPortlet',
                        'QuickSearchPortlet'
                       ),
    'CSS'			=> _BIGACE_DIR_PUBLIC_WEB . 'spring_flavour/editor.css',
    'fckstyles'		=> _BIGACE_DIR_PUBLIC_WEB . 'spring_flavour/fckstyles.xml',
    'fcktemplates'	=> _BIGACE_DIR_PUBLIC_WEB . 'spring_flavour/fcktemplates.xml'

);

?>