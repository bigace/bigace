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
 * @subpackage util.formular
 */

import('classes.util.html.Select');
import('classes.administration.EditorHelper');

/**
 * This class defines a Select Box to select one of the
 * installed HTML Editors.
 * Set all values and call <code>getHtml()</code>.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.formular
 */
class EditorSelect extends Select
{
    /**
     * @access private
     */
    var $preSelectedID = null;

    function EditorSelect() {
        parent::Select();
    }

    function setPreSelected($id) {
        $this->preSelectedID = $id;
    }
    
    function getHtml() {
		$allEditors = bigace_get_all_editor();
		foreach($allEditors AS $tf) {
        		$o = new Option($tf, $tf);
        		if($this->preSelectedID != null && $this->preSelectedID == $tf)
        			$o->setIsSelected();
        		$this->addOption($o);
        }
    	return parent::getHtml();
    }

}

?>