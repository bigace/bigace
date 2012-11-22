SELECT * FROM 
	{DB_PREFIX}comments 
WHERE
	`cid` = {CID} AND (`activated` = '1' OR `ip` = '{IP}')
   {EXTENSION}
ORDER BY `timestamp` {ORDER_BY}
LIMIT {START}, {END}
