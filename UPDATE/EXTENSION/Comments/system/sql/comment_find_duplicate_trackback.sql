SELECT * FROM 
	{DB_PREFIX}comments 
WHERE
	`cid` = {CID} AND `itemtype` = {ITEMTYPE} AND `itemid` = {ID} AND `language` = {LANGUAGE}
	AND (`type` = 'trackback' OR `type` = 'ping') AND `homepage` = {URL}
