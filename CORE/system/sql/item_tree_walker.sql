SELECT 
	{COLUMNS}
FROM 
	{DB_PREFIX}item_{ITEMTYPE} a
RIGHT JOIN {DB_PREFIX}group_right b
	ON b.itemid=a.id AND b.cid=a.cid AND b.itemtype='{ITEMTYPE}' AND b.value > '{RIGHT_VALUE}' 
RIGHT JOIN {DB_PREFIX}user_group_mapping c
	ON c.group_id = b.group_id AND c.cid=b.cid AND c.userid='{USER}'
	{JOIN_EXTENSION}
WHERE 
	a.cid='{CID}' 
	{WHERE_EXTENSION}
GROUP BY a.id 
{ORDER_BY}
{LIMIT}