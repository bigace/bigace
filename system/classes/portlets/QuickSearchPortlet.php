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
 * @subpackage portlets
 */

import('api.portlet.TranslatedPortlet');
import('classes.util.ApplicationLinks');

/**
 * This portlets displas a Quick Search Formular.
 * It submits the entry into the Standard Search Frame.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage portlets
 */
class QuickSearchPortlet extends TranslatedPortlet
{

    function QuickSearchPortlet()
    {
        // load translations
        $this->loadBundle('QuickSearchPortlet');
    }


    function getIdentifier() {
        return 'QuickSearchPortlet';
    }

    /**
     * Set the Title of this Portlet.
     */
    function setTitle($title) {
        $this->setParameter('title', $title);
    }

    /**
     *  Returns the Title of this Portlet.
     */
    function getTitle() {
        return $this->getParameter('title', $this->getTranslation('title'));
    }

    function getHtml() {
        return '<div class="quickSearchPortlet">
            <form action="' . ApplicationLinks::getSearchURL($GLOBALS['MENU']->getID()) . '" method="post" id="quickSearch" name="quickSearch" onSubmit="javascript:return checkQuickSearch();">
            <input type="hidden" name="language" value="'.$GLOBALS['MENU']->getLanguageID().'">
                <table cellpadding="0" cellspacing="0" border="0" width="90%">
                    <tbody>
                	<tr>
                		<td>' . $this->getTranslation('searchterm') . '</td>
                	</tr>
                    <tr>
                      <td align="right"><input id="quickSearchTerm" name="search" type="text"></td>
                	</tr>
                	<tr>
                    	<td colspan="2" align="right">
                			<button class="quickSearchSubmit" type="submit">' . $this->getTranslation('submit') . '</button>
                	    </td>
                    </tr>
                    </tbody>
                </table>
            </form></div>';
    }

    function needsJavascript() {
        return true;
    }

    function getJavascript() {
        return "
            <script type=\"text/javascript\">
                function checkQuickSearch()
                {
                    if(document.getElementById('quickSearchTerm').value.length == 0) {
                        alert('".$this->getTranslation('empty_searchterm')."');
                    	return false;
    				}
                    else if(document.getElementById('quickSearchTerm').value.length < 4) {
                        alert('".$this->getTranslation('short_searchterm')."');
	                    return false;
				    }
                    return true;
                }
            </script>
        ";
    }

}