SELECT * FROM 
    {DB_PREFIX}{TABLE} 
WHERE 
    id <> '{ITEM_ID}' AND parentid ='{PARENT_ID}' AND language='{LANGUAGE_ID}' AND cid='{CID}' AND num_4 {DIRECTION} '{POSITION}'
ORDER BY 
    num_4 {ORDER} 
LIMIT 0, {LIMIT}