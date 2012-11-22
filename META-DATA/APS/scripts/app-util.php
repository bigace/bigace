<?php

ini_set('include_path', '.');

require_once('env-parser.php');
require_once('file-util.php');
require_once('db-util.php');

// required for database structure parsing
require_once('XmlToSqlParser.php');
require_once('adodb/adodb.inc.php');
require_once('adodb/adodb-xmlschema03.inc.php');

function configure($config_files, $xml_schemas, $schema_files, $db_ids, $psa_modify_hash, $db_modify_hash, $settings_modify_hash, $crypt_settings_modify_hash, $settings_enum_modify_hash, $additional_modify_hash)
{
    // -- Creating and fixing DB structure and community data --
    install_database_structure($db_ids, $xml_schemas, $psa_modify_hash, $db_modify_hash, $settings_modify_hash, $crypt_settings_modify_hash, $settings_enum_modify_hash, $additional_modify_hash);

    // -- Import additional SQL files to DB --
    import_sql_scripts_to_databases($schema_files, $db_ids, $psa_modify_hash, $db_modify_hash, $settings_modify_hash, $crypt_settings_modify_hash, $settings_enum_modify_hash, $additional_modify_hash);

    // -- Writing config file --
    write_config_files($config_files, $psa_modify_hash, $db_modify_hash, $settings_modify_hash, $crypt_settings_modify_hash, $settings_enum_modify_hash, $additional_modify_hash);
}

// removes all known bigaces table from the database
function remove_app($schema_files, $db_ids, $psa_modify_hash, $db_modify_hash, $settings_modify_hash, $crypt_settings_modify_hash, $settings_enum_modify_hash, $additional_modify_hash)
{
    foreach($db_ids as $db_id)
    {
        // FIXME: support more database types?
        if(get_db_type($db_id) != "mysql")
        {
            print "Database type " . get_db_type($db_id) . " is not supported.\n";
            exit(1);
        }
        foreach($schema_files as $schema_filename => $schema_db_id){
            if($schema_db_id == $db_id){
                mysql_db_connect(get_db_address($db_id),
                                 get_db_login($db_id),
                                 get_db_password($db_id),
                                 get_db_name($db_id));

                $tables = get_all_table_names($schema_filename);
                $sql = "";
                foreach($tables AS $name) {
                    $sql .= "DROP TABLE IF EXISTS `{CID_DB_PREFIX}".$name."`;\n";
                }

                $sql = modify_sql($sql, array_merge($psa_modify_hash,
                                            $db_modify_hash,
                                            $settings_modify_hash,
                                            $settings_enum_modify_hash,
					                        $crypt_settings_modify_hash,
					                        $additional_modify_hash));

                populate_mysql_db($sql);
            }
        }
    }
}

// required for upgrading
function import_sql_scripts_to_databases($schema_files, $db_ids, $psa_modify_hash, $db_modify_hash, $settings_modify_hash, $crypt_settings_modify_hash, $settings_enum_modify_hash, $additional_modify_hash)
{
    foreach($db_ids as $db_id)
    {
        // FIXME: support more database types?
        if(get_db_type($db_id) != "mysql")
        {
            print "Database type " . get_db_type($db_id) . " is not supported.\n";
            exit(1);
        }
        foreach($schema_files as $schema_filename => $schema_db_id){
            if($schema_db_id == $db_id){
                mysql_db_connect(get_db_address($db_id),
                                 get_db_login($db_id),
                                 get_db_password($db_id),
                                 get_db_name($db_id));

                $sql = modify_content($schema_filename,
                                      array_merge($psa_modify_hash,
                                            $db_modify_hash,
                                            $settings_modify_hash,
                                            $settings_enum_modify_hash,
					    $crypt_settings_modify_hash,
					    $additional_modify_hash));

                populate_mysql_db($sql);
            }
        }
    }
}

// writes parsed config files
function write_config_files($config_files, $psa_modify_hash, $db_modify_hash, $settings_modify_hash, $crypt_settings_modify_hash, $settings_enum_modify_hash, $additional_modify_hash)
{
    foreach($config_files as $web_id => $arr2){
        foreach($arr2 as $arr) {
            $template_file = $arr[0];
            $dest_path = get_web_dir($web_id).'/'.$arr[1];
            modify_file($template_file,
                    $dest_path,
                    array_merge($psa_modify_hash,
                          $db_modify_hash,
                          $settings_modify_hash,
                          $settings_enum_modify_hash,
			  $crypt_settings_modify_hash,
			  $additional_modify_hash));
	    }
    }
}

