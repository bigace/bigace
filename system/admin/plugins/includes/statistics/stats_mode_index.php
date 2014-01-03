<?php

    $distinct_column = 'session_id'; // 'ip';

	// Analyze and calculate time elapsed data (i.e. Daily hits, hourly hits)
	$avgday = 0;
	$sql = "SELECT TO_DAYS(MAX(date)) - TO_DAYS(MIN(date)) AS record FROM "._TABLE_STATISTIC." where cid='"._CID_."'";
	$results  =  $STAT_SERVICE->executeStatisticSQL( $sql );
	for($i = 0; $i < $results->count(); $i++) {
		$myrow = $results->next();
		$avgday = $myrow["record"];
	}

	$total = $STAT_SERVICE->countSessions();

	$sql  =  "SELECT DISTINCT(".$distinct_column.") FROM "._TABLE_STATISTIC." WHERE browser LIKE ('%MSIE%') and cid='"._CID_."'";
	$results  =  $STAT_SERVICE->executeStatisticSQL( $sql );
	$ms  =  $results->count();
	$netscape  = ( $total  -  $ms );

	$sql  =  "SELECT DISTINCT(".$distinct_column.") FROM "._TABLE_STATISTIC." WHERE browser LIKE ('%".$_OS['win']->getDefinition()."%') and cid='"._CID_."'";
	$results  =  $STAT_SERVICE->executeStatisticSQL( $sql );
	$windows  =  $results->count();
	$mac  = ( $total  -  $windows );

	// Create Percentage Data from our values
	$macval  = ($mac + $windows > 0) ? ( $windows /( $mac + $windows )) : 1;
	$winval  = ($mac + $windows > 0) ? ( $mac /( $mac + $windows )) : 1;
	$msval   = ($netscape + $ms > 0) ? ( $netscape /( $netscape + $ms )) : 1;
	$nsval   = ($netscape + $ms > 0) ? ( $ms /( $netscape + $ms )) : 1;

	$allHits = $STAT_SERVICE->countAllHits(_CID_);

	if ($avgday == 0)
	    $avgday = 1;
	$avgDhitsUser  = ( $total / $avgday );
	$avgDhitsUser  = round ( $avgDhitsUser );
	$avgHhitsUser = round( $avgDhitsUser / 24 );

	$avgDhits  = ( $allHits /$avgday );
	$avgDhits  =  round ( $avgDhits );
	$avgHhits = round( $avgDhits / 24 );

	// *****************************************************************

	echo '<table border="0" cellpadding="5" width="100%" align="center">';

	echo '<tr><td>';
    	echo getTranslation('stats_period').'<br>';
    	echo date("d.m.Y",$STAT_SERVICE->getMinTimestamp());
    	echo  " - ";
    	echo date("d.m.Y",$STAT_SERVICE->getMaxTimestamp());
    	echo '<br><br>';
    	echo sprintf( getTranslation('stats_complete'), $avgday, $allHits, $total);
	echo "</td>";

	echo '<td align="center">';
    	echo '<table cellpadding="5" border="1" bgcolor="#cccccc">';
    	echo '<tr><td align="center" valign="bottom">';
    	graphic( array($winval,$windows), array($macval,$mac), getTranslation('stats_hits'));
    	echo  "</td>";
    	echo "</tr>";
    	echo '<tr><td align="center">';
    	echo '<span style="font-weight:bold;color:'._COLOR_1.'">Windows</span> VS <span style="font-weight:bold;color:'._COLOR_2.'">Other</span>';
    	echo "</td></tr></table>";
	echo '</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td align="center">&nbsp;';
	echo '</td>';
	echo '<td align="center">';
    	echo '<table cellpadding="5" border="1" bgcolor="#cccccc">';
    	echo '<tr><td align="center" valign="bottom">';
    	graphic(array($msval,$ms), array($nsval,$netscape), 'Hits');
    	echo "</td>";
    	echo "</tr>";
    	echo '<tr><td align="center" align="center">';
    	echo '<span style="font-weight:bold;color:'._COLOR_1.'">Internet Explorer</span> VS <span style="font-weight:bold;color:'._COLOR_2.'">Other</span>';
    	echo "</td></tr></table>";
	echo '</td>';
	echo '</tr>';

	echo '<tr><td colspan="2" align="center">&nbsp;</td></tr>';

	echo '<tr><td>';
    	echo '<u>'.getTranslation('stats_pagehits').'</u><br>';
        echo getTranslation('stats_pagehits_info');
	echo "</td>";
	echo '<td align="center">';
    	echo '<table border="0" cellspacing="0" width="200px">';
    	echo '<tr>';
    	echo '<td>'.getTranslation('stats_total').':';
    	echo '</td><td align="right">';
    	echo '<b>'.$allHits.'</b>';
        echo '</td></tr><tr>';
    	echo '<td>'.getTranslation('stats_per_day').':';
    	echo '</td><td align="right">';
    	echo '<b>'.$avgDhits.'</b>';
        echo '</td></tr><tr>';
    	echo '<td>'.getTranslation('stats_per_hour').':';
    	echo '</td><td align="right">';
    	echo '<b>'.$avgHhits.'</b>';
    	echo '</td></tr>';
    	echo '</table>';
	echo '</td>';
	echo "</tr>";

	echo '<tr><td>';
    	echo '<u>'.getTranslation('stats_visitor').'</u><br>';
        echo getTranslation('stats_visitor_info');
	echo "</td>";
	echo '<td align="center">';
    	echo '<table border="0" cellspacing="0" width="200px">';
    	echo '<tr>';
    	echo '<td>'.getTranslation('stats_total').':';
    	echo '</td><td align="right">';
    	echo '<b>'.$total.'</b>';
        echo '</td></tr><tr>';
    	echo '<td>'.getTranslation('stats_per_day').':';
    	echo '</td><td align="right">';
    	echo '<b>'.$avgDhitsUser.'</b>';
        echo '</td></tr><tr>';
    	echo '<td>'.getTranslation('stats_per_hour').':';
    	echo '</td><td align="right">';
    	echo '<b>'.$avgHhitsUser.'</b>';
    	echo '</td></tr>';
    	echo '</table>';

	echo '</td></tr>';

	echo '</table>';

?>