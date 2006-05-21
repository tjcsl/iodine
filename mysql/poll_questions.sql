DROP TABLE IF EXISTS poll_questions;
CREATE TABLE poll_questions (
		  /* qid is pollid + 3 digits (NOT pid+000, which is special) */
		  qid INT(11) UNSIGNED NOT NULL,
		  PRIMARY KEY(qid),
		  maxvotes INT(10) UNSIGNED NOT NULL DEFAULT 0,
		  answertype ENUM('standard','approval','freeresponse','essay') NOT NULL DEFAULT 'standard',
		  answerlimit MEDIUMINT(8) UNSIGNED DEFAULT 0,
		  question TEXT NOT NULL DEFAULT ''
);
