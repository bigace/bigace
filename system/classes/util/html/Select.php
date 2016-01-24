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

import('classes.util.html.HtmlElement');
import('classes.util.html.Option');

/**
 * This class defines a HTML Select Box ...
 * Set all values and call <code>getHtml()</code>.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.html
 */
class Select extends HtmlElement
{

    function Select() {
        $this->setTagName('select');
    }

    function addOption($option) {
        $this->addChildElement($option);
    }

    function getOptions() {
        return $this->getChildElements();
    }

    function setIsMultiple() {
        $this->setTagAttribute('multiple', 'multiple');
    }

    function setSize($size) {
        $this->setTagAttribute('size', $size);
    }

    function setOnChange($onChange) {
        $this->setTagAttribute('onChange', $onChange);
    }

    /**
     * Adds a bunch of Option Tags to the Select Box by iterating above the passed
     * key-value mapped Array.
     */
    function addOptionTags($keyValueArray = array(), $preselected = NULL)
    {
        foreach ($keyValueArray AS $key => $val) {
            $o = new Option();
            $o->setValue($val);
            $o->setText($key);
            if ($preselected != NULL && $preselected == $val) {
                $o->setIsSelected();
            }
            $this->addOption($o);
        }
    }

}

?>