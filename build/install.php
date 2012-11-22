<?php

if (!isset($argv[1])) {
    die('No mode given');
}

if($argv[1] != "remove" && $argv[1] != "install") {
    die('Unknown mode "'.$argv[1].'". Use "install" or "remove" instead.');
}


$options = parse_ini_file(dirname(__FILE__).'/build.properties', false);

if (!file_exists($options['nightly.tempdir']."/scripts/configure")) {
    die('configure could not be found: ' . $options['nightly.tempdir']."/scripts/configure");
}

$required = array(
    'nightly.domain',
    'nightly.path',
    'nightly.title',
    'nightly.directory',
    'nightly.tempdir',
    'nightly.db.port',
    'nightly.db.host',
    'nightly.db.adress',
    'nightly.db.name',
    'nightly.db.user',
    'nightly.db.pass',
    'nightly.db.prefix',
    'nightly.user.name',
    'nightly.user.pass',
    'nightly.user.mail'
);

foreach ($required as $key) {
    if (!isset($options[$key])) {
        die('Missing configuration: ' . $key);
    }
}

chdir($options['nightly.tempdir']."/scripts/");

putenv("SSL_ENABLED=1");
putenv("SSL_ENABLED_YN=y");
putenv("BASE_URL_SCHEME=http");
putenv("BASE_URL_HOST=".$options['nightly.domain']);
putenv("BASE_URL_PORT=80");
putenv("BASE_URL_PATH=".$options['nightly.path']);
putenv("MOD_REWRITE=false");
putenv("INSTALL_PREFIX_WLS=");
putenv("DB_main_TYPE=mysql");
putenv("DB_main_NAME=".$options['nightly.db.name']);
putenv("DB_main_LOGIN=".$options['nightly.db.user']);
putenv("DB_main_PASSWORD=".$options['nightly.db.pass']);
putenv("DB_main_PREFIX=".$options['nightly.db.prefix']);
putenv("DB_main_HOST=".$options['nightly.db.host']);
putenv("DB_main_PORT=".$options['nightly.db.port']);
putenv("DB_main_ADDRESS=".$options['nightly.db.adress']);
putenv("WEB___DIR=".$options['nightly.directory']);
putenv("SETTINGS_admin_name=".$options['nightly.user.name']);
putenv("SETTINGS_admin_password=".$options['nightly.user.pass']);
putenv("SETTINGS_admin_email=".$options['nightly.user.mail']);
putenv("SETTINGS_locale=en_US");
putenv("SETTINGS_title=".$options['nightly.title']);

$result = array();

exec('php configure ' . escapeshellarg($argv[1]), $result);

foreach ($result AS $line) {
    echo $line . "\n";
}

?>