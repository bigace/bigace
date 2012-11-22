UPDATE {DB_PREFIX}design 
SET description={DESCRIPTION},stylesheet={STYLESHEET},template={TEMPLATE},portlets={PORTLETS} 
WHERE cid={CID} AND NAME={NAME}
