SELECT 
    a.name, b.cid, b.group_id, b.fright
FROM 
    {DB_PREFIX}frights a, {DB_PREFIX}group_frights b
WHERE 
    a.cid='{CID}'
AND 
    a.name='{FRIGHT_NAME}' 
AND 
    b.cid='{CID}'
AND 
    b.fright='{FRIGHT_NAME}' 
AND 
    b.group_id='{GROUP_ID}';