SELECT 
	{COLUMNS}
FROM 
	{DB_PREFIX}item_{ITEMTYPE} a 
WHERE 
	a.id='{ITEM_ID}' AND a.cid='{CID}' AND a.language='{LANGUAGE_ID}' 
