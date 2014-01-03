<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.<br>Copyright (C) Kevin Papst.
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
 * @subpackage portlets
 */

import('api.portlet.Portlet');
import('classes.util.Translations');

/**
 * This is a base class for Portlets that use translations.
 * For each parameter, you have to give a key.
 *
 * For example, you have the PortletParameter "foo", you should have
 * a tranlation entry called "param_name_foo".
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.api
 * @subpackage portlets
 */
class TranslatedPortlet extends Portlet
{
    private $bundle = null;

    /**
     * Loads the given translation file for this Portlet.
     */
    function loadBundle($name)
    {
        if($this->bundle === null) {
            $this->bundle = Translations::get($name, _ULC_, $this->getIdentifier());
        } else {
            $this->bundle->load($name);
        }
    }

    /**
     * Gets the Translation fo the given key.
     * If this is not found or the Bundle is null, it returns fallback.
     * @return String the Translation or fallback
     */
    function getTranslation($key, $fallback = null) {
        if ($this->bundle !== null) {
            return $this->bundle->getString($key, $fallback);
        }
        return $fallback;
    }

    /**
     * Returns the translation for a parameter by searching for a key with the name:
     * 'param_name_'.$key.
     *
     * @see CORE/system/api/portlet/Portlet#getParameterName($key)
     */
    function getParameterName($key) {
        return $this->getTranslation('param_name_'.$key, $key);
    }

}