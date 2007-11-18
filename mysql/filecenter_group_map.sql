DROP TABLE IF EXISTS filecenter_group_map;
CREATE TABLE filecenter_group_map (
	gid MEDIUMINT UNSIGNED NOT NULL,
	fsid MEDIUMINT UNSIGNED NOT NULL,
	KEY(gid),
	KEY(fsid)
);
