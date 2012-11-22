<?php

include(dirname(__FILE__).'/config_phplist.php');
include(dirname(__FILE__).'/../../../system/config/config.system.php');

// fix me with bigace session ???
$language_module = "english.inc";

$database_host = $_BIGACE['db']['host'];
$database_name = $_BIGACE['db']['name'];
$database_user = $_BIGACE['db']['user'];
$database_password = $_BIGACE['db']['pass'];

# if you use multiple installations of PHPlist you can set this to
# something to identify this one. it will be prepended to email report
# subjects
$installation_name = 'PHPlist';

# if you want a prefix to all your tables, specify it here
$table_prefix = "phplist_";

# if you want to use a different prefix to user tables, specify it here.
# read README.usertables for more information
$usertable_prefix = "phplist_user_";

# if you change the path to the PHPlist system, make the change here as well
# path should be relative to the root directory of your webserver (document root)
# you cannot actually change the "admin", but you can change the "lists"
$pageroot = '/'.BIGACE_DIR_PATH.'/addon/phplist';
$adminpages = $pageroot.'/admin';
