/*
** Defines permissions and maps them to names.
*/
DROP TABLE IF EXISTS permissions;
CREATE TABLE permissions(
	pid MEDIUMINT UNSIGNED UNIQUE NOT NULL DEFAULT NULL AUTO_INCREMENT,
	PRIMARY KEY(pid),
	name VARCHAR(255) NOT NULL DEFAULT '',
	description VARCHAR(255) NOT NULL DEFAULT ''
);
