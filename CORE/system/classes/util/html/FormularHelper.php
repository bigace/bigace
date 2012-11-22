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
 * This file defines some methods to help building HTML Forms.
 * IT IS RECOMMENDED NOT TO USE THIS METHODS IN YOUR APPLICATION, 
 * IT IS HISTORICALLY HERE AND MIGHT CHANGE WITHOUT NOTIFICATION!
 *
 * It is used in the Administration, in some modules and Layouts.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 */

/**
 * Creates a two column table.
 * @param $config
 * @return unknown_type
 */
function createTable($config)
{
    $out = "\n";
    $config['align']['title']  = (isset($config['align']['title']))  ? $config['align']['title']  : 'center';
    $config['align']['left']   = (isset($config['align']['left']))   ? $config['align']['left']   : 'left';
    $config['align']['right']  = (isset($config['align']['right']))  ? $config['align']['right']  : 'left';
    $config['align']['bottom'] = (isset($config['align']['bottom'])) ? $config['align']['bottom'] : 'center';

    if (!isset($config['form_hide']))
    {
        $config['form_action'] = (isset($config['form_action'])) ? $config['form_action'] : createMenuLink($GLOBALS['_BIGACE']['PARSER']->getItemID());
        $config['form_method'] =  (isset($config['form_method'])) ? $config['form_method'] : 'post';

        $out .= '<form action="'.$config['form_action'].'" method="'.$config['form_method'].'"';

        $out .= (isset($config['form_enctype']))  ? ' ENCTYPE="'.$config['form_enctype'].'"'   : '';
        $out .= (isset($config['form_reset']))    ? ' onreset="'.$config['form_reset'].'"'     : '';
        $out .= (isset($config['form_onsubmit'])) ? ' onsubmit="'.$config['form_onsubmit'].'"' : '';
        $out .= (isset($config['form_target']))   ? ' target="'.$config['form_target'].'"'     : '';
        $out .= (isset($config['form_name']))     ? ' name="'.$config['form_name'].'" id="'.$config['form_name'].'"'            : '';

        $out .= '>';

        if(isset($config['form_hidden']) && is_array($config['form_hidden'])) {
            foreach($config['form_hidden'] AS $k => $v) {
                $out .= "\n".'<input type="hidden" name="'.$k.'" value="'.$v.'">';
            }
        }
    }

    $out .= '<table class="tablesorter" cellspacing="0">
    <colgroup>
        <col';
    if (isset($config['size']['left']))
        $out .= ' width="'.$config['size']['left'].'"';
    $out .= ' />
        <col';
    if (isset($config['size']['right']))
        $out .= ' width="'.$config['size']['right'].'"';
    $out .= ' />
    </colgroup>
    <thead>
      <tr>
        <th colspan="2">';
    if(isset($config['image'])) {
        $config['image'] = (strpos ($config['image'],_BIGACE_DIR_PUBLIC_WEB) === false) ? _BIGACE_DIR_PUBLIC_WEB.'system/images/' . $config['image'] : $config['image'];
        $out .= '<img src="'.$config['image'].'" vspace="0" hspace="0" align="absbottom"> ';
    }

    $out .= $config['title']."\n</th></tr>
    </thead>
    <tbody>
    ";

    foreach($config['entries'] AS $k => $v)
    {
        if ($k != 'empty')
        {
            if ($v != 'empty')
            {
                $out .= '<tr><td';
                $out .= (isset($config['valign']['left'])) ? ' valign="'.$config['valign']['left'].'" ' : '';
                $out .= ' align="'.$config['align']['left'].'">'.$k.'</td>';
                $out .= '<td';
                $out .= (isset($config['valign']['right'])) ? ' valign="'.$config['valign']['right'].'" ' : '';
                $out .= ' align="'.$config['align']['right'].'">'.$v.'</td></tr>';
            }
            else
            {
                $out .= '<tr><td colspan="2"';
                $out .= (isset($config['valign']['left'])) ? ' valign="'.$config['valign']['left'].'" ' : '';
                $out .= ' align="'.$config['align']['left'].'">'.$k.'</td></tr>';
            }
        }
        else {
            $out .= '<tr><td colspan="2" align="'.$config['align']['left'].'">&nbsp;</td></tr>';
        }
    }

    $out .= '<tr><td colspan="2" align="'.$config['align']['bottom'].'">';

    if (!isset($config['form_hide']))
    {
        if($config['form_submit'] == true) 
        {
            $out .= '<button class="ok" type="submit" value="'.$config['submit_label'].'"';
            $out .= (isset($config['submit_disable'])) ? ' disabled' : '';
            $out .= (isset($config['submit_name'])) ? ' name="'.$config['submit_name'].'" id="'.$config['submit_name'].'"' : '';
           $out .= '>'.$config['submit_label'] . '</button>';
        } 
        else {
            $out .= '&nbsp;';
        }
    } else {
        $out .= (isset($config['submit_label'])) ? $config['submit_label'] : '&nbsp;';
    }

    $out .= (isset($config['form_reset'])) ? '&nbsp;<button class="reset" type="reset">'.$config['reset_label'].'</button>' : '';

    $out .= '</td></tr>
    </tbody>
    </table>';

    $out .= (isset($config['form_hide'])) ? '' : '</form>';

    return $out;
}

