INSERT INTO {DB_PREFIX}item_future 
	SELECT 
		'{ITEMTYPE}' AS itemtype, {DB_PREFIX}item_{ITEMTYPE}.workflow as workflowname, '{ACTIVITY}' as activity, '{INITIATOR}' as initiator, {DB_PREFIX}item_{ITEMTYPE}.* 
	FROM 
		{DB_PREFIX}item_{ITEMTYPE}
	WHERE 
		id='{ITEM_ID}' AND cid='{CID}' AND language='{LANGUAGE_ID}'