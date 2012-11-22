SELECT a.* FROM {DB_PREFIX}groups a, {DB_PREFIX}user_group_mapping b
WHERE a.cid='{CID}' AND b.cid='{CID}' AND b.userid='{USER}' AND a.group_id=b.group_id
ORDER BY a.group_id ASC