SELECT * FROM 
	{DB_PREFIX}comments 
WHERE
	`cid` = {CID} AND `activated` = '0'
ORDER BY `timestamp` ASC
LIMIT {START}, {END}
