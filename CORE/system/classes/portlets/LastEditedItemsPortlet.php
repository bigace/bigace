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
import('classes.item.ItemRequests');
import('classes.item.ItemRequest');

/**
 * @access private
 */
define('LAST_EDITED_PARAM_ORDER', 'order');
/**
 * @access private
 */
define('LAST_EDITED_PARAM_CSS', 'css');
/**
 * @access private
 */
define('LAST_EDITED_PARAM_AMOUNT',   'amount');
/**
 * @access private
 */
define('LAST_EDITED_PARAM_LANGUAGE', 'language');
/**
 * @access private
 */
define('LAST_EDITED_PARAM_TITLE', 'title');

/**
 * Shows a configurable amount of Items that were last edited in the System.
 * The default CSS class is "lastEditedItemsPortlet".
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage portlets
 */
class LastEditedItemsPortlet extends TranslatedPortlet
{

    function LastEditedItemsPortlet()
    {
        // load translations
        $this->loadBundle('LastEditedItemsPortlet');

        //$this->setTitle('Last Edited Items');
        $this->setCSS("lastEditedItemsPortlet");
		$this->setLanguageID('');
		$this->setAmount(5);
    }

    function getIdentifier() {
        return 'LastEditedItemsPortlet';
    }

    function getParameterType($key) {
        switch ($key) {
            case LAST_EDITED_PARAM_AMOUNT:
                return PORTLET_TYPE_INT_POSITIVE;
            case LAST_EDITED_PARAM_LANGUAGE:
                return PORTLET_TYPE_LANGUAGE_OPTIONAL;
            case LAST_EDITED_PARAM_CSS:
                return PORTLET_TYPE_STRING;
            case LAST_EDITED_PARAM_TITLE:
                return PORTLET_TYPE_TEXT;
        }
        return PORTLET_TYPE_STRING;
    }


    /**
     * Set the Title of this Portlet.
     */
    function setTitle($title) {
        $this->setParameter(LAST_EDITED_PARAM_TITLE, $title);
    }

    /**
     *  Returns the Title of this Portlet.
     */
    function getTitle() {
        return $this->getParameter(LAST_EDITED_PARAM_TITLE, $this->getTranslation('title', 'Last Edited Items'));
    }

    function needsJavascript() {
        return false;
    }

    function getHtml()
    {
    	$ir = new ItemRequest(_BIGACE_ITEM_MENU);
    	$ir->setOrder($ir->_ORDER_DESC);
    	$ir->setLanguageID($this->getLanguageID());
    	$ir->setLimit(0, $this->getAmount());
    	$temp = bigace_last_edited_items($ir);

        $html = '<ul class="'.$this->getCSS().'">';

        for ($i=0; $i < $temp->count(); $i++)
        {
            $lastEdited = $temp->next();
            $html .= '<li><a href="' . LinkHelper::getUrlFromCMSLink(LinkHelper::getCMSLinkFromItem( $lastEdited )) . '">' . $lastEdited->getName() . '</a><br/>';
            if (strlen($lastEdited->getDescription()) > 0) {
                $html .= substr ( $lastEdited->getDescription(), 0, 50);
                if (strlen($lastEdited->getDescription()) > 53)
                    $html .= '...';
                $html .= '<br />';
            }
            $html .= '<i>' . date("d.m.Y", $lastEdited->getLastDate()) . '</i>';
            $html .= '</li>';
        }
        return $html . "</ul>\n";
    }

	// ------------------------------------------------------------

    function getCSS() {
        return $this->getParameter(LAST_EDITED_PARAM_CSS, '');
    }

    /**
     * Set the CSS Class for the Item List.
     */
    function setCSS($css = '') {
        $this->setParameter(LAST_EDITED_PARAM_CSS, $css);
    }

    /**
     * Sets the Language for the Tree to be fetched.
     * If none (empty String) is passed, it uses the current Menu Language ID.
     */
    function setLanguageID($id = '') {
        $this->setParameter( LAST_EDITED_PARAM_LANGUAGE, $id );
    }

    function getLanguageID() {
        $id = $this->getParameter( LAST_EDITED_PARAM_LANGUAGE );
        if ($id == '')
            return _ULC_;
        return $id;
    }

    /**
     * Sets the amount of Items that will be displayed.
     */
    function setAmount($amount = '') {
        $this->setParameter( LAST_EDITED_PARAM_AMOUNT, $amount );
    }

    function getAmount() {
        $id = $this->getParameter( LAST_EDITED_PARAM_AMOUNT );
        if ($id == '')
            return 5;
        return $id;
    }

}
