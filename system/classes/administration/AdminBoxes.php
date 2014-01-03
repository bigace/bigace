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
 * @subpackage administration
 */

/**
 * Singleton for building Admin Boxes.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage administration
 */
class AdminBoxes
{
    private static $instance = null;
    
    private function __constructor() { }

    /**
     * Returns the Singleton instance of AdminBoxes
     * @return AdminBoxes the instance to use
     */    
    static function get() 
    {
        if(AdminBoxes::$instance === null)
            AdminBoxes::$instance = new AdminBoxes();

        return AdminBoxes::$instance;
    }

    /**
     * Returns the HTML to start a page which uses admin boxes.
     *
     * @return String the html code to use
     */
    function getPageHeader() 
    {
        return '
        <div id="poststuff">
            ';
    }

    /**
     * Returns the HTML to end a page which uses admin boxes.
     *
     * @return String the html code to use
     */
    function getPageFooter()
    {
        return'
        </div>

        <script type="text/javascript">
        $(document).ready( function() { 
		        $(".postbox h3").prepend(\'<a class="togbox">+</a> \');	
		        $(".postbox h3").click( function() { $($(this).parent().get(0)).toggleClass("closed"); } );
            } 
        );
        </script>
        ';
    }
    
    /**
     * Returns the HTML to start a new admin box.
     *
     * Params is an array with the following keys:
     * array('title' => 'Box Title', 'toggle'  => true, 'closed' => false, 'style' => '')
     * where style is an additional css style.
     * 
     * @return String the html code to use
     */
    function getBoxHeader($params) 
    {
        $title  = (isset($params['title'])) ? $params['title'] : '* No title set *';
        $toggle = (isset($params['toggle'])) ? (bool)$params['toggle'] : true;
        $closed = (isset($params['closed'])) ? (bool)$params['closed'] : false;
        $style  = (isset($params['style'])) ? ' style="'.$params['style'].'"' : '';
        
        if($toggle) {
            return '
            <div class="postbox'.($closed ? ' closed' : '').'"'.$style.'>
                <h3>'.$title.'</h3>
                <div class="inside">
            ';
        }
        
        return '
        <div class="stuffbox"'.$style.'>
            <h3>'.$title.'</h3>
            <div class="inside">
        ';
    }
    
    /**
     * Returns the HTML to end a admin box.
     *
     * Params is an array, which is currently not used.
     * 
     * @return String the html code to use
     */
    function getBoxFooter($params) 
    {
        return '
            </div>
        </div>
        ';
    }

}

