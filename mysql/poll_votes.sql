DROP TABLE IF EXISTS poll_votes;
CREATE TABLE poll_votes (
		  uid MEDIUMINT(8) UNSIGNED DEFAULT 0,
		  /* This may be either a valid aid or a qid followed by 000 (for free-response/essay questions)*/
		  aid BIGINT(14) UNSIGNED DEFAULT 0,
		  answer MEDIUMTEXT DEFAULT ''
);
