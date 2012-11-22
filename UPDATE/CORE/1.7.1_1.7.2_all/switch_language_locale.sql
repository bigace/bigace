ALTER TABLE item_1 ADD temp_id INT( 11 ) NOT NULL ;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_1 set temp_id = langid;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
ALTER TABLE item_1 CHANGE langid language VARCHAR( 20 ) NOT NULL;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_1 set language = 'de' where temp_id = 1;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_1 set language = 'en' where temp_id = 2;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_1 set language = 'fr' where temp_id = 3;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_1 set language = 'ru' where temp_id = 4;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
ALTER TABLE item_1 DROP temp_id;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE item_4 ADD temp_id INT( 11 ) NOT NULL ;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_4 set temp_id = langid;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
ALTER TABLE item_4 CHANGE langid language VARCHAR( 20 ) NOT NULL;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_4 set language = 'de' where temp_id = 1;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_4 set language = 'en' where temp_id = 2;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_4 set language = 'fr' where temp_id = 3;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_4 set language = 'ru' where temp_id = 4;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
ALTER TABLE item_4 DROP temp_id;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE item_5 ADD temp_id INT( 11 ) NOT NULL ;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_5 set temp_id = langid;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
ALTER TABLE item_5 CHANGE langid language VARCHAR( 20 ) NOT NULL;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_5 set language = 'de' where temp_id = 1;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_5 set language = 'en' where temp_id = 2;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_5 set language = 'fr' where temp_id = 3;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_5 set language = 'ru' where temp_id = 4;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
ALTER TABLE item_5 DROP temp_id;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE item_history ADD temp_id INT( 11 ) NOT NULL ;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_history set temp_id = langid;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
ALTER TABLE item_history CHANGE langid language VARCHAR( 20 ) NOT NULL;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_history set language = 'de' where temp_id = 1;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_history set language = 'en' where temp_id = 2;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_history set language = 'fr' where temp_id = 3;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_history set language = 'ru' where temp_id = 4;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
ALTER TABLE item_history DROP temp_id;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE item_project_text ADD temp_id INT( 11 ) NOT NULL ;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_project_text set temp_id = langid;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
ALTER TABLE item_project_text CHANGE langid language VARCHAR( 20 ) NOT NULL;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_project_text set language = 'de' where temp_id = 1;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_project_text set language = 'en' where temp_id = 2;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_project_text set language = 'fr' where temp_id = 3;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_project_text set language = 'ru' where temp_id = 4;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
ALTER TABLE item_project_text DROP temp_id;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE item_project_num ADD temp_id INT( 11 ) NOT NULL ;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_project_num set temp_id = langid;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
ALTER TABLE item_project_num CHANGE langid language VARCHAR( 20 ) NOT NULL;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_project_num set language = 'de' where temp_id = 1;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_project_num set language = 'en' where temp_id = 2;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_project_num set language = 'fr' where temp_id = 3;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update item_project_num set language = 'ru' where temp_id = 4;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
ALTER TABLE item_project_num DROP temp_id;

# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #

ALTER TABLE user ADD temp_id INT( 11 ) NOT NULL ;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update user set temp_id = language;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
ALTER TABLE user CHANGE language language VARCHAR( 20 ) NOT NULL;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update user set language = 'de' where temp_id = 1;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update user set language = 'en' where temp_id = 2;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update user set language = 'fr' where temp_id = 3;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
update user set language = 'ru' where temp_id = 4;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
ALTER TABLE user DROP temp_id;
# {-S-T-A-T-E-M-E-N-T--S-P-L-I-T-T-E-R-} #
DROP TABLE language;
