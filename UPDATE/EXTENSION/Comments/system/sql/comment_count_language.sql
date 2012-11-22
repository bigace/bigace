SELECT count(`id`) as `counter` FROM  
	{DB_PREFIX}comments 
WHERE
	`cid` = {CID} AND `itemtype` = {ITEMTYPE} AND 
	`itemid` = {ITEMID} AND `language` = {LANGUAGE} AND 
	`activated` = '1'
