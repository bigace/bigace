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
 * @package bigace.api
 * @subpackage portlet
 */

/**
 * The default type of a Portlet Parameter. A String.
 */
define('PORTLET_TYPE_STRING',           10);
/**
 * Defines the value as a Text. 
 * Text can be empty, is optional, and can be a mix of
 * Character and Numbers.  
 * Text will call <code>strip_tags()</code> on its value. 
 */
define('PORTLET_TYPE_TEXT',             15);
/**
 * An int value is always numeric.
 */
define('PORTLET_TYPE_INT',              20);
/**
 * An int value that must be greater or equal 0.
 */
define('PORTLET_TYPE_INT_POSITIVE',     22);
/**
 * Behaves like <code>PORTLET_TYPE_INT</code>, but might also be empty. 
 */
define('PORTLET_TYPE_INT_OPTIONAL',     25);
/**
 * Boolean is a MUST value that will can choosen by a SelectBox.
 */
define('PORTLET_TYPE_BOOLEAN',          30);
/**
 * HTML behaves like <code>PORTLET_TYPE_TEXT</code>, 
 * but keeps all TAGs. 
 */
define('PORTLET_TYPE_HTML',             40);
/**
 * Menu ID must be a Number that might be negative.
 */
define('PORTLET_TYPE_MENUID',           50);
/**
 * This can be an empty Value. Otherwise it behaves like: <code>PORTLET_TYPE_MENUID</code>
 */
define('PORTLET_TYPE_MENUID_OPTIONAL',  55);
/**
 * Defines a Language ID.
 */
define('PORTLET_TYPE_LANGUAGE',         60);
/**
 * Defines an optional Language ID. Must not be filled with a value.
 */
define('PORTLET_TYPE_LANGUAGE_OPTIONAL',65);

/**
 * A Portlet is a small piece of Logic, normally displayed by a HTML snippet.
 * 
 * This class is the base class for all Portlets.
 * The method <code>getAllParameter()</code> must always return all
 * possible Parameter for your Portlet.
 * <br>
 * Therefor your implementation MUST call <code>setParameter($key, $value)</code> 
 * for each possible Parameter in its constructor!
 * <br> 
 * Make sure to override <code>getParameterType(key)</code> and return 
 * proper PortletType definitions:
 * <br>
 * - <code>PORTLET_TYPE_STRING</code> 
 * - <code>PORTLET_TYPE_TEXT</code> 
 * - <code>PORTLET_TYPE_INT</code> 
 * - <code>PORTLET_TYPE_INT_POSITIVE</code> 
 * - <code>PORTLET_TYPE_INT_OPTIONAL</code> 
 * - <code>PORTLET_TYPE_BOOLEAN</code> 
 * - <code>PORTLET_TYPE_HTML</code> 
 * - <code>PORTLET_TYPE_MENUID</code> 
 * - <code>PORTLET_TYPE_MENUID_OPTIONAL</code> 
 * - <code>PORTLET_TYPE_LANGUAGE</code> 
 * - <code>PORTLET_TYPE_LANGUAGE_OPTIONAL</code> 
 *  
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id$
 * @package bigace.api
 * @subpackage portlet
 */
class Portlet
{
    /**
     * @access private
     */
    var $params = array();
    
    function Portlet() {
    }
    
    /**
     * Sets a Portlet Parameter.
     */
    function setParameter($key, $value) {
        if ($this->getParameterType($key) == PORTLET_TYPE_TEXT)
            $value = strip_tags($value);
        $this->params[$key] = $value;
    }
    
    /**
     * Gets a Portlet Parameter.
     * @return mixed the Value for the Key or the Fallback
     */
    function getParameter($key, $fallback = '') {
        if (isset($this->params[$key]))
            return $this->params[$key];
        return $fallback;
    }
    
    /**
     * Gets all configured Portlet Parameter.
     * <br>
     * Make sure this method always returns all possible Values,
     * cause they are used for the dynamic Portlet Configuration.
     * @return array an Array with all avialable Key-Value Parameter
     */
    function getAllParameter() {
        return $this->params;
    }
    
    /**
     * Get the Identifier for this Portlet. 
     * @return String the Unique Identifier for this Portlet
     */
    function getIdentifier() {
        return get_class($this);
    }
    
    // ----------------------- REQUIRED TO REIMPLEMENT -----------------------

    /**
     * <b>REQIURED TO BE OVERWRITEN!</b>
     * <br>
     * Returns the PortletType for the given Key.
     * Your implementation should use a switch to return proper 
     * PortletType.
     * <br>
     * The default implementation returns <code>PORTLET_TYPE_STRING</code>.
     * @return mixed one of the available Portlet Types
     */
    function getParameterType($key) {
        return PORTLET_TYPE_STRING;
    }
    
    /**
     * Returns the Name of the given Parameter.
     * The Name will be used in Administration Masks to increase the usability for End User.
     * @return String the Name for the given Parameter
     */
    function getParameterName($key) {
        return $key;
    }
    
    /**
     * <b>REQIURED TO BE OVERWRITEN!</b>
     * <br>
     * Return the Title for this Portlet.
     * @return String the Title for this Portlet
     */
    function getTitle() {
        return 'Reimplement getTitle() for Portlet with ID: ' . $this->getIdentifier();
    }
    
    /**
     * <b>REQIURED TO BE OVERWRITEN!</b>
     * <br>
     * Return the HTML snippet that this Portlet represents.
     * @return String the HTML that should be displayed
     */
    function getHtml() {
        return '';
    }

    // ----------------------- OPTIONAL TO REIMPLEMENT -----------------------

    /**
     * <b>OPTIONAL TO BE OVERWRITEN!</b>
     * <br>
     * This method defines if the Portlet needs a Javascript block to work.
     * The default implementation checks if <code>getJavascript()</code> 
     * returns a String with length greater than zero.
     * @return boolean whether this Portlet needs some Javascript to work
     */
    function needsJavascript() {
        return (strlen($this->getJavascript()) > 0);
    }
    
    /**
     * <b>OPTIONAL TO BE OVERWRITEN!</b>
     * <br>
     * Overwrite if your Portlet needs a Javascript snippet to work.
     * @return String the Javascript for this Portlet
     */
    function getJavascript() {
        return '';
    }

    /**
     * <b>OPTIONAL TO BE OVERWRITEN!</b>
     * <br>
     * Return if this Portlet should be displayed or not.
     * You might use this to display stateful Portlets, like a Login,
     * that should only be diplayed to Anonymous User.
     * <br>
     * Default returns <code>true</code>.
     * @return boolean whether this Portlet should be displayed or not
     */
    function displayPortlet() {
        return true;
    }

    
}
 
?>