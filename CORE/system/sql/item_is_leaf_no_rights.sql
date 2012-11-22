SELECT id FROM 
    {DB_PREFIX}item_{ITEMTYPE} 
WHERE 
    parentid ='{PARENT_ID}' AND cid='{CID}'
LIMIT 1