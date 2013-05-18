DROP TABLE IF EXISTS filecenter_folders_groups;
CREATE TABLE filecenter_folders_groups (
	gid MEDIUMINT UNSIGNED NOT NULL,
	path VARCHAR(255) NOT NULL,
	name VARCHAR(255) NOT NULL
);
