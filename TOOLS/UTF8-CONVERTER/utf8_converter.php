<?php
 
// this script will output the queries need to change all fields/tables to a different collation
// it is HIGHLY suggested you take a MySQL dump prior to running any of the generated
// this code is provided as is and without any warranty
 
die("Make a backup of your MySQL database then remove this line");
 
set_time_limit(0);
 
// collation you want to change:
$convert_from = 'latin1_swedish_ci';
 
// collation you want to change it to:
$convert_to   = 'utf8_general_ci';
 
// character set of new collation:
$character_set= 'utf8';
 
$show_alter_table = true;
$show_alter_field = true;
 
// DB login information
$username = 'user';
$password = 'pass';
$database = 'table';
$host     = 'localhost';
 
mysql_connect($host, $username, $password);
mysql_select_db($database);
 
$rs_tables = mysql_query(" SHOW TABLES ") or die(mysql_error());
 
print '<pre>';
while ($row_tables = mysql_fetch_row($rs_tables)) {
    $table = mysql_real_escape_string($row_tables[0]);
    
    // Alter table collation
    // ALTER TABLE `account` DEFAULT CHARACTER SET utf8
    if ($show_alter_table) {
        echo("ALTER TABLE `$table` DEFAULT CHARACTER SET $character_set;\r\n");
    }
 
    $rs = mysql_query(" SHOW FULL FIELDS FROM `$table` ") or die(mysql_error());
    while ($row=mysql_fetch_assoc($rs)) {
        
        if ($row['Collation']!=$convert_from)
            continue;
 
        // Is the field allowed to be null?
        if ($row['Null']=='YES') {
            $nullable = ' NULL ';
        } else {
            $nullable = ' NOT NULL';
        }
 
        // Does the field default to null, a string, or nothing?
        if ($row['Default']==NULL) {
            $default = " DEFAULT NULL";
        } else if ($row['Default']!='') {
            $default = " DEFAULT '".mysql_real_escape_string($row['Default'])."'";
        } else {
            $default = '';
        }
 
        // Alter field collation:
        // ALTER TABLE `account` CHANGE `email` `email` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
        if ($show_alter_field) {
            $field = mysql_real_escape_string($row['Field']);
            echo "ALTER TABLE `$table` CHANGE `$field` `$field` $row[Type] CHARACTER SET $character_set COLLATE $convert_to $nullable $default; \r\n";
        }
    }
}
 
?>
