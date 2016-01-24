SELECT 
	count(distinct(a.id)) as counter 
FROM 
	{DB_PREFIX}item_{ITEMTYPE} a, {DB_PREFIX}group_right b, {DB_PREFIX}user_group_mapping c
WHERE 
	a.cid='{CID}' and a.id <> '{TOP_LEVEL_ID}'
	AND b.itemtype='{ITEMTYPE}' AND b.cid='{CID}' AND b.itemid=a.id 
	AND (c.cid='{CID}' AND c.userid='{USER}' AND c.group_id = b.group_id AND b.value > '{RIGHT_VALUE}') 
	