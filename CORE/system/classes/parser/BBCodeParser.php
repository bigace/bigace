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
 * @package bigace.classes
 * @subpackage parser
 */

/**
 * Class used for parsing BBCode into HTML. 
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage parser
 */
class BBCodeParser 
{

    function BBCodeParser() {
    }
    
    /**
     * Returns HTML for the given BBCode.
     * @param String bbcode the BBCode to parse to HTML
     * @param boolean strip_html if HTML Tags should be stripped or kept
     */
    function parse($bbcode, $strip_html=true)
    {
	    $s = stripslashes($bbcode); 
	    // This fixes the extraneous ;) smilies problem. When there was an html escaped
	    // char before a closing bracket - like >), "), ... - this would be encoded
	    // to &xxx;), hence all the extra smilies. I created a new :wink: label, removed
	    // the ;) one, and replace all genuine ;) by :wink: before escaping the body.
	    // (What took us so long? :blush:)- wyz
	    $s = str_replace(";)", ":wink:", $s);
	
	    if ($strip_html)
	        $s = htmlspecialchars($s); 
	     // [center]Centered text[/center]
	    $s = preg_replace("/\[center\]((\s|.)+?)\[\/center\]/i", "<center>\\1</center>", $s); 
	    // [list]List[/list]
	    $s = preg_replace("/\[list\]((\s|.)+?)\[\/list\]/", "<ul>\\1</ul>", $s); 
	    // [list=disc|circle|square]List[/list]
	    $s = preg_replace("/\[list=(disc|circle|square)\]((\s|.)+?)\[\/list\]/", "<ul type=\"\\1\">\\2</ul>", $s); 
	    // [list=1|a|A|i|I]List[/list]
	    $s = preg_replace("/\[list=(1|a|A|i|I)\]((\s|.)+?)\[\/list\]/", "<ol type=\"\\1\">\\2</ol>", $s); 
	    // [*]
	    $s = preg_replace("/\[\*\]/", "<li>", $s); 
	    // [b]Bold[/b]
	    $s = preg_replace("/\[b\]((\s|.)+?)\[\/b\]/", "<b>\\1</b>", $s); 
	    // [i]Italic[/i]
	    $s = preg_replace("/\[i\]((\s|.)+?)\[\/i\]/", "<i>\\1</i>", $s); 
	    // [u]Underline[/u]
	    $s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/", "<u>\\1</u>", $s); 
	    // [u]Underline[/u]
	    $s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/i", "<u>\\1</u>", $s); 
	    // [img]http://www/image.gif[/img]
	    $s = preg_replace("/\[img\]([^\s'\"<>]+?)\[\/img\]/i", "<img src=\"\\1\" alt=\"\" border=\"0\">", $s); 
	    // [img=http://www/image.gif]
	    $s = preg_replace("/\[img=([^\s'\"<>]+?)\]/i", "<img src=\"\\1\" alt=\"\" border=\"0\">", $s); 
	    // [color=blue]Text[/color]
	    $s = preg_replace("/\[color=([a-zA-Z]+)\]((\s|.)+?)\[\/color\]/i",
	        "<font color=\\1>\\2</font>", $s); 
	    // [color=#ffcc99]Text[/color]
	    $s = preg_replace("/\[color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]((\s|.)+?)\[\/color\]/i",
	        "<font color=\\1>\\2</font>", $s); 
	    // [url=http://www.example.com]Text[/url]
	    $s = preg_replace("/\[url=([^()<>\s]+?)\]((\s|.)+?)\[\/url\]/i",
	        "<a href=\"\\1\">\\2</a>", $s); 
	    // [url]http://www.example.com[/url]
	    $s = preg_replace("/\[url\]([^()<>\s]+?)\[\/url\]/i",
	        "<a href=\"\\1\">\\1</a>", $s); 
	    // [size=4]Text[/size]
	    $s = preg_replace("/\[size=([1-7])\]((\s|.)+?)\[\/size\]/i",
	        "<font size=\\1>\\2</font>", $s); 
	    // [font=Arial]Text[/font]
	    $s = preg_replace("/\[font=([a-zA-Z ,]+)\]((\s|.)+?)\[\/font\]/i",
	        "<font face=\"\\1\">\\2</font>", $s);
	    // Linebreaks
	    $s = nl2br($s); 
	    // [pre]Preformatted[/pre]
	    $s = preg_replace("/\[pre\]((\s|.)+?)\[\/pre\]/i", "<tt><nobr>\\1</nobr></tt>", $s); 
	    // Maintain spacing
	    $s = str_replace("  ", " &nbsp;", $s);
	    return $s;
    }
    
}

?>