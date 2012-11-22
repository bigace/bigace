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
 * This class can be used to create a template based Feedback Message.
 * The Template is Administratiopn Style dependend and can be called 
 * via the 'answer_box.php' Admin-Include.
 * 
 * It can be used like that:
 * <code>
 * include_once(_BIGACE_DIR_ADMIN.'include/answer_box.php');
 * 
 * $def = new AnswerViewDefinition();
 * $def->setTitle( getTranslation('Successful Feedback Title') );
 * $def->setButtonLabel(getTranslation('back'));
 * $def->setStateIcon('success.png');
 * $def->setMessageValues( array( 'Name' => 'Result Name') );
 * $def->setHiddenValues( array('data[id]' => '-1') );
 * $def->setLink(createAdminLink($GLOBALS['MENU']->getID()));
 * displayAnswerViewDefinition($def); 
 * </code>
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage administration
 */
class AnswerViewDefinition
{
	/**
	 * @access private
	 */
	var $title = '';
	/**
	 * @access private
	 */
	var $messageRows = array();
	/**
	 * @access private
	 */
	var $link = '';
	/**
	 * @access private
	 */
	var $hidden = array();
	/**
	 * @access private
	 */
	var $buttonLabel = '';
	/**
	 * @access private
	 */
	var $icon = null;
	/**
	 * @access private
	 */
	var $stateIcon = null;
	
	/**
	 * Default Constructor.
	 */
	function AnswerViewDefinition() {
		$this->buttonLabel = getTranslation('next');
	}
	
	function getStateIcon() {
		return $this->stateIcon;
	}

	/**
	 * Sets the Name of the State Icon. Pass a full Filename of
	 * an Image that is located below the Styles Root Directory.
	 * You even may pass something like 'images/test.gif'....   
	 * 
	 * @param String the Name of the Image File
	 */
	function setStateIcon($icon) {
		$this->stateIcon = $icon;
	}
	
	function getTitleIcon() {
		return $this->icon;
	}

	function setTitleIcon($icon) {
		$this->icon = $icon;
	}
	
	function getTitle() {
		return $this->title;
	}

	function setTitle($title) {
		$this->title = $title;
	}
	
	function getButtonLabel() {
		return $this->buttonLabel;
	}

	function setButtonLabel($label) {
		$this->buttonLabel = $label;
	}

	function getLink() {
		return $this->link;
	}

	function setLink($url) {
		$this->link = $url;
	}

	function getHiddenValues() {
		return $this->hidden;
	}

	/**
	 * Set some hidden values, that should not appear within the Link.
	 * These values are Key-Value Pairs in an Array.
	 * For example:
	 * <code>
	 * array('name' => 'foo', 'description' => 'bar');
	 * </code>
	 * 
	 * @param array all Values that will mormally be submitted in hidden input types
	 */
	function setHiddenValues($hidden) {
		$this->hidden = $hidden;
	}

	function getMessageValues() {
		return $this->messageRows;
	}

	function setMessageValues($msg) {
		$this->messageRows = $msg;
	}

}

?>