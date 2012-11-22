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
 * @subpackage core
 */

/**
 * This core class returns Service Instances.
 * You should NOT instantiate Services directly, but request them via
 * this class.
 * 
 * ATTENTION: This is a singleton, get an instance by calling:
 * <code>$services = ServiceFactory::get();</code>
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage core
 */
class ServiceFactory
{
    /**
     * Service instances will be cached inside for further usage.
     * @access private
     */
    private $services = null;
    /**
     * @access private
     */
    private $config = null;
    /**
     * @access private
     */
    private static $factory = null;
    
    /**
     * Singletons do not have a public constructor.
     * @access private
     */
    private function ServiceFactory() {
        $this->services = array();
        $this->config = parse_ini_file(_BIGACE_DIR_ROOT.'/system/config/services.ini', true); 
    }

    /**
     * Static getter, to receive an ServiceFactory instance.
     * @return ServiceFactory the initialized ServiceFactory instance to be used
     */
    static function get() {
        if(ServiceFactory::$factory == null) {
            ServiceFactory::$factory = new ServiceFactory();
        }
        return ServiceFactory::$factory;
    }

    /**
     * Gets the Authenticator instance to be used.
     * @return Authenticator the requested Authenticator instance
     */
    function getAuthenticator() {
        return $this->getService('authenticator');
    }

    /**
     * Gets the PrincipalService to be used.
     * @return PrincipalService the requested PrincipalService instance
     */
    function getPrincipalService() {
        return $this->getService('principal');
    }
    
    /**
     * Returns a configured Service. 
     * If the Service could not be found null will be returned.
     * @return mixed the service instance or null
     */
    function getService($name) 
    {
        if(!isset($this->services[$name]))
        {
            if(isset($this->config['services'][$name])) {
                import($this->config['services'][$name]);
                $classname = substr(strrchr($this->config['services'][$name], '.'),1);
                if(!isset($this->config['parameter'][$name])) {
                    $temp = new $classname();
                }
                else {
                    $params = explode (',', $this->config['parameter'][$name]);
                    if(count($params) == 1) {
                        $temp = new $classname($params[0]);
                    } else if(count($params) >= 2) {
                        $temp = new $classname($params[0],$params[1]);
                    }
                }
                if(isset($this->config[$name])) {
                    foreach($this->config[$name] AS $methodName => $value) {
                        $methodName = 'set' . $methodName;
                        $temp->$methodName($value);
                        //echo $name.'->'.$methodName.'('.$value.')';
                    }
                }
                $this->services[$name] = $temp;
            }
        }
        
        if(isset($this->services[$name]))        
            return $this->services[$name];

        return null;
    }

}
