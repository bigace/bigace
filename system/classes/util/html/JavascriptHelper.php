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
 * @package bigace.classes
 * @subpackage util.html
 */

/**
 * This class defines methods some helper methods for Javascript handling.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.html
 */
class JavascriptHelper
{
    /**
     * Generates the complete (see following example) HTML code to insertr into your Page
     * to have a Javascript function to open a PopUp:
     * <code>
     * &lt;script type="text/javascript"&gt;
     *  function func_name() {
     *      ... more generated code ...
     *  }
     * &lt;/script&gt;
     * </code> 
     * 
     * Use like this: 
     * <code>
     * echo JavascriptHelper::createJSPopup('openYourFoo', 'FooTitle', '(screen.width)', '(screen.height)', 'http://www.example.com',array(),'no','yes',false);
     * </code>
     * 
     * @param String func_name the Javascript function name
     * @param String title the title of the Popup
     * @param int width the width of the Popup
     * @param int height the height of the Popup
     * @param String link the URL to open in the Popup
     * @param array modifier an array of String that will be used as Parameter in the function method
     * @param String scrollbar the HTML value to display an Scrollbar or not (yes/no)
     * @param String resizable the HTML value to decide whether the Popup is resizable or not (yes/no)
     * @param boolean appendModifier decide if the Modifiers will be appended to the Link URL or not
     */
    static function createJSPopup($func_name, $title, $width, $height, $link, $modifier = array(), $scrollbar = 'no', $resizable = 'no', $appendModifier = true)
    {
        $html = "\n";
        $html .= '<script type="text/javascript">';
        $html .= 'function ' . $func_name . ' (';
        for ($i=0; $i<count($modifier); $i++) 
        {
            $html .= 'val'.$i;
            if (count($modifier) > 1 && $i < count($modifier)-1) {
                $html .= ',';
            }
        }
        $html .= ') {';
        $html .= 'fenster = open ("' . $link;
        if (count($modifier) == 0 || !$appendModifier) {
            $html .= '"';
        } else {
            if (!preg_match("/\?/",$html))
                $html .= '?';
            else
                $html .= '&';
                
            for ($i=0; $i<count($modifier); $i++) 
            {
                $html .= $modifier[$i].'="+val'.$i;
                if ($i < count($modifier)-1) {
                    $html .= '+"&';
                }
            }
        }
        $html .= ',"'.$title.'","menubar=no,toolbar=no,statusbar=no,directories=no,location=no,scrollbars='.$scrollbar.',resizable='.$resizable.',height='.$height.',width='.$width.',screenX=0,screenY=0");';
        $html .= 'bBreite=screen.width;';
        $html .= 'bHoehe=screen.height;';
        $html .= 'fenster.moveTo((bBreite-'.$width.')/2,(bHoehe-'.$height.')/2);';
        $html .= '}';
        $html .= '</script>';
        return $html;
    }    
}

/**
 * Maps to JavascriptHelper::createJSPopup(...)
 * 
 * @deprecated DO NOT USE THIS METHOD, BUT USE THE CLAS METHOD INSTEAD!
 */
function createJSPopup($func_name, $title, $width, $height, $link, $modifier = array(), $scrollbar = 'no', $resizable = 'no')
{
    JavascriptHelper::createJSPopup($func_name, $title, $width, $height, $link, $modifier, $scrollbar, $resizable);
}

?>