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
 * @subpackage portlets
 */

import('api.portlet.Portlet');

/**
 * @access private
 */
define('SKYPE_ONLINE_PARAM_UID', 'uid');
/**
 * @access private
 */
define('SKYPE_ONLINE_PARAM_MODE', 'clickMode');
/**
 * @access private
 */
define('SKYPE_ONLINE_PARAM_TITLE', 'title');

/**
 * Shows a Skype Javascript, that displays the Status of the configured Person.
 * The person must enable "Share State on the Web" in their privacy.
 * See http://www.skype.com/share/buttons/ for further information.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage portlets
 */
class SkypeOnlinePortlet extends Portlet
{

    function SkypeOnlinePortlet()
    {
        // we could make the title dynamic if there wouldn't be the javascript encoding problem
        // with spaces...
//        $this->setTitle('Skype Online Status');
        // we could also make the mode dynamic, but for now, we leave it on "call"
		//$this->setSkypeMode('call');
		$this->setSkypeUserID('');
    }

    function getIdentifier() {
        return 'SkypeOnlinePortlet';
    }

    function getParameterType($key) {
        switch ($key) {
            case SKYPE_ONLINE_PARAM_MODE:
                return PORTLET_TYPE_STRING;
            case SKYPE_ONLINE_PARAM_UID:
                return PORTLET_TYPE_TEXT;
            case SKYPE_ONLINE_PARAM_TITLE:
                return PORTLET_TYPE_TEXT;
        }
        return PORTLET_TYPE_STRING;
    }


    /**
     * Set the Title of this Portlet.
     */
    function setTitle($title) {
        $this->setParameter(SKYPE_ONLINE_PARAM_TITLE, $title);
    }

    /**
     *  Returns the Title of this Portlet.
     */
    function getTitle() {
        return "Skype";
        //return $this->getParameter(SKYPE_ONLINE_PARAM_TITLE, 'Skype Online Status');
    }

    function needsJavascript() {
        return true;
    }

    function getJavascript() {
        return '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>'."\n";
    }

    function getHtml()
    {
        if($this->getSkypeUserID() == '')
            return '<b>Unconfigured, missing Skype UID.</b>';
        return "\n" . '<p><a href="skype:'.$this->getSkypeUserID().'?'.$this->getSkypeMode().'"><img src="http://mystatus.skype.com/balloon/'.$this->getSkypeUserID().'" style="border: none;" width="150" height="60" alt="'.$this->getTitle().'" /></a></p>' . "\n";
    }

	// ------------------------------------------------------------

    /**
     * Sets the Skype User ID.
     */
    function setSkypeUserID($uid = '') {
        $this->setParameter( SKYPE_ONLINE_PARAM_UID, $uid );
    }

    function getSkypeUserID() {
        return $this->getParameter( SKYPE_ONLINE_PARAM_UID, '' );
    }

    function setSkypeMode($mode = 'call') {
        $this->setParameter( SKYPE_ONLINE_PARAM_MODE, $mode );
    }

    function getSkypeMode() {
        return $this->getParameter( SKYPE_ONLINE_PARAM_MODE, 'call' );
    }

}