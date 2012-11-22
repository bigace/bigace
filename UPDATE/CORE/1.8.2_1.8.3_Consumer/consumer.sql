INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'editor', 'fckeditor.toolbar', 'full', 'string');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}frights (cid,name,defaultvalue,description) VALUES 
({CID}, 'editor.html.sourcecode', 'N', 'Gives editing access to the HTML Sourcecode.');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}frights (cid,name,defaultvalue,description) VALUES 
({CID}, 'edit.portlet.settings',  'N', 'Allows to open, edit and save the Portlet settings for each Page, the User has write rights on.');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}group_frights (cid,group_id,fright) VALUES 
({CID}, 25, 'editor.html.sourcecode');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}group_frights (cid,group_id,fright) VALUES 
({CID}, 30, 'editor.html.sourcecode');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}group_frights (cid,group_id,fright) VALUES 
({CID}, 40, 'editor.html.sourcecode');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}group_frights (cid,group_id,fright) VALUES 
({CID}, 30, 'edit.portlet.settings');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}group_frights (cid,group_id,fright) VALUES 
({CID}, 40, 'edit.portlet.settings');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
