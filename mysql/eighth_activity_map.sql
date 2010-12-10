DROP TABLE IF EXISTS eighth_activity_map;
CREATE TABLE eighth_activity_map (
	aid MEDIUMINT NOT NULL,

	bid MEDIUMINT UNSIGNED NOT NULL,

	userid MEDIUMINT UNSIGNED NOT NULL,

	PRIMARY KEY(bid,userid)
);
