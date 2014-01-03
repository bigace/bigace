<?php
/**
 * See config.default.php for all available settings.
 */ 

// ------------------------------ [CORE SETTINGS] ----------------------------
define ('BIGACE_TIMEZONE', 'Europe/Berlin');	 // Timezone of your server, see http://de3.php.net/timezones
define ('BIGACE_URL_REWRITE', '{MOD_REWRITE}');  // De-/activates rewriten/friendly URLs
define ('BIGACE_DIR_PATH', '{BASE_DIR}');        // Directory of the BIGACE installation, relative from DocumentRoot. 

// ------------------------------ [DATABASE CONNECTION] ----------------------------
$_BIGACE['db']['type']                = '{CID_DB_TYPE}';	// only 'mysql' is supported currently
$_BIGACE['db']['character-set']       = 'utf8';				// if you really need to change, bestter ask in our forum before
$_BIGACE['db']['host']                = '{CID_DB_HOST}';	// often 'localhost' is a good idead
$_BIGACE['db']['name']                = '{CID_DB_NAME}';	// the db name 
$_BIGACE['db']['user']                = '{CID_DB_USER}';	// db user name
$_BIGACE['db']['pass']                = '{CID_DB_PASS}';	// password for the above user
$_BIGACE['db']['prefix']              = '{CID_DB_PREFIX}';	// prefix for the table names (can be empty!)

?>