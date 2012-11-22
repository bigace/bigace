SELECT a.* FROM 
    {DB_PREFIX}item_{ITEMTYPE} a
WHERE 
    a.language='{LANGUAGE}' 
AND 
    a.cid='{CID}' 
	{EXTENSION}
GROUP BY
    a.id
ORDER BY 
    a.createdate
{ORDER}
LIMIT
    {LIMIT_START}, {LIMIT_STOP}