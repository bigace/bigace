SELECT 
	count(distinct(id)) as counter
FROM 
	{DB_PREFIX}item_{ITEMTYPE}
WHERE 
	cid='{CID}' and id <> '{TOP_LEVEL_ID}'
