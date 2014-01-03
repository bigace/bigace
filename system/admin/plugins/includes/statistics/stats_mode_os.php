<?php

	$overall = $STAT_SERVICE->countAllHits(_CID_);

    $total = 0;

	echo '<table style="'._TABLE_STYLE.'" width="'.ADMIN_MASK_WIDTH_SMALL.'">';
	echo '<tr><th align="left">Operating System</th><th align="right">Page Hits</th></tr>';

	foreach($_OS AS $os)
	{
	    $def = $os->getDefinition();
	    $res =  $STAT_SERVICE->countOperatingSystem($def,_CID_);
	    $total += $res;

    	echo '<tr><td>';
    	echo $os->getName();
    	echo '</td><td align="right">';
    	echo $res;
    	echo '</td></tr>';
	}

	$other 	= ($overall - $total);

	echo '<tr><td>';
	echo "Other";
	echo '</td><td align="right">';
	echo $other;
	echo '</td></tr>';
	echo '<tr><td colspan="2"><hr></td></tr>';
	echo '<tr><td>';
	echo "<i>Total</i>";
	echo '</td><td align="right"><i>';
	echo $overall;
	echo '</i></td></tr>';
	echo '</table>';


