
INSERT INTO {DB_PREFIX}frights (cid,name,defaultvalue,description) VALUES 
({CID}, 'community.admin', 'N', 'Allows to edit the Community-Domain Mapping for all Communities.');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}group_frights (cid,group_id,fright) VALUES 
({CID}, 40, 'community.admin');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}frights (cid,name,defaultvalue,description) VALUES 
({CID}, 'community.installation', 'N', 'Allows to add new Communities to this installation.');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}group_frights (cid,group_id,fright) VALUES 
({CID}, 40, 'community.installation');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}frights (cid,name,defaultvalue,description) VALUES 
({CID}, 'community.maintenance', 'N', 'Allows to edit the Website Maintenance State and Message.');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}group_frights (cid,group_id,fright) VALUES 
({CID}, 40, 'community.maintenance');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}frights (cid,name,defaultvalue,description) VALUES 
({CID}, 'community.deinstallation', 'N', 'Allows to deinstall Communities. SECURITY WARNING!');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}group_frights (cid,group_id,fright) VALUES 
({CID}, 40, 'community.deinstallation');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'blix.design', 'show.footer.login', 'TRUE', 'boolean');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'blix.design', 'show.home.in.topmenu', 'TRUE', 'boolean');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'blix.design', 'show.google.search', '0', 'boolean');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'blix.design', 'copyright.footer', 'Kevin Papst', 'string');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'blix.design', 'top.menu.start.id', '-1', 'menu_id');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'content', 'show.default.content', 'TRUE', 'boolean');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}frights (cid,name,defaultvalue,description) VALUES 
({CID}, 'edit.usergroup', 'N', 'Allows to edit the Usergroups and their Member links.');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}group_frights (cid,group_id,fright) VALUES 
({CID}, 40, 'edit.usergroup');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE {DB_PREFIX}configuration SET value = 'standard' WHERE package = 'admin' AND name = 'default.style';
