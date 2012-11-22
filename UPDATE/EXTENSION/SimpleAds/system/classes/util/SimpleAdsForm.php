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
 * @subpackage util
 */

import('classes.util.GenericForm');

/**
 * The base class for the Simple ADs System.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util
 */
class SimpleAdsForm extends GenericForm
{
    public function __construct() {
        $opts = array(
                'perm-edit'     => 'simple.ads',
                'perm-create'   => 'simple.ads',
        );
        $this->GenericForm('simpleads', $opts);
    }

    public function style_section_tools($section) {
        return '';
    }

    public function has_section_tools() {
        return false;
    }

    public function style_input_entry($name = '', $value = '', $sectionId = null) {
        return '
            <table border="0">
            <tr><td style="background-color:transparent">'.getTranslation("generic_name").'</td>
            <td style="background-color:transparent"><input size="50" type="text" name="'.GENERIC_PARAM_NAME.'" value="'.$name.'">' .
            '<br/>' . ($name == '' ? getTranslation('name_unique') : getTranslation('copy_content').': [ad]'.$name.'[/ad]') .
            '</td></tr>
            <tr><td style="background-color:transparent">'.getTranslation("generic_value").'</td>
            <td style="background-color:transparent"><textarea rows="5" cols="40" name="'.GENERIC_PARAM_VALUE.'">'.$value.'</textarea>'.
            ($name != '' ? ' <button type="submit">'.getTranslation("save").'</button>' : '').
            '</td></tr>
            </table>
        ';
    }
}
