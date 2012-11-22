INSERT INTO {DB_PREFIX}groups (group_id, cid, group_name) VALUES 
(25, {CID}, 'Revisor');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}frights (id,cid,name,description) VALUES 
(17, {CID}, 'updates_manager', 'Inheriting this right enables the User to manage and install Updates.');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}frights (id,cid,name,description) VALUES 
(18, {CID}, 'admin_configurations', 'Allows the User to change Configurations of the Core System.');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}frights (id,cid,name,description) VALUES 
(19, {CID}, 'edit_menus',           'Allows User to edit Menus (use Editor, Workflow...)');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}frights (id,cid,name,description) VALUES 
(20, {CID}, 'edit_items',           'Allows User to edit Items (update existing ones).');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}group_frights (cid,group_id,fright_id) VALUES 
({CID}, 40, 17);

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}group_frights (cid,group_id,fright_id) VALUES 
({CID}, 40, 18),
({CID}, 40, 19),
({CID}, 40, 20),
({CID}, 30, 19),
({CID}, 30, 20),
({CID}, 25,  1),
({CID}, 25,  2),
({CID}, 25,  5),
({CID}, 25, 16),
({CID}, 25, 19),
({CID}, 25, 20),
({CID}, 20, 19),
({CID}, 20, 20);

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'system', 'write.statistic', 'TRUE', 'boolean'),
({CID}, 'system', 'hide.footer', '0', 'boolean'),
({CID}, 'system', 'send.pragma.no.cache', 'TRUE', 'boolean'),
({CID}, 'system', 'failed.logins.before.deactivate', '5', 'integer'),
({CID}, 'workflow', 'singlereview.group.id', '30', 'group'),
({CID}, 'admin',  'default.style', 'standard', 'string');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'login',  'login.with.user.language', 'TRUE', 'boolean');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'editor', 'default.editor', 'fckeditor', 'string');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE {DB_PREFIX}item_1 SET workflow='';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE {DB_PREFIX}item_4 SET workflow='';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE {DB_PREFIX}item_5 SET workflow='';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE {DB_PREFIX}configuration SET package='login', name='failures.before.deactivate' WHERE package='system' AND name='failed.logins.before.deactivate';

