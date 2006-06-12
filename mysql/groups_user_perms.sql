/*
** Maps UIDs and GIDs to a permission
*/
DROP TABLE IF EXISTS groups_user_perms;
CREATE TABLE groups_user_perms(
	uid MEDIUMINT UNSIGNED NOT NULL,
	KEY(uid),
	gid MEDIUMINT UNSIGNED NOT NULL,
	KEY(gid),
	pid MEDIUMINT UNSIGNED NOT NULL,
	KEY(pid)
);
