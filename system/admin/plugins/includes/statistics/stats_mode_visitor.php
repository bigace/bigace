<?php
/**
* Script for reading the Top Visitors of your System.
* Display attributes are the Amount, IP and (if available) the Hosts Name.
*
* Copyright (C) Kevin Papst.
*
* For further information go to http://www.bigace.de/
*
* @version $Id$
* @author Kevin Papst 
* @package bigace.administration
*/

$result = $STAT_SERVICE->countTopVisitors('10', _CID_);

echo '<table style="'._TABLE_STYLE.'" cellspacing="2" width="'.ADMIN_MASK_WIDTH_SMALL.'">';
echo '<tr><th>Number of Hits</th><th>IP Address</th><th align="right">Name</th></tr>';

while ($row = $result->next()) {
	echo '<tr><td align="center">';
	echo $row["CNT"];
	echo '</td><td align="center">';
	echo $row["ip"];
	echo '</td><td align="right">';
	echo gethostbyaddr($row["ip"]);
	echo '</td></tr>';
}

echo '</table>';

?>