/**
 * Creates an HTML Input type "text".
 */
function createNamedTextInputType($name, $value, $max, $disabled = false, $size = '35', $id = '')
{
    if ($id == '') $id = $name;
    $html = '<input type="text" name="'.$name.'" id="'.$id.'" maxlength="'.$max.'" size="'.$size.'" value="'.addslashes($value).'"';
    if ($disabled) {
        $html .= ' readonly ';
    }
    $html .= '>';
    return $html;
}

function createTextInputType($name, $value, $max, $disabled = false, $size = '35', $id = '')
{
    return createNamedTextInputType('data['.$name.']', $value, $max, $disabled, $size, $id);
}

function createPasswordField($name, $value, $max)
{
    return createNamedPasswordField('data['.$name.']', $value, $max);
}

function createNamedPasswordField($name, $value, $max)
{
    return '<input type="password" name="'.$name.'" id="'.$name.'" maxlength="'.$max.'" size="35" value="'.$value.'">';
}

function createNamedRadioButton($name, $value, $selected, $id = '')
{
    $html  = '<input type="radio" name="'.$name.'" value="'.$value.'"';
    if ($id != '')
        $html .= ' id="'.$id.'"';
    if ($selected) {
        $html .= ' checked ';
    }
    $html .= '>';
    return $html;
}

function createRadioButton($name, $value, $selected, $id = '')
{
    return createNamedRadioButton('data['.$name.']', $value, $selected, $id);
}

/**
 * Creates a Checkbox with the given Name.
 * @param the Name of the HTML Checkbox
 * @param the Value of the HTML Checkbox
 * @param if set and TRUE the Checkbox will be checked
 * @param if set and TRUE the Checkbox will be disabled
 */
function createNamedCheckBox($name, $value, $checked = FALSE, $disabled = FALSE, $id = "") {
    $html  = '<input type="checkbox" name="'.$name.'" ';
    $html .= ' value="'.$value.'"';
    if($id != '')
        $html .= ' id="'.$id.'"';
    if ($checked) { $html .= ' checked '; }
    if ($disabled) { $html .= ' disabled '; }
    $html .= '>';
    return $html;
}

function createCheckBox($name, $value, $checked, $disabled = FALSE, $id = "")
{
    return createNamedCheckBox('data['.$name.']', $value, $checked, $disabled, $id);
}

function createTextArea($name, $value, $rows = '10', $cols = '50', $wrap = '')
{
    $html  = '<textarea name="data['.$name.']" id="'.$name.'" rows="'.$rows.'" cols="'.$cols.'" wrap="'.$wrap.'">';
    $html .= $value;
    $html .= '</textarea>';
    return $html;
}

/**
 * Creates an File Input Type with the given Name.
 * The HTML Elements ID Attribute will be the same as the NAME Attribute.
 * @param the Name of the HTML Element
 */
function createFileInput($name) {
    return '<input type="file" name="'.$name.'" id="'.$name.'">';
}

/**
 * Pass the Values in an Array:
 * <code>array('foo' => 'bar') will lead to &lt;option value="foo"&gt;bar&lt;/option&gt;</code>
 * @param String the Name Attribute (name="")
 * @param Array the Array with the Values
 * @param String the preselected Value (leave empty for none)
 * @param String the Javascript to be executed "onChange" (leave empty for none)
 * @param boolean whether the Select Box should be disabled or not (default is false)
 * @param String the ID Attribute (id="")
 */
function createNamedSelectBox($name, $opt_name_val, $sel = NULL, $onChange = '', $disabled = false, $id = '')
{
    $select = '<select name="'.$name.'"';
    if ($id != '') {
        $select .= ' id="'.$id.'"';
    }
    if ($onChange != '') {
        $select .= ' onChange="'.$onChange.'"';
    }
    if ($disabled) {
        $select .= ' disabled';
    }
    $select .= '>';
    $select .= createOptionTags($opt_name_val, ($sel == '' ? NULL : $sel));
    return $select . '</select>';
}

/**
 * Creates a HTML select box with the name used in an Array called <code>data</code>.
 * For example, you pass <code>foo</code>, it will wrap the name as <code>&lt;select name="data[foo]"&gt;</code>.
 * @param String the Name Attribute (name="")
 * @param Array the Array with the Values
 * @param String the preselected Value (leave empty for none)
 * @param String the Javascript to be executed "onChange" (leave empty for none)
 * @param boolean whether the Select Box should be disabled or not (default is false)
 * @param String the ID Attribute (id="")
 * @see createNamedSelectBox
 */
function createSelectBox($name, $opt_name_val, $sel = '', $onChange = '', $disabled = false, $id = '') {
    return createNamedSelectBox('data['.$name.']', $opt_name_val, $sel, $onChange, $disabled, ($id == '') ? $name : $id);
}

/**
 * Creates a String, holding the HTML representation of the Key-Value-pair
 * Array as OPTION Tags for a Select Box.
 */
function createOptionTags($keyValueArray = array(), $preselected = NULL)
{
    $select = '';
    foreach ($keyValueArray AS $key => $val) {
        $select .= '<option value="'.$val.'"';
        if ($preselected != NULL && $preselected == $val) {
            $select .= ' selected';
        }
        $select .= '>'.$key.'</option>'. "\n";
    }
    return $select;
}

