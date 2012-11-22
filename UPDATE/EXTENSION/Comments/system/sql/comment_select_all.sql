SELECT * FROM 
	{DB_PREFIX}comments 
WHERE
	`cid` = {CID}
ORDER BY `timestamp` ASC
LIMIT {START}, {END}
