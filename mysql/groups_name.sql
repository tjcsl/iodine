/*
** Defines groups and maps them to names and descriptions.
*/
DROP TABLE IF EXISTS groups_name;
CREATE TABLE groups_name(
	gid MEDIUMINT UNSIGNED UNIQUE NOT NULL DEFAULT NULL AUTO_INCREMENT,
	PRIMARY KEY(gid),
	name VARCHAR(255) NOT NULL DEFAULT '',
	description TEXT NOT NULL DEFAULT ''
);
