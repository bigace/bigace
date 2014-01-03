<?php

    $fil = $_FILTER['LINKS'];

    $linkParam = 'showDomain';
    $hideOwnUrls = (extractVar($linkParam, '0') == '0');

    if ($hideOwnUrls) {
        // Do not display Links from own Domain!
        array_push($fil, $GLOBALS['_BIGACE']['DOMAIN']);
    }

	$result = $STAT_SERVICE->countTopReferer('10', $fil, _CID_);
    unset($fil);

	echo '<p>';

    if ($hideOwnUrls) {
    	echo '<a href="'.createStatisticLink(_MODE_REFERER, array($linkParam => '1')) . '">Display own Links</a>';
    } else {
    	echo '<a href="'.createStatisticLink(_MODE_REFERER, array($linkParam => '0')) . '">Hide own Links</a>';
    }

	echo '</p><br>' ;

	echo '<table style="'._TABLE_STYLE.'" cellspacing="2" width="'.ADMIN_MASK_WIDTH_SMALL.'">';
	echo '<tr><th align="left">Number of Hits</th><th>Referred From</th></tr>';

	while ($row = $result->next())
	{
		$refe = ((trim($row["referer"]) == '') ? 'No Referer was submitted! Maybe a Visitor with a Bookmark Link...' : $row["referer"]);
		echo '<tr><td>';
		echo $row["CNT"];
		echo "</td><td>";
        echo str_replace($GLOBALS['_BIGACE']['DOMAIN'],'', $refe);
		echo "</td></tr>";
	}

	echo '</table>';


