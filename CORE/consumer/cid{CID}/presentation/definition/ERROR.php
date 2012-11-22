<?php
/**
 * Definiton of default errors. 
 * $Id$ 
 */
$DEFINITION['ERROR'] = array (

    'NAME'          => 'ERROR',
    'TITLE'         => 'Error Templates',
    'STANDARD'      => 'error/error_404.php',
    'DESCRIPTION'   => 'Defines keys for all error messages.',
    'KEYS'          => array (
                            'error-login'       => '../../../../system/application/auth/login.php',
                            'error-anonymous'   => 'error/error_anonymous.php',
                            'error-deactivated' => 'error/error_deactivated.php',
                            'error-401'     	=> 'error/error_401.php',
                            'error-403'     	=> 'error/error_403.php',
                            'error-404'     	=> 'error/error_404.php'
                       ),
    'PUBLIC'        => false

);
