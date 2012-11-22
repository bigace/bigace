INSERT INTO {DB_PREFIX}statistics(cid,ip,browser,date,timestamp,referer,command,itemid,userid,session_id) 
            VALUES({CID},{IP},{BROWSER},now(),unix_timestamp(),{REFERER},{COMMAND},{ITEMID},{USERID},{SESSION})
