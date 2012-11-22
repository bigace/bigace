SELECT * FROM 
	{DB_PREFIX}comments 
WHERE
	`cid` = {CID} AND `itemtype` = {ITEMTYPE} AND `itemid` = {ITEMID} AND `language` = {LANGUAGE}
	AND (`activated` = '1' OR `ip` = {IP})
ORDER BY `timestamp` ASC