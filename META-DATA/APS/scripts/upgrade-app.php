<?php
require_once('app-util.php');
require_once('file-util.php');

function upgrade_app($from_ver, $from_rel, $config_files, $schema_files, $db_ids, $psa_modify_hash, $db_modify_hash, $settings_modify_hash, $crypt_settings_modify_hash, $settings_enum_modify_hash, $additional_modify_hash){
//    $upgrade_schema_files = get_upgrade_schema_files($argv[2], $argv[3]);
    $upgrade_schema_files = array(); // array('upgrade-1.0-1.sql' => 'main')
    configure($config_files, $upgrade_schema_files, $db_ids, $psa_modify_hash, $db_modify_hash, $settings_modify_hash, $crypt_settings_modify_hash, $settings_enum_modify_hash, $additional_modify_hash);
    return 0;
}
?>
