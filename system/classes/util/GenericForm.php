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

define('GENERIC_PARAM_SECTION', 'section');
define('GENERIC_PARAM_NAME', 'name');
define('GENERIC_PARAM_VALUE', 'value');

/**
 * Class used for generating and handling GenericForm Data.
 * Including methods to fetch data, that was saved using the GenericForm Admin Framework.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util
 */
class GenericForm
{
    private $options = array(
                'table-section' => 'generic_sections',
                'table-mapping' => 'generic_mappings',
                'table-entries' => 'generic_entries',
                'perm-edit'     => 'system_admin', // if none is set, only Administrator are allowed
                'perm-create'   => 'system_admin', // if none is set, only Administrator are allowed
            );
    private $identifier = 'default';

    function GenericForm($type = null, $opts = array()) {
        if(!is_null($type))
            $this->setIdentifier($type);
        $this->setOptions($opts);
    }

    function setIdentifier($id = 'default') {
        $this->identifier = $id;
    }

    function getIdentifier() {
        return $this->identifier;
    }

    function setOptions($opts = array()) {
        foreach($this->options AS $key => $value) {
            if(isset($opts[$key]))
                $this->options[$key] = $opts[$key];
        }
    }

    // -----------------------------------------------------------------------------------
    function get_add_where_section() {
        return " AND type = '".$this->identifier."'";
    }

    function style_input_section($value = '') {
        return '<input type="text" name="'.GENERIC_PARAM_SECTION.'" value="'.$value.'">';
    }

    function style_input_entry($name = '', $value = '', $sectionId = null) {
        return '
            <table border="0">
            <tr><td style="background-color:transparent">'.getTranslation("generic_name").'</td>
            <td style="background-color:transparent"><input size="50" type="text" name="'.GENERIC_PARAM_NAME.'" value="'.$name.'"></td></tr>
            <tr><td style="background-color:transparent">'.getTranslation("generic_value").'</td>
            <td style="background-color:transparent"><input size="50" type="text" name="'.GENERIC_PARAM_VALUE.'" value="'.$value.'">
            <button type="submit" class="save links">'.getTranslation("save").'</button>
            </td></tr>
            </table>
        ';
    }

    function style_section_tools($section) {
        return '';
    }

    function has_section_tools() {
        return false;
    }
    // -----------------------------------------------------------------------------------

    function get_permission_edit() {
        return $this->options['perm-edit'];
    }

    function get_permission_create() {
        return $this->options['perm-create'];
    }

    function get_table_mappings() {
        return $this->options['table-mapping'];
    }

    function get_table_entries() {
        return $this->options['table-entries'];
    }

    function get_table_sections() {
        return $this->options['table-section'];
    }

    function get_generic_sections()
    {
        $sqlString = "SELECT sections.*, count(mappings.cid) as amount FROM {DB_PREFIX}".$this->get_table_sections()." sections
                        LEFT JOIN {DB_PREFIX}".$this->get_table_mappings()." mappings ON
                            sections.id = mappings.section_id AND sections.cid = mappings.cid
                        WHERE sections.cid = {CID} ".$this->get_add_where_section()." GROUP BY sections.id";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

        $results = array();
        for($i=0; $i < $res->count(); $i++) {
            $results[] = $res->next();
        }
        return $results;
    }

    function get_generic_section($id)
    {
        $sqlString = "SELECT * FROM {DB_PREFIX}".$this->get_table_sections()." sections WHERE
                            sections.cid = {CID} AND sections.id = " . $id;
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        if($res->count() > 0) {
            return $res->next();
        }
        return null;
    }

