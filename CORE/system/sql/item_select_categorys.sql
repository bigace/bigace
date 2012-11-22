SELECT * FROM {DB_PREFIX}category c 
RIGHT JOIN {DB_PREFIX}item_category ic 
	ON c.id = ic.categoryid
WHERE ic.itemtype='{ITEMTYPE_ID}' 
	AND ic.itemid='{ITEM_ID}' 
	AND ic.cid='{CID}'
	AND c.cid=ic.cid
