UPDATE {DB_PREFIX}configuration set type = 'adminstyle' WHERE 
package = 'admin' AND name = 'default.style' AND type = 'string';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}configuration (cid, package, name, value, type) VALUES
({CID}, 'admin', 'max.upload.size', '10000000', 'long');

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
