DROP TABLE IF EXISTS eighth_block_map;
CREATE TABLE eighth_block_map (
	bid MEDIUMINT UNSIGNED NOT NULL,

	activityid MEDIUMINT UNSIGNED NOT NULL,

	sponsors VARCHAR(127) NOT NULL,

	rooms VARCHAR(127) NOT NULL,

	attendancetaken TINYINT(1) NOT NULL DEFAULT '0',

	cancelled TINYINT(1) NOT NULL DEFAULT '0',

	roomchanged TINYINT(1) NOT NULL DEFAULT '0',

	comment VARCHAR(255) NOT NULL,

	advertisement TEXT NOT NULL,

	capacity INTEGER NOT NULL DEFAULT '-1',

	PRIMARY KEY(bid,activityid)
);
