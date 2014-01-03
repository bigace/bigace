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
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.administration
 * @subpackage system
 */

check_admin_login();
admin_header();

// defines if the configuration checks are skipped or performed
define('SKIP_CONFIGURATION_CHECKS', false);

// configuration package where all test definitions are defined
define('CONFIG_PACKAGE_HINTS', 'hints');

// name of the template blocks, use these keys in the test definitions
define('BLOCK_NAME_ERROR', 'hint');
define('BLOCK_NAME_HINT', 'hint');
define('BLOCK_NAME_INFO', 'message');

// array keys for the test definitions
define('DEFINITION_FRIGHT_NAME',    'frightName');
define('DEFINITION_FRIGHT_DEFAULT', 'frightDefault');
define('DEFINITION_TEST_RESULT',    'testResult');
define('DEFINITION_BLOCK_TRUE',     'blockTrue');
define('DEFINITION_BLOCK_FALSE',    'blockFalse');
define('DEFINITION_MESSAGE_TRUE',   'messageTrue');
define('DEFINITION_MESSAGE_FALSE',  'messageFalse');

class SystemCheck
{
    function SystemCheck() {
    }
    
    function getResult() {
        return false;
    }
    
    function getMessage() {
        return '';
    }
    
    function getResultType() {
    }
    
    function getFunctionalRightString() {
        return '';
    }
    
    function getDefaultAccess() {
        return true;
    }
}

class ArrayCheck extends SystemCheck
{
    var $definition;
    var $name;
    
    function ArrayCheck($name,$def) {
        $this->name = $name;
        if (isset($def[DEFINITION_FRIGHT_NAME]) && isset($def[DEFINITION_FRIGHT_DEFAULT]) && isset($def[DEFINITION_TEST_RESULT])
        && isset($def[DEFINITION_BLOCK_TRUE]) && isset($def[DEFINITION_MESSAGE_TRUE]) && isset($def[DEFINITION_BLOCK_FALSE]) && isset($def[DEFINITION_MESSAGE_FALSE]))
        {
            $this->definition = $def;
        }
    }
    
    function getName() {
        return $this->name;
    }

    function getResult() {
        return (bool)$this->definition[DEFINITION_TEST_RESULT];
    }
    
    function getMessage() {
        if($this->definition == null)
            return "The Test '".$this->getName()."' is not properly configured!";
            
        if((bool)$this->definition[DEFINITION_TEST_RESULT])
            return $this->definition[DEFINITION_MESSAGE_TRUE];
        return $this->definition[DEFINITION_MESSAGE_FALSE];
    }
    
    function getResultType() {
        if((bool)$this->definition[DEFINITION_TEST_RESULT])
            return $this->definition[DEFINITION_BLOCK_TRUE];
        return $this->definition[DEFINITION_BLOCK_FALSE];
    }
    
    function getFunctionalRightString() {
        return $this->definition[DEFINITION_FRIGHT_NAME];
    }
    
    function getDefaultAccess() {
        return $this->definition[DEFINITION_FRIGHT_DEFAULT];
    }
    
}

// the key in the template that will display the message
define('TEMPLATE_MESSAGE', 'MESSAGE');

import('classes.configuration.ConfigurationReader');
import('classes.consumer.ConsumerService');

// preload all config entrys for the 'hints' package
ConfigurationReader::getPackage( CONFIG_PACKAGE_HINTS );
$consumerService = new ConsumerService();

$tests = array(
                new ArrayCheck('CheckForInstallationDirectory', array(
                    DEFINITION_FRIGHT_NAME      => 'installation.directory',
                    DEFINITION_FRIGHT_DEFAULT   => (int)true,
                    DEFINITION_TEST_RESULT      => (int)file_exists(_BIGACE_DIR_ROOT . '/misc/install/'),
                    DEFINITION_BLOCK_TRUE       => BLOCK_NAME_HINT,
                    DEFINITION_MESSAGE_TRUE     => getTranslation('hint_installation_dir') . ': ' . _BIGACE_DIR_ROOT . '/misc/install/',
                    DEFINITION_BLOCK_FALSE      => BLOCK_NAME_INFO,
                    DEFINITION_MESSAGE_FALSE    => getTranslation('info_installation_dir')
                )),
                new ArrayCheck('CheckForMiscHtaccess', array(
                    DEFINITION_FRIGHT_NAME      => 'misc.htaccess',
                    DEFINITION_FRIGHT_DEFAULT   => (int)true,
                    DEFINITION_TEST_RESULT      => (int)(!file_exists(_BIGACE_DIR_ROOT . '/misc/.htaccess')),
                    DEFINITION_BLOCK_TRUE       => BLOCK_NAME_HINT,
                    DEFINITION_MESSAGE_TRUE     => sprintf(getTranslation('hint_misc_htaccess'), _BIGACE_DIR_ROOT . '/misc/_.htaccess', _BIGACE_DIR_ROOT . '/misc/.htaccess'),
                    DEFINITION_BLOCK_FALSE      => BLOCK_NAME_INFO,
                    DEFINITION_MESSAGE_FALSE    => getTranslation('info_misc_htaccess')
                )),
                new ArrayCheck('CheckForDefaultCommunity', array(
                    DEFINITION_FRIGHT_NAME      => 'check.default.community',
                    DEFINITION_FRIGHT_DEFAULT   => (int)true,
                    DEFINITION_TEST_RESULT      => (int)($consumerService->getConsumerIdForDomain(DEFAULT_COMMUNITY) < 0),
                    DEFINITION_BLOCK_TRUE       => BLOCK_NAME_INFO,
                    DEFINITION_MESSAGE_TRUE     => getTranslation('hint_default_consumer'),
                    DEFINITION_BLOCK_FALSE      => BLOCK_NAME_INFO,
                    DEFINITION_MESSAGE_FALSE    => ''
                )),
                new ArrayCheck('LinkToSecurityInfos', array(
                    DEFINITION_FRIGHT_NAME      => 'link.security.docu',
                    DEFINITION_FRIGHT_DEFAULT   => (int)true,
                    DEFINITION_TEST_RESULT      => (int)true,
                    DEFINITION_BLOCK_TRUE       => BLOCK_NAME_INFO,
                    DEFINITION_MESSAGE_TRUE     => getTranslation('link_security_docu'),
                    DEFINITION_BLOCK_FALSE      => BLOCK_NAME_INFO,
                    DEFINITION_MESSAGE_FALSE    => getTranslation('link_security_docu')
                )),
        );


$tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("AdminHints.tpl.htm", false, true);

foreach($tests AS $systemCheck)
{
    $uniqueTestName = $systemCheck->getName();

    if (ConfigurationReader::getConfigurationValue(CONFIG_PACKAGE_HINTS, $systemCheck->getFunctionalRightString(), $systemCheck->getDefaultAccess()))
    {
        if ($systemCheck->getMessage() != null && $systemCheck->getMessage() != '')
        {
            $tpl->setCurrentBlock($systemCheck->getResultType());
            $tpl->setVariable(TEMPLATE_MESSAGE, $systemCheck->getMessage());
            $tpl->parseCurrentBlock();
        }
    }
}


$tpl->show();

admin_footer();

?>
