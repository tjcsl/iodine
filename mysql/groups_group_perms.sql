/*
** Maps UIDs and GIDs to a permission
*/
DROP TABLE IF EXISTS groups_group_perms;
CREATE TABLE groups_group_perms(
	usergroup MEDIUMINT UNSIGNED NOT NULL,
	KEY(usergroup),
	gid MEDIUMINT UNSIGNED NOT NULL,
	KEY(gid),
	pid MEDIUMINT UNSIGNED NOT NULL,
	KEY(pid)
);