    function get_generic_entries_by_name($sectionName, $params = array())
    {
        $order = (isset($params['order']) && strtolower($params['order']) == 'desc' ? 'DESC' : 'ASC');
        $orderBy = (isset($params['orderBy']) && ($params['orderBy'] == 'timestamp' || $params['orderBy'] == 'name') ? $params['orderBy'] : 'id');
        $start = (isset($params['start']) ? $params['start'] : null);
        $end = (isset($params['end']) ? $params['end'] : null);

        $sqlString = "SELECT entries.* FROM {DB_PREFIX}".$this->get_table_entries()." entries
                        LEFT JOIN {DB_PREFIX}".$this->get_table_mappings()." mappings ON
                        entries.id = mappings.entry_id
                        LEFT JOIN {DB_PREFIX}".$this->get_table_sections()." sections ON
                        sections.id = mappings.section_id
                        AND entries.cid = mappings.cid
                        WHERE entries.cid = {CID}
                        AND sections.name = {SECTION}
                        ".$this->get_add_where_section()."
                        ORDER BY entries." . $orderBy . " " . $order;
        if(!is_null($start) && !is_null($end)) {
            $sqlString .= " LIMIT {START}, {END}";
        }
        $replacer = array(
            'SECTION'   => $sectionName,
            'START'     => intval($start),
            'END'       => intval($end),
            'ORDER'     => $order,
            'ORDER_BY'  => $orderBy
        );

        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $replacer, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

        $results = array();
        for($i=0; $i < $res->count(); $i++) {
            $results[] = $res->next();
        }
        return $results;
    }

    function count_generic_entries($sectionID)
    {
        $sqlString = "SELECT count(mappings.entry_id) as amount FROM {DB_PREFIX}".$this->get_table_mappings()." mappings
                        WHERE cid = {CID} AND section_id = {SECTION}";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array('SECTION' => $sectionID), true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        if(!$res->isError()) {
            $res = $res->next();
            return $res['amount'];
        }
        return false;
    }

    function get_generic_entry($name, $sectionId = null)
    {
        if ($sectionId === null) {
            $sql = "SELECT * FROM {DB_PREFIX}".$this->get_table_entries()." entries
                    WHERE entries.cid = {CID} AND entries.name = {NAME}";
        } else {
            $sql = "SELECT * FROM {DB_PREFIX}".$this->get_table_entries()." entries LEFT
                    JOIN {DB_PREFIX}".$this->get_table_mappings()." mappings ON
                    entries.id = mappings.entry_id AND entries.cid = mappings.cid WHERE
                    entries.cid = {CID} AND entries.name = {NAME} AND mappings.section_id = " . $sectionId;
        }
        $values = array('NAME' => $name, 'SECTION' => $sectionId);
        $sql    = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        $res    = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);

        if ($res->count() == 1) {
            return $res->next();
        }

        $results = array();
        for($i=0; $i < $res->count(); $i++) {
            $results[] = $res->next();
        }
        return $results;
    }

    function get_generic_entries($sectionID)
    {
        $sqlString = "SELECT * FROM {DB_PREFIX}".$this->get_table_entries()." entries LEFT JOIN {DB_PREFIX}".$this->get_table_mappings()."
                    mappings ON entries.id = mappings.entry_id AND entries.cid = mappings.cid
                        WHERE entries.cid = {CID} AND mappings.section_id = " . $sectionID;
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $results = array();
        for($i=0; $i < $res->count(); $i++) {
            $results[] = $res->next();
        }
        return $results;
    }

    function get_generic_entries_last($params)
    {
        $order = (isset($params['order']) && strtolower($params['order']) == 'asc') ? 'ASC' : 'DESC';
        $start = (isset($params['start']) ? $params['start'] : 0);
        $end = (isset($params['amount']) ? $params['amount'] : (isset($params['end']) ? $params['end'] : 5));

        $values = array( 'START' => intval($start), 'END' => intval($end), 'TYPE' => $this->getIdentifier() );
        $sqlString = "SELECT entries.* FROM {DB_PREFIX}".$this->get_table_entries()." entries LEFT JOIN {DB_PREFIX}".$this->get_table_mappings()."
                    mappings ON entries.id = mappings.entry_id AND entries.cid = mappings.cid
                    LEFT JOIN {DB_PREFIX}".$this->get_table_sections()." sections ON
                    mappings.section_id = sections.id
                        WHERE sections.type = {TYPE} AND entries.cid = {CID} ORDER BY entries.timestamp ".$order." LIMIT {START}, {END}";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);

        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

        $entries = array();
        for($i=0; $i < $res->count(); $i++)
            $entries[] = $res->next();
        return $entries;
    }


}
