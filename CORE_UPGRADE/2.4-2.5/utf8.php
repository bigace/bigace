<?php
 
require_once(dirname(__FILE__) . '/system/config/config.system.php');
require_once(dirname(__FILE__) . '/system/config/config.default.php');

if(!isset($_BIGACE['db']['name']))
	die('Did you upgrade your config file /system/config/config.system.php ???');
	
mysql_connect($_BIGACE['db']['host'], $_BIGACE['db']['user'], $_BIGACE['db']['pass']);
mysql_select_db($_BIGACE['db']['name']);

if(!isset($_POST['convert_from'])) 
{
	print "<h1>DID YOU CREATE A BACKUP ?!? If not, do it first!</h1>";
	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print "Select your database collation: ";
	print '<select name="convert_from">';
 
	$charsets = mysql_query(" SHOW COLLATION ") or die(mysql_error());
	while ($char_row = mysql_fetch_row($charsets)) {
		print '<option value="'.$char_row[0].'"';
		if($char_row[0] == 'latin1_german1_ci')
			print " selected";
		print '>'.$char_row[0].'</option>';
	}
 
	print '</select>';
	print '<input type="submit" value="Convert">';
	print '</form>';
}
else
{
	$convert_from = $_POST['convert_from'];
 
	set_time_limit(0); 
 
	// collation you want to change it to:
	$convert_to   = 'utf8_general_ci';
 
	// character set of new collation:
	$character_set= 'utf8';
 
	$rs_tables = mysql_query(" SHOW TABLES ") or die(mysql_error());
	$error_occured = false;
	print '<pre>';
	$amount = 0; // number of converted tables
	while ($row_tables = mysql_fetch_row($rs_tables)) 
	{
		// only convert prefixed tables OR all if an empty prefix was used
		if(strlen($_BIGACE['db']['prefix']) == 0 || stripos($row_tables[0], $_BIGACE['db']['prefix']) !== false)  
		{
			$table = mysql_real_escape_string($row_tables[0]);
 
			$conv_table_sql = "ALTER TABLE `$table` DEFAULT CHARACTER SET $character_set;";
			mysql_query($conv_table_sql);
			if(mysql_errno() != 0) {
				echo $conv_table_sql . "\r\n";
				echo "<b>".mysql_error()."</b>\r\n";
				$error_occured = true;
			}
			else {
				$amount++;
			}
			
			$conv_started = false;
			$conv_sql = "ALTER TABLE `$table`";
			$rs = mysql_query(" SHOW FULL FIELDS FROM `$table` ") or die(mysql_error());
			while ($row=mysql_fetch_assoc($rs)) {
 
				if ($row['Collation']!=$convert_from) {
				    continue;
				}
 
				// Alter field collation:
				$field = mysql_real_escape_string($row['Field']);
				if($conv_started)
					$conv_sql .= ", ";
				$conv_sql .= " CHANGE `$field` `$field` $row[Type] CHARACTER SET $character_set COLLATE $convert_to";
				$conv_started = true;
			}
			$conv_sql .= ";";
 
			// execute the conversion if at least one column with old collation was found
			if($conv_started) {
				mysql_query($conv_sql);
				if(mysql_errno() != 0) {
					echo $conv_sql." \r\n";
					echo "<b>".mysql_error()."</b>\r\n";
					$error_occured = true;
				}
			}
		}
	}
	print "</pre>";
 
	if($amount < 29) {
		echo '<h1>Only <u>'.$amount.' tables were converted, which might be an error! You should <a href="http://forum.bigace.de">contact us at the Forum</a>.</h1>';
	}
	else {
		if($error_occured)
			echo '<h1>An error occured :( <a href="http://forum.bigace.de">Please contact us at the Forum</a>.</h1>';
		else
			echo '<h1>Congratulations, your data was converted!</h1>';
	}
}
?>