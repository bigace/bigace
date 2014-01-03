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
 * @subpackage file
 */

import('classes.item.ItemAdminService');
import('classes.util.IOHelper');
 
/**
 * The FileAdminService provides all kind of writing services for Files.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage file
 */
class FileAdminService extends ItemAdminService
{

    function FileAdminService()
    {
        $this->initItemAdminService( _BIGACE_ITEM_FILE );
    }

    /**
     * overwritten to add proper file extension
     * @access protected
     */
    function buildUniqueName($name, $extension, $delim = '_') {
    	if(ConfigurationReader::getConfigurationValue('seo', 'file.use.extension', true))
        	return parent::buildUniqueName($name, '.'.$extension);
        else
    	    return parent::buildUniqueName($name, '');
    }

    function createItemLanguageVersion($id, $copyLangID, $data)
    {
        $requestResult = $this->createLanguageVersion($id, $copyLangID, $data);
        
        if ($requestResult->isSuccessful()) {
            $filename = $requestResult->getMessage();
            
            $itemOrig = $this->getClass($id, ITEM_LOAD_FULL, $copyLangID);
            $itemNew = $this->getClass($id, ITEM_LOAD_FULL, $data['langid']);

            copy($itemOrig->getFullURL(), $itemNew->getFullURL());
        }
        
        return $requestResult;
    }

}
