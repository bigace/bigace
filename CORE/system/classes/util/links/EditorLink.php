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
 * @subpackage util.links
 */

import('classes.util.CMSLink');

/**
 * This class return the URL for an Editor.
 * If not Editor is set, the configured default Editor will be taken.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.links
 */
class EditorLink extends CMSLink
{

    function EditorLink($id = null, $language = null) {
    	if($id != null) $this->setItemID($id);
    	if($language != null) $this->setLanguageID($language);
    		
        $this->setAction(ConfigurationReader::getConfigurationValue('editor', 'default.editor', 'plaintext'));
        $this->setCommand(_BIGACE_CMD_EDITOR);
        $this->setFilename('editor.php');
		$this->setUseSSL(BIGACE_USE_SSL);
    }
    
    /**
     * Sets the editor to use.
     * Default is configurable.
     */
    function setEditor($editorName) {
        $this->setAction($editorName);
    }
}
