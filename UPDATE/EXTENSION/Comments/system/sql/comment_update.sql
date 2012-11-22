UPDATE {DB_PREFIX}comments 
SET 
	`name` = {NAME}, `email` = {EMAIL}, `homepage` = {HOMEPAGE}, `comment` = {COMMENT}  
WHERE 
	`cid` = {CID} AND `id` = {ID} 