DROP TABLE IF EXISTS eighth_activity_permissions;
CREATE TABLE eighth_activity_permissions (
	aid MEDIUMINT NOT NULL,

	userid MEDIUMINT UNSIGNED NOT NULL,

	PRIMARY KEY(aid,userid)
);
