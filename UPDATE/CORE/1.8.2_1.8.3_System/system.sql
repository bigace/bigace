ALTER TABLE `{DB_PREFIX}item_1` CHANGE `num_2` `num_2` INT( 11 ) NULL DEFAULT '0';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}group_frights ADD fright VARCHAR(50) NOT NULL AFTER group_id;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE {DB_PREFIX}frights a, {DB_PREFIX}group_frights b SET b.fright = a.name WHERE a.id = b.fright_id AND a.cid = b.cid;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}group_frights DROP PRIMARY KEY;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}group_frights DROP fright_id;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}group_frights ADD PRIMARY KEY (cid,group_id,fright);

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}frights DROP PRIMARY KEY;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}frights DROP id;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}frights ADD PRIMARY KEY (cid,name);

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE {DB_PREFIX}item_1 set num_3 = 0;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE {DB_PREFIX}item_4 set num_3 = 0;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE {DB_PREFIX}item_5 set num_3 = 0;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
