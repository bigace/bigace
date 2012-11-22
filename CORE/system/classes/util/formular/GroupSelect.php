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

import('classes.group.GroupEnumeration');
import('classes.util.html.Select');

/**
 * This class defines a HTML Select Box that lets the user select 
 * one or more Usergroups.
 * 
 * Usage: Set all required attributes and call <code>getHtml()</code>.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.formular
 */
class GroupSelect extends Select
{
    /**
     * @access private
     */
    var $preSelectedID = null;
    /**
     * @access private
     */
    var $addedGroups = false;
    /**
     * @access private
     */
    var $groupsToHide = array();

    function GroupSelect() {
        parent::Select();
    }

    function addGroupIDToHide($id) {
        $this->groupsToHide[] = $id;
    }

    function setPreSelectedID($id) {
        $this->preSelectedID = $id;
    }

    function generate() {
        if(!$this->addedGroups)
        {
            $enum = new GroupEnumeration();
            for($i=0; $i < $enum->count(); $i++)
            {
                $group = $enum->next();
                if($this->groupsToHide == null || !in_array($group->getID(), $this->groupsToHide))
                {
                    $opt = new Option();
                    $opt->setText($group->getName());
                    $opt->setValue($group->getID());
                    if($this->preSelectedID != null && $this->preSelectedID == $group->getID())
                        $opt->setIsSelected();
                    $this->addOption($opt);
                }
            }
            $this->addedGroups = true;
        }
    }

    function getHtml() {
        if(!$this->addedGroups) {
            $this->generate();
        }
        return parent::getHtml();
    }

}

?>