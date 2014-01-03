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

import('classes.util.ImageLink');

/**
 * This class generates a URL to a Image thumbnail.
 * 
 * By default, cropping is used for, so if you do not want this feature, call:
 * <code>
 * $thumbnailLink->setCropping(false);
 * </code>
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.links
 */
class ThumbnailLink extends ImageLink
{

    function ThumbnailLink() {
    	parent::ImageLink();
    }
    
    function setWidth($pixel) {
        $this->addParameter('w', $pixel);
    }

    function setHeight($pixel) {
        $this->addParameter('h', $pixel);
    }

    function setCropping($crop = true) {
        $this->addParameter('crop', intval((bool)$crop));
    }
}

?>