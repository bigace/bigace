UPDATE 
	{DB_PREFIX}gaestebuch 
SET 
	name={NAME}, email={EMAIL}, homepage={HOMEPAGE}, eintrag={COMMENT} 
WHERE 
	id={ENTRY_ID} AND cid={CID}
