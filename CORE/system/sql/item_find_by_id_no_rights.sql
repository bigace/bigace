SELECT a.* FROM 
    {DB_PREFIX}item_{ITEMTYPE} a
WHERE 
    a.id LIKE '%{ID}%' AND a.cid='{CID}' 
GROUP BY
    a.id
ORDER BY 
    a.id
{ORDER}
LIMIT
    {LIMIT_START}, {LIMIT_STOP}
