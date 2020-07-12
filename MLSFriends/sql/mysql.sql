CREATE TABLE MLSfriends (
   ref int(11) NOT NULL auto_increment,
   uid int(5) NOT NULL default '0',
   fuid int(5) NOT NULL default '0',
   PRIMARY KEY  (ref),
   UNIQUE KEY REF (ref)
   ) TYPE=MyISAM;
