SELECT count(id) as amount FROM {DB_PREFIX}item_future WHERE itemtype='{ITEMTYPE}' AND id='{ITEM_ID}' AND cid='{CID}' AND language='{LANGUAGE_ID}'
