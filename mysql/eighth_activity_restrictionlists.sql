DROP TABLE IF EXISTS eighth_activity_restrictionlists;
CREATE TABLE eighth_activity_restrictionlists (
	gid MEDIUMINT NOT NULL,

	bid MEDIUMINT UNSIGNED NOT NULL,

	aidlist MEDIUMTEXT,

	PRIMARY KEY(bid,gid)
);
