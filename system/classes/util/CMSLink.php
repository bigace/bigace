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

/**
 * This class generates Links to Item in the CMS.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util
 */
class CMSLink
{
    private $name = null;
    private $cmd  = null;
    private $id = null;
    private $lang = null;
    private $template = null;
    private $templateKey = null;
    private $parameter = null;
    private $uniqueName = null;
    private $base = null;

    function setUniqueName($uniqueName) {
        $this->uniqueName = $uniqueName;
    }

    function getUniqueName() {
        return $this->uniqueName;
    }

    function setFileName($filename) {
        $this->name = $filename;
    }

    function getFileName() {
        return $this->name;
    }

    function setItemID($itemid) {
        $this->id = $itemid;
    }

    function getItemID() {
        return $this->id;
    }

    function setCommand($command) {
        $this->cmd = $command;
    }

    function getCommand() {
        return $this->cmd;
    }

    function setLanguageID($languageID) {
        $this->lang = $languageID;
    }

    function getLanguageID() {
        return $this->lang;
    }

    function setAction($action) {
        $this->template = $action;
    }

    function getAction() {
        return $this->template;
    }

    function setSubAction($part) {
        $this->templateKey = $part;
    }

    function getSubAction() {
        return $this->templateKey;
    }

    function getParameter() {
        return $this->parameter;
    }

    function getBaseURL() {
		if(is_null($this->base)) {
	        return $GLOBALS['_BIGACE']['DOMAIN'];
		}
		return $this->base;
    }

    function setUseSSL($ssl = true) {
    	if($ssl)
    		$this->base = BIGACE_URL_HTTPS;
    	else 
        	$this->base = BIGACE_URL_HTTP;
    }

    function addParameter($key, $value) {
        if($this->parameter == null)
            $this->parameter = array();
        $this->parameter[$key] = $value;
    }
}

?>