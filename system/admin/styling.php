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
 * Include for styling.
 */
import('classes.administration.AdminStyleService');

$STYLE_SERVICE = new AdminStyleService();

if ($GLOBALS['_BIGACE']['SESSION']->isValueSet('STYLE')) {
    $GLOBALS['_BIGACE']['style']['class'] = $STYLE_SERVICE->loadStyle($GLOBALS['_BIGACE']['SESSION']->getSessionValue('STYLE'));
} else {
    $GLOBALS['_BIGACE']['style']['class'] = $STYLE_SERVICE->getConfiguredStyle();
}

define('_BIGACE_DIR_STYLE', $GLOBALS['_BIGACE']['style']['class']->getWebDirectory());
$GLOBALS['_BIGACE']['style']['DIR'] = _BIGACE_DIR_STYLE;

function createStyledBackLink($link, $text = null)  
{
    return '<div class="backDiv"><a class="back imgbtn" href="'.$link.'">'.($text == null ? getTranslation('back') : $text).'</a></div>';
} 
 
function displayError($message) 
{ 
    echo '<h3 class="error">'.$message.'</h3>';
} 
 
function displayMessage($message) 
{ 
    echo '<h3 class="info">'.$message.'</h3>';
}
