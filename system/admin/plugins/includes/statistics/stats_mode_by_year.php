<?php

	$sql = "SELECT COUNT(*) AS cnt, YEAR(date) AS year
	FROM "._TABLE_STATISTIC." where cid='"._CID_."' GROUP BY year ORDER BY year";

	$year_result = $STAT_SERVICE->executeStatisticSQL($sql);

	echo '<br><hr noshade>';

	// for each year one row
	for($i=0; $i < $year_result->count(); $i++)
	{
		$row = $year_result->next();
		$year = $row["year"];

		echo '<h3>'.getTranslation('stats_year').' '.$year.': '.$row["cnt"].'</h3>';

		$sql = "SELECT COUNT(*) as cnt, MONTHNAME(date) as mn,
		MONTH(date) as month
		FROM "._TABLE_STATISTIC." WHERE YEAR(date) = ".$year." and cid='"._CID_."'
		GROUP BY mn,month
		ORDER BY month";
		$result = $STAT_SERVICE->executeStatisticSQL($sql);

		echo '<br>' .
				'	<table style="'._TABLE_STYLE.'">' .
						'<tr>' .
						'	<th>'.getTranslation('stats_month').'</th>' .
						'	<th>'.getTranslation('stats_results').'</th>' .
						'</tr>';
						for($a=0; $a < $result->count(); $a++)
						{
							$row1 = $result->next();
							echo '<tr><td>'.$row1["mn"].'</td><td>'.$row1["cnt"].'</td></tr>';
						}
				echo '</table>';

		$sql = "SELECT COUNT(*) as cnt, WEEK(date) as wk
		FROM "._TABLE_STATISTIC." WHERE YEAR(date) = $year
		 and cid='"._CID_."'
		GROUP BY wk
		ORDER BY wk";

		$result = $STAT_SERVICE->executeStatisticSQL($sql);
		echo '<br><table style="'._TABLE_STYLE.'">';
		echo '<tr><th>'.getTranslation('stats_week').'</th><th>'.getTranslation('stats_results').'</th></tr>';
		for($a=0; $a < $result->count(); $a++)
		{
			$row1 = $result->next();
			echo '<tr><td>'.$row1["wk"].'</td><td>'.$row1["cnt"].'</td></tr>';
		}
		echo '</table>';
		echo '<br><hr noshade>';
	}

?>