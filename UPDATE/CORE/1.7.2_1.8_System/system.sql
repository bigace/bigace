ALTER TABLE `{DB_PREFIX}item_1` ADD INDEX ( `cid` ) 

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE `{DB_PREFIX}item_1` ADD INDEX ( `language` ) 

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE `{DB_PREFIX}item_1` ADD INDEX ( `parentid` ) 

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE `{DB_PREFIX}item_4` ADD INDEX ( `cid` ) 

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE `{DB_PREFIX}item_4` ADD INDEX ( `language` ) 

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE `{DB_PREFIX}item_4` ADD INDEX ( `parentid` ) 

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE `{DB_PREFIX}item_5` ADD INDEX ( `cid` ) 

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE `{DB_PREFIX}item_5` ADD INDEX ( `language` ) 

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE `{DB_PREFIX}item_5` ADD INDEX ( `parentid` ) 

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

CREATE TABLE {DB_PREFIX}configuration (
  cid int(11) NOT NULL default '0',
  package varchar(50) NOT NULL default '',
  name varchar(50) NOT NULL default '',
  value varchar(255) NOT NULL default '',
  type varchar(10) NOT NULL default 'string',
  PRIMARY KEY  (cid,package,name),
  KEY cid_package (cid,package)
) TYPE=MyISAM;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

CREATE TABLE {DB_PREFIX}item_future (
  itemtype int(11) NOT NULL default '0',
  workflowname varchar(100) NOT NULL default '',
  activity varchar(50) NOT NULL default '',
  initiator int(11) NOT NULL,
  id int(11) NOT NULL default '0',
  cid int(11) NOT NULL default '0',
  language VARCHAR( 20 ) NOT NULL default 'de',
  mimetype varchar(100) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  parentid int(11) NOT NULL default '-1',
  description text,
  catchwords varchar(255) default NULL,
  createdate int(11) NOT NULL,
  createby int(11) NOT NULL,
  modifieddate int(11) NOT NULL,
  modifiedby int(11) NOT NULL,
  workflow varchar(100) NOT NULL default '',
  text_1 text,
  text_2 text,
  text_3 text,
  text_4 text,
  text_5 text,
  num_1 int(11) default '0',
  num_2 int(11) default '-1',
  num_3 int(11) default '0',
  num_4 int(11) default '0',
  num_5 int(11) default '0',
  date_1 int(11) default '0',
  date_2 int(11) default '0',
  date_3 int(11) default '0',
  date_4 int(11) default '0',
  date_5 int(11) default '0',
  PRIMARY KEY  (itemtype,id,cid,language)
) TYPE=MyISAM;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}item_1 ADD workflow VARCHAR( 100 ) NOT NULL AFTER modifiedby;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}item_4 ADD workflow VARCHAR( 100 ) NOT NULL AFTER modifiedby;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}item_5 ADD workflow VARCHAR( 100 ) NOT NULL AFTER modifiedby;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}item_history ADD workflow VARCHAR( 100 ) NOT NULL AFTER modifiedby;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

CREATE TABLE {DB_PREFIX}future_user_right (
  itemtype int(11) NOT NULL default '0',
  cid int(11) NOT NULL default '0',
  itemid int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  value int(11) NOT NULL default '0',
  PRIMARY KEY  (itemtype,cid,itemid,user_id)
) TYPE=MyISAM;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}gaestebuch CHANGE name name VARCHAR(255) NOT NULL; 

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}gaestebuch CHANGE email email VARCHAR(255) NOT NULL; 

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE {DB_PREFIX}gaestebuch CHANGE homepage homepage VARCHAR(255) NOT NULL;
