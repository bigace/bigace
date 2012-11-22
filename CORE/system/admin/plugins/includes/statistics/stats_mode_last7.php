<?php
    
	$sql = "SELECT count(*) as cnt
	FROM "._TABLE_STATISTIC."
	WHERE date >= DATE_SUB( CURRENT_DATE, INTERVAL 6 day )
	 and cid='"._CID_."'";
	$allLastWeek = $STAT_SERVICE->executeStatisticSQL($sql);
	$allLastWeek = $allLastWeek->next();

	$sql = "SELECT count(*) as cnt, DAYNAME(date) as dnr,
	TO_DAYS(date) as tdr
	FROM "._TABLE_STATISTIC."
	WHERE date >= DATE_SUB( CURRENT_DATE, INTERVAL 6 day )
	 and cid='"._CID_."'
	GROUP BY dnr,tdr
	ORDER BY tdr";
	$result = $STAT_SERVICE->executeStatisticSQL($sql);

	echo '<table style="'._TABLE_STYLE.'" width="'.ADMIN_MASK_WIDTH_SMALL.'">';
	echo '<tr><th align="left">Day</th><th></th><th align="right">Total Hits</th></tr>';

	for($i=0; $i < $result->count(); $i++)
	{
		$row = $result->next();

		echo '<tr><td>';
		echo $row["dnr"];
    	echo '</td>';
        echo '<td align="left">';
    	echo getScaleImage($allLastWeek["cnt"], $row["cnt"]);
       	echo '</td>';
        echo '<td align="right">';
		echo $row["cnt"];
		echo '</td></tr>';
	}

	echo '<tr><td colspan="3"><hr></td></tr>';

	echo '<tr><td>';
	echo 'Total';
	echo '</td>';
    echo '<td align="left">';
	echo getScaleImage($allLastWeek["cnt"], $allLastWeek["cnt"]);
   	echo '</td>';
    echo '<td align="right">';
	echo $allLastWeek["cnt"];
	echo '</td></tr>';

	echo '</table>';
