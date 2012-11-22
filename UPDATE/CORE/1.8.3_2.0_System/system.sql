ALTER TABLE `{DB_PREFIX}item_project_num` CHANGE `project_key` `project_key` VARCHAR( 50 ) NOT NULL DEFAULT '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE `{DB_PREFIX}item_project_text` CHANGE `project_key` `project_key` VARCHAR( 50 ) NOT NULL DEFAULT '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

UPDATE `{DB_PREFIX}item_project_text` set `project_key`='portlet.config.column.1' where `project_key`='10';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

DELETE FROM {DB_PREFIX}configuration WHERE package = 'admin' AND name = 'dimension.large.mode';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}session ADD `cid` INT NOT NULL  AFTER `id`;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}session ADD `userid` INT NOT NULL  AFTER `cid`;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}session ADD `ip` VARCHAR( 20 ) NOT NULL  AFTER `userid`;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

CREATE TABLE {DB_PREFIX}user_attributes(
cid int( 11 ) NOT NULL,
userid int( 11 ) NOT NULL default '0',
attribute_name VARCHAR( 50 ) NOT NULL ,
attribute_value VARCHAR( 255 ) NOT NULL DEFAULT '',
PRIMARY KEY ( userid, cid, attribute_name )
) TYPE = MYISAM ;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'firstname',  firstname from {DB_PREFIX}userdata where userid != '1' and userid != '2' and firstname != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'lastname',   lastname  from {DB_PREFIX}userdata where userid != '1' and userid != '2' and lastname  != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'email',      email     from {DB_PREFIX}userdata where userid != '1' and userid != '2' and email     != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'homepage',   homepage  from {DB_PREFIX}userdata where userid != '1' and userid != '2' and homepage  != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'phone',      phone     from {DB_PREFIX}userdata where userid != '1' and userid != '2' and phone     != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'mobile',     mobile    from {DB_PREFIX}userdata where userid != '1' and userid != '2' and mobile    != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'fax',        fax       from {DB_PREFIX}userdata where userid != '1' and userid != '2' and fax       != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'company',    company   from {DB_PREFIX}userdata where userid != '1' and userid != '2' and company   != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'street',     street    from {DB_PREFIX}userdata where userid != '1' and userid != '2' and street    != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'city',       city      from {DB_PREFIX}userdata where userid != '1' and userid != '2' and city      != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'citycode',   citycode  from {DB_PREFIX}userdata where userid != '1' and userid != '2' and citycode  != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'country',    country   from {DB_PREFIX}userdata where userid != '1' and userid != '2' and country   != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'birthdate',  birthdate from {DB_PREFIX}userdata where userid != '1' and userid != '2' and birthdate != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'extended1',  extended1 from {DB_PREFIX}userdata where userid != '1' and userid != '2' and extended1 != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'extended2',  extended2 from {DB_PREFIX}userdata where userid != '1' and userid != '2' and extended2 != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

insert into {DB_PREFIX}user_attributes (userid,cid,attribute_name,attribute_value) select userid,cid,'extended3',  extended3 from {DB_PREFIX}userdata where userid != '1' and userid != '2' and extended3 != '';

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

DROP TABLE IF EXISTS {DB_PREFIX}userdata;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

CREATE TABLE {DB_PREFIX}user_group_mapping (
  cid INT NOT NULL,
  userid INT NOT NULL,
  group_id INT NOT NULL default '0',
  PRIMARY KEY (cid,userid,group_id)
) TYPE=MYISAM;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

INSERT INTO {DB_PREFIX}user_group_mapping SELECT cid, id, group_id FROM {DB_PREFIX}user;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}user DROP group_id;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}statistics CHANGE userid userid INT(11) NOT NULL;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}statistics_history CHANGE userid userid INT(11) NOT NULL;
