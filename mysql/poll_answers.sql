DROP TABLE IF EXISTS poll_answers;
CREATE TABLE poll_answers (
		  /* aid is pid + last three digits of qid + three digits */
		  aid BIGINT(14) UNSIGNED NOT NULL DEFAULT NULL AUTO_INCREMENT,
		  PRIMARY KEY(aid),
		  answer MEDIUMTEXT NOT NULL DEFAULT ''
);
