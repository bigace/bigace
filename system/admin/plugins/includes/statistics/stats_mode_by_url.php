<?php

	$result = $STAT_SERVICE->getTopURLs('10', $_FILTER['COMMANDS'], _CID_);

	echo '<table style="'._TABLE_STYLE.'" cellspacing="2" width="'.ADMIN_MASK_WIDTH_SMALL.'">';
	echo '<tr><th>'.getTranslation('stats_result_count').'</th><th>'.getTranslation('stats_result_type').'</th>';
	echo '<th>'.getTranslation('stats_result_url').'</th><th>'.getTranslation('stats_result_show').'</th></tr>';

	$masterItemType = new MasterItemType();

	while ($row = $result->next())
	{
	    if ($row["command"] != '')
	    {
    		$type = $masterItemType->getItemTypeForCommand($row["command"]);
            if ($type != null)
            {
        		$ISERVICE = new ItemService($type);
        		$itemtype = new Itemtype($type);
        		$myitem = $ISERVICE->getItem($row["itemid"]);
        		// make sure the values belong to an existing item
        		if($myitem->exists())
        		{
	        		$url = createCommandLink($row["command"], $myitem->getID());

	        		echo '<tr><td>';
	        		echo $row["cnt"];
	        		echo "</td><td>";
	        		echo $itemtype->getClassName();
	        		echo "</td><td>";
	        		echo $myitem->getName();
	        		echo "</td><td>";
	        		echo '<a href="'.$url.'" target="_blank">'.str_replace($GLOBALS['_BIGACE']['DOMAIN'],'', $url).'</a>';
	        		echo "</td></tr>";

	        		$tooltip = 'Hits Counter: '.$row["cnt"].'\nItemtype: '.$itemtype->getClassName().'\n\nAvailable at: ' . $url;
        		}
            }
    	}
	}

	echo '</table>';
