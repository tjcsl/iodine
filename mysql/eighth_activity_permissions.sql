DROP TABLE eighth_activity_permissions;
CREATE TABLE eighth_activity_permissions (
	aid MEDIUMINT UNSIGNED NOT NULL,

	userid MEDIUMINT UNSIGNED NOT NULL,

	PRIMARY KEY(aid,userid)
);
