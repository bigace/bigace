SELECT 
	{COLUMNS} 
FROM 
	{DB_PREFIX}item_{ITEMTYPE} a
	{JOIN_EXTENSION}
WHERE 
	a.cid='{CID}' 
	{WHERE_EXTENSION} 
GROUP BY a.id 
{ORDER_BY} 
{LIMIT}