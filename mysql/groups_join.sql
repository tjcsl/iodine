/*
** Keeps track of user membership requirements for group-based dynamic groups.
*/
DROP TABLE IF EXISTS groups_join;
CREATE TABLE groups_join (
	gid MEDIUMINT UNSIGNED NOT NULL,
	optype ENUM('AND','AND NOT','OR','OR NOT') NOT NULL,
	group1 MEDIUMINT UNSIGNED NOT NULL,
	group2 MEDIUMINT UNSIGNED NOT NULL,
	KEY(gid)
);
