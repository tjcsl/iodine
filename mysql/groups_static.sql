/*
** Keeps track of user membership for static groups.
*/
DROP TABLE IF EXISTS groups_static;
CREATE TABLE groups_static (
	uid MEDIUMINT UNSIGNED NOT NULL,
	gid MEDIUMINT UNSIGNED NOT NULL,
	KEY(uid),
	KEY(gid)
);