// executed during installation and upgrade
function install_database_structure($db_ids, $xml_schemas, $psa_modify_hash, $db_modify_hash, $settings_modify_hash, $crypt_settings_modify_hash, $settings_enum_modify_hash, $additional_modify_hash)
{
    foreach($db_ids as $db_id)
    {
        // FIXME: support more database types?
        if(get_db_type($db_id) != "mysql")
        {
            print "Database type " . get_db_type($db_id) . " is not supported.\n";
            exit(1);
        }

        if (!file_exists("community.xml"))
        {
            print "ERROR: Missing database file: community.xml\n";
            exit(1);
        }

        if (!file_exists("structure.xml"))
        {
            print "ERROR: Missing database file: structure.xml\n";
            exit(1);
        }
        
        $db_type = get_db_type($db_id);
        $db_adress = get_db_address($db_id);
        $db_name = get_db_name($db_id);
        $db_user = get_db_login($db_id);
        $db_pass = get_db_password($db_id);
        $db_prefix = get_db_prefix($db_id);

        // ---------------------------------------------------------------------
        // create database tables from schema file structure.xml
        $db = ADONewConnection( $db_type );
        @$db->Connect( $db_adress, $db_user, $db_pass );

        if(!$db->IsConnected())
        {
            print "ERROR: Could not connect to database.\n";
            exit(1);
        }

        // TODO: now or after creating structure or both ??
		$db->Execute("SET NAMES utf8");
		$db->Execute("SET CHARACTER SET utf8");

        $dict = NewDataDictionary($db);
        $ttt = $dict->CreateDatabase($db_name, 
		    array("mysql" => "DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci"));
        $resCreateDB = $dict->ExecuteSQLArray($ttt);

        // FIXME change for use with other database types, 1007 is mysql specific
        if ($resCreateDB != 2 && $db->ErrorNo() != 1007) {
            $errors[] = getTranslation('error_db_create');
        }

        @$db->Connect( $db_adress, $db_user, $db_pass, $db_name );
	
        if (!$db->IsConnected())
        {
            print "ERROR: Could not select database.\n";
            exit(1);
        }

        // prepare the Database Installation Files
        $schema = new adoSchema( $db );
        $schema->SetPrefix($db_prefix, FALSE);
        $sql = @$schema->ParseSchema( "structure.xml" );

        if($sql === FALSE)
        {
            print "ERROR: Could not parse structure.xml.\n";
            exit(1);
        }
        
        $result = $schema->ExecuteSchema();

        if($result != 2) 
        {
            print "ERROR: Could not create database structure.\n";
            exit(1);
        }
        // ---------------------------------------------------------------------

        // ---------------------------------------------------------------------
        // import community data

        // TODO: now or before creating structure or both ??
		//$db->Execute("SET NAMES utf8");
		//$db->Execute("SET CHARACTER SET utf8");
		
        foreach($xml_schemas as $xf)
        {
          	// parse XML File to Community Directory
            $xmlContent = read_file($xf);
            $xmlContent = modify_sql($xmlContent, array_merge($psa_modify_hash,
                                        $db_modify_hash,
                                        $settings_modify_hash,
                                        $settings_enum_modify_hash,
				                        $crypt_settings_modify_hash,
				                        $additional_modify_hash));

            $myParser = new XmlToSqlParser();
            $myParser->setAdoDBConnection($db);
            $myParser->setIgnoreVersionConflict(true);
            $myParser->setTablePrefix($db_prefix);
            $myParser->setReplacer(array('{CID}' => "1"));
            $myParser->setMode(XML_SQL_MODE_INSTALL);
            $myParser->parseStructure($xmlContent);

            $errors = $myParser->getError();
            
            if(count($errors) >0)
            {
                print "ERROR: Error parsing community data:\n";
                foreach($errors AS $error) {
                    print $error . "\n";
                }
                exit(1);
            }
            
            $sqls = $myParser->getSqlArray();
            foreach($sqls AS $statement) 
            {
                $recordSet = &$db->Execute($statement);
                if($recordSet === FALSE) {
                    print "ERROR: SQL statement failed: ".$statement."\n";
                }         
            }
        }
        // ---------------------------------------------------------------------
    }
}

?>
