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
 * This prints the BIGACE copyright footer.
 * 
 * This uses the CSS Classes:
 * - CopyrightFooter
 * - copyright
 * 
 * If you want to add a Copyright Footer to your Application or Layout simply add:
 * <code>
 * import('classes.util.html.CopyrightFooter');
 * CopyrightFooter::toString();
 * </code>
 * 
 * NOTICE:
 * I request you retain the full copyright notice below including the link to the BIGACE Homepage.
 * This not only gives respect to the large amount of time given freely by the developer
 * but also helps build interest, traffic and use of BIGACE. If you cannot (for good reason) 
 * retain the full copyright I request you at least leave in place the  "Powered by" line, 
 * linked to http://sourceforge.net/projects/bigace/.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.html
 */
class CopyrightFooter
{
    /**
     * Returns the HTML for the Copyright Footer.
     * @return String the HTML to use
     */
    public static function get() {
        return '<div align="center" class="CopyrightFooter"><span class="copyright">'.
               'Powered by <a href="http://www.bigace.de/" target="_blank">BIGACE ' . _BIGACE_ID. '</a>.' .
               '&nbsp;All rights reserved. <br />&copy; 2002-'.date('Y').' <a href="http://www.kevinpapst.de/" target="_blank">Kevin Papst</a>' .
               '<br /></span></div>'; 
    }
    
    /**
     * Prints the Copyright Footer to the Standard Out.
     */
    public static function toString() {
        echo CopyrightFooter::get();
    }
}

?>