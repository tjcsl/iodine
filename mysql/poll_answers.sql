DROP TABLE IF EXISTS poll_answers;
CREATE TABLE poll_answers (
		  pid MEDIUMINT(8) UNSIGNED DEFAULT 0,
		  qid MEDIUMINT(8) UNSIGNED DEFAULT 0,
		  aid MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT NULL AUTO_INCREMENT,
		  PRIMARY KEY(aid),
		  answer TEXT
);
