INSERT INTO {DB_PREFIX}item_history SELECT '{ITEMTYPE}' AS itemtype,{DB_PREFIX}{TABLE}.* FROM {DB_PREFIX}{TABLE} WHERE id='{ITEM_ID}' AND cid='{CID}' AND language='{LANGUAGE_ID}'