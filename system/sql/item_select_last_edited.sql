SELECT a.* FROM 
    {DB_PREFIX}item_{ITEMTYPE} a, {DB_PREFIX}group_right b, {DB_PREFIX}user_group_mapping c
WHERE 
    a.language='{LANGUAGE}' AND a.cid='{CID}' 
AND 
	b.itemtype='{ITEMTYPE}' AND b.cid='{CID}' AND b.itemid=a.id
AND 
	(c.cid='{CID}' AND c.userid='{USER}' AND c.group_id = b.group_id AND b.value > '{PERMISSION}') 
	{EXTENSION}
GROUP BY
    a.id
ORDER BY 
    a.modifieddate
{ORDER}
LIMIT
    {LIMIT_START}, {LIMIT_STOP}