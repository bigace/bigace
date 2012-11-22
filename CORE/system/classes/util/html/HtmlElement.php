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
 * This is the Base class for all HTML Elements.
 * Call <code>getHtml()</code> to get the rendered HTML.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.html
 */
class HtmlElement
{
    /**
     * @access private
     */
    var $tagAttributes = array();
    /**
     * @access private
     */
    var $tagName = '';
    /**
     * @access private
     */
    var $simpleTag = false;
    /**
     * @access private
     */
    var $childElements = array();
    /**
     * @access private
     */
    var $allowEmptyAttributes = false;

    function HtmlElement() {
    }
    
    function setAllowEmptyAttributes($allow) {
    	$this->allowEmptyAttributes = $allow;
    }

    function setTagName($name) {
        $this->tagName = $name;
    }

    function addChildElement($element) {
        array_push($this->childElements,$element);
    }

    function getChildElements() {
        return $this->childElements;
    }

    function setIsSimpleTag($simpleTag) {
        $this->simpleTag = $simpleTag;
    }

    function isSimpleTag() {
        return $this->simpleTag;
    }

    function setClass($class) {
        $this->setTagAttribute('class', $class);
    }

    function getClass() {
        return $this->getTagAttribute('class');
    }

    function setID($id) {
        $this->setTagAttribute('id', $id);
    }

    function getID() {
        return $this->getTagAttribute('id');
    }

    function setName($name) {
        $this->setTagAttribute('name', $name);
    }

    function getName() {
        return $this->getTagAttribute('name');
    }

    function setTagAttribute($key, $value) {
        $this->tagAttributes[$key] = $value;
    }

    function getTagAttribute($key) {
        if(isset($this->tagAttributes[$key]))
            return $this->tagAttributes[$key];
        return null;
    }

    function getHtml() {
        $html  = '<' . $this->tagName;
        foreach($this->tagAttributes AS $key => $value) {
            // this line was added for xhtml compatibility (selected="selected") 
        	if($value == '' && !$this->allowEmptyAttributes) {
        		$value = $key;
        	}
            $html .= ' ' . $key . '="' . $value . '"';
        }
        if($this->isSimpleTag())
            $html .= ' /';
        $html .= '>';

        if(!$this->isSimpleTag()) {
            while(($child = array_shift($this->childElements)) != NULL) {
                $html .= $child->getHtml();
            }
            $html .= '</' . $this->tagName .'>';
        }
        return $html;
    }

}

?>