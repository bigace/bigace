<?php

	// ------------------- COUNT BOTS -------------------

   // Get all unknown bots
    $sql = "";
	foreach($_BOTS AS $bo)
	{
	    if ($sql != '') {
	        $sql += ' AND ';
	    }
	    $sql += "browser NOT LIKE '%".$bo->getDefinition()."%'";
	}

    $sql = "SELECT count(id) as cnt FROM "._TABLE_STATISTIC." WHERE cid='"._CID_."' AND browser LIKE '%crawl%' OR browser LIKE '%bot%' AND (".$sql.")";
	$result = $STAT_SERVICE->executeStatisticSQL($sql);
	$row = $result->next();
	$bots = $row["cnt"];

	// all bots
	$allBots = $bots;

	foreach($_BOTS AS $bo)
	{
	    $res =  $STAT_SERVICE->countTotalBrowser($bo->getDefinition(),_CID_);
	    $allBots += $res;
	}

	// ------------------- COUNT BOTS -------------------

	// all hits
	$overall = $STAT_SERVICE->countAllHits(_CID_);

    // Get all Mozilla/Netscape Browser (not Firefox!)
	$sql = "SELECT count(id) as cnt FROM "._TABLE_STATISTIC." WHERE cid='"._CID_."' AND browser LIKE '%Mozilla%' AND (browser NOT LIKE '%MSIE%' and browser NOT LIKE '%Firefox%' and browser NOT LIKE '%Opera%' and browser NOT LIKE '%iCab%' and browser NOT LIKE '%Konqueror%' and browser NOT LIKE '%Girafabot%' and browser NOT LIKE '%slurp%' and browser NOT LIKE '%zyborg%' and browser NOT LIKE '%googlebot%')";
	$result = $STAT_SERVICE->executeStatisticSQL($sql);
	$row = $result->next();
	$ns = $row["cnt"];

	$total = $overall - $allBots;
	$other = $total;

	echo '<table style="'._TABLE_STYLE.'" width="'.ADMIN_MASK_WIDTH_SMALL.'">';

	echo '<tr>';
        echo '<th align="left">';
    	echo "Browser";
    	echo '</th>';
        echo '<th align="right">';
    	echo 'Page Hits';
    	echo '</th>';
    echo '</tr>';

	echo '<tr>';
        echo '<td>';
    	echo "Mozilla";
    	echo '</td>';
        echo '<td align="right">';
    	echo $ns;
    	echo '</td>';
    echo '</tr>';

	foreach($_BROWSER AS $br)
	{
	    $def = $br->getDefinition();
	    $res =  $STAT_SERVICE->countTotalBrowser($def,_CID_);
	    //$total += $res;
	    $other -= $res;

    	echo '<tr>';
            echo '<td>';
        	echo $br->getName();
        	echo '</td>';
            echo '<td align="right">';
        	echo $res;
        	echo '</td>';
        echo '</tr>';
	}

	echo '<tr>';
        echo '<td>';
    	echo "<i>Other Browser</i>";
    	echo '</td>';
        echo '<td align="right">';
    	echo $other;
    	echo '</td>';
    echo '</tr>';
	echo '<tr>';
        echo '<td colspan="2"><hr></td>';
    echo '</tr>';
	echo '<tr>';
        echo '<td>';
    	echo "Total";
    	echo '</td>';
        echo '<td align="right">';
    	echo $total;
    	echo '</td>';
    echo '</tr>';

	echo "</table>";

/*
	echo "<br><br>";

	echo '<table style="'._TABLE_STYLE.'"> ';
	echo '<tr><th>Browser</th><th>Page Hits</th></tr>';

	echo '<tr><td>';
	echo "Bots/Spider";
	echo '</td><td align="right">';
	echo $allBots;
	echo '</td></tr>';
	echo '<tr><td>';
	echo "Manual Browser (Human)";
	echo '</td><td align="right">';
	echo $total;
	echo '</td></tr>';
	echo '<tr><td colspan="2"><hr></td></tr>';
	echo '<tr><td>';
	echo "TOTAL";
	echo '</td><td align="right">';
	echo $overall;
	echo '</td></tr>';

	echo "</table>";
*/
?>
