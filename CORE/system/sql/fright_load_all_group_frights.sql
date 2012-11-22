SELECT 
    a.name, a.cid, b.cid, b.group_id, b.fright
FROM 
    {DB_PREFIX}frights a, {DB_PREFIX}group_frights b
WHERE 
    a.cid='{CID}'
AND
    b.cid='{CID}'
AND 
    b.group_id='{GROUP_ID}'
AND 
    a.name=b.fright;