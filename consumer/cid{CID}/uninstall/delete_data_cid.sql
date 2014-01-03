#
# Script to delete all consumer data from database
# Copyright: Kevin Papst
# 
# $Id: delete_data_cid.sql,v 1.6 2010/04/24 11:40:58 kpapst Exp $
# (Leave semicolon!);

DELETE FROM {DB_PREFIX}autojobs WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}category WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}configuration WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}content WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}design WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}design_contents WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}design_portlets WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}events WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}frights WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}future_user_right WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}generic_entries WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}generic_mappings WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}generic_sections WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}groups WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}group_frights WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}group_right WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}id_gen WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}item_1 WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}item_4 WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}item_5 WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}item_category WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}item_future WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}item_history WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}item_project_num WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}item_project_text WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}logging WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}plugins WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}session WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}smarty_history WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}statistics WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}stylesheet WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}template WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}unique_name WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}user WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}user_attributes WHERE cid='{CID}';
DELETE FROM {DB_PREFIX}user_group_mapping WHERE cid='{CID}';
