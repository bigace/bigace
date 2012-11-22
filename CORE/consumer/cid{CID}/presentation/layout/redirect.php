<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.<br>Copyright (C) Kevin Papst.
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
 */

loadLanguageFile('redirect', $GLOBALS['_BIGACE']['PARSER']->getLanguage());

$url = $MENU->getCatchwords();

if (trim(strlen($url)) < 1 || strpos($url, '://') === false) 
{
    echo '<p>'.getTranslation('error_wrong_url').'</p>'; 
    
    if(trim(strlen($url)) > 0)
    {
        echo '<p>'.getTranslation('error_show_url').': ' . '"' . $url . '"' .   
        '<br><ul><li><a href="http://en.wikipedia.org/wiki/Uniform_Resource_Identifier" target="_blank">Uniform Resource Identifier (Wikipedia, English)</a></li>' .
        '<li><a href="http://de.wikipedia.org/wiki/URI" target="_blank">Uniform Resource Identifier (Wikipedia, German)</a></li></ul></p>';
    }
}
else
{
    header("Location: " . $url); 
    exit;
}
