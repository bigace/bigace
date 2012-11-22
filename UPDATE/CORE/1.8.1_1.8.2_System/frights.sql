ALTER TABLE `{DB_PREFIX}frights` ADD `defaultvalue` ENUM( 'Y', 'N' ) DEFAULT 'N' NOT NULL AFTER `name`;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE `{DB_PREFIX}group_frights` ADD `value` ENUM( 'Y', 'N' ) DEFAULT 'Y' NOT NULL AFTER `fright_id`;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
