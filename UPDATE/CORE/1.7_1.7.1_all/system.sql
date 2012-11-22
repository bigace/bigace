UPDATE `groups` SET group_id = group_id * 10;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE `group_frights` SET group_id = group_id * 10;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE `statistics` ADD `command` VARCHAR( 25 ) NOT NULL AFTER `url`;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE `statistics` ADD `itemid` INT( 11 ) NULL AFTER `command`;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE `statistics` ADD `userid` VARCHAR( 25 ) NOT NULL AFTER `itemid`;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE statistics SET itemid = "-1", command = "menu" WHERE url = "/index.php";

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE statistics SET itemid = "-1", command = "menu" WHERE url = "/";

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE statistics SET itemid = "-1", command = "menu" WHERE url = "/?BSID=";

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE statistics SET itemid = "-1", command = "menu" WHERE url like "/?BSID=%";

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE statistics SET itemid = "-1", command = "menu" WHERE url like "/index.php%";

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE statistics SET command = "menu" WHERE url like "/bigace/menu/%";

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE statistics SET command = "image" WHERE url like "/bigace/image/%";

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE statistics SET command = "file" WHERE url like "/bigace/file/%";

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE statistics SET command = "login" WHERE url like "/bigace/login/%";

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE statistics SET command = "logout" WHERE url like "/bigace/logout/%";

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE statistics SET command = "editor" WHERE url like "/bigace/editor/%";


# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE `statistics` DROP `searchstring`;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE `statistics` DROP `url`;
