INSERT INTO {DB_PREFIX}item_project_text (itemtype, id, cid, language, project_key, project_value) VALUES 
(1, -1, {CID}, 'de', 'portlet.config.column.1', '<?xml version=''1.0''?>\n <Portlets>\n    <ToolPortlet css="toolPortlet" login="1" home="" />\n    <NavigationPortlet css="navigationPortlet" language="" id="" />\n    <LastEditedItemsPortlet css="lastEditedItemsPortlet" language="" amount="5" />\n </Portlets>\n'),
(1, -1, {CID}, 'en', 'portlet.config.column.1', '<?xml version=''1.0''?>\n <Portlets>\n    <ToolPortlet css="toolPortlet" login="1" home="" />\n    <NavigationPortlet css="navigationPortlet" language="" id="" />\n    <LastEditedItemsPortlet css="lastEditedItemsPortlet" language="" amount="5" />\n </Portlets>\n');

INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'blix.design', 'show.footer.login', 'TRUE', 'boolean');

INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'blix.design', 'show.home.in.topmenu', 'TRUE', 'boolean');

INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'blix.design', 'show.google.search', '0', 'boolean');

INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'blix.design', 'copyright.footer', 'Kevin Papst', 'string');

INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'blix.design', 'top.menu.start.id', '-1', 'menu_id');
