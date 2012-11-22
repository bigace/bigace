SELECT a.* FROM {DB_PREFIX}group_right a, {DB_PREFIX}user_group_mapping b
WHERE 
	b.cid='{CID}' 
	AND b.userid='{USER_ID}' 
	AND a.group_id = b.group_id 
	AND a.cid='{CID}' 
	AND a.itemtype='{ITEMTYPE}' 
	AND a.itemid='{ITEM_ID}' 
