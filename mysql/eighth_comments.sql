/*
Contains all the hottest gossip the 8th-period office has on a student.
*/
DROP TABLE IF EXISTS eighth_comments;
CREATE TABLE eighth_comments (
	uid MEDIUMINT UNSIGNED NOT NULL PRIMARY KEY,
	comments TEXT NOT NULL DEFAULT ''
);
