/*
** Defines groups and maps them to names.
*/
CREATE TABLE groups(
	gid MEDIUMINT UNSIGNED UNIQUE NOT NULL DEFAULT '0' AUTO_INCREMENT,
	PRIMARY KEY(gid),
	name VARCHAR(128)
);
