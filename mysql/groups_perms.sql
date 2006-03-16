/*
** Maps UIDs and GIDs to a permission
*/
DROP TABLE IF EXISTS groups_perms;
CREATE TABLE groups_perms(
	uid MEDIUMINT UNSIGNED NOT NULL,
	KEY(uid),
	gid MEDIUMINT UNSIGNED NOT NULL,
	KEY(gid),
	permission VARCHAR(64) NOT NULL DEFAULT ''
);
