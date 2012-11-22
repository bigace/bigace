<?php

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

	echo '<table style="'._TABLE_STYLE.'" width="'.ADMIN_MASK_WIDTH_SMALL.'">';
	echo '<tr><th>Search Spider</th><th align="right">Page Hits</th></tr>';

	foreach($_BOTS AS $bo)
	{
	    $def = $bo->getDefinition();
	    $res =  $STAT_SERVICE->countTotalBrowser($def,_CID_);
	    $allBots += $res;

    	echo '<tr><td>';
    	if ($bo->getLink() != '') {
    	    echo '<a href="'.$bo->getLink().'" target="_blank"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'info.png" border="0"></a> ';
    	}
    	echo $bo->getName();
    	echo '</td><td align="right">';
    	echo $res;
    	echo '</td></tr>';
	}

	echo '<tr><td>';
	echo "<i>Other Bots/Spiders</i>";
	echo '</td><td align="right">';
	echo $bots;
	echo '</td></tr>';
	echo '<tr><td colspan="2"><hr></td></tr>';
	echo '<tr><td>';
	echo "Total";
	echo '</td><td align="right">';
	echo $allBots;
	echo '</td></tr>';
	echo "</table>";

