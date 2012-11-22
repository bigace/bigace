SELECT 
    a.name, b.cid, b.group_id, b.fright 
FROM 
    {DB_PREFIX}frights a, {DB_PREFIX}group_frights b, {DB_PREFIX}user_group_mapping c
WHERE 
    a.cid='{CID}'
AND 
    b.cid='{CID}'
AND 
    c.cid='{CID}'
AND 
    a.name='{FRIGHT_NAME}' 
AND 
    b.fright='{FRIGHT_NAME}' 
AND 
    c.userid='{USER_ID}'
AND 
    b.group_id=c.group_id;