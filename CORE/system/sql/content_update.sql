UPDATE {DB_PREFIX}content 
SET cnt_type = {TYPE}, state = {STATE}, position = {POSITION}, valid_from = {VALID_FROM}, valid_to = {VALID_TO}, content = {CONTENT}
WHERE cid = {CID} AND id = {ID} AND language = {LANGUAGE} AND name = {NAME}  
