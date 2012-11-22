UPDATE {DB_PREFIX}comment_spam_counter 
SET	`counter` = `counter` + 1 
WHERE `cid` = {CID}
