DROP TABLE IF EXISTS poll_questions;
CREATE TABLE poll_questions (
		  pid MEDIUMINT(8) UNSIGNED NOT NULL,
		  qid TINYINT(4) UNSIGNED NOT NULL,
		  PRIMARY KEY(pid, qid),
		  maxvotes INT(10) UNSIGNED NOT NULL DEFAULT 0,
		  answertype ENUM('standard','approval','split_approval', 'free_response') NOT NULL DEFAULT 'standard',
		  question TEXT NOT NULL DEFAULT ''
);
