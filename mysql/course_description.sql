DROP TABLE IF EXISTS course_description;
CREATE TABLE course_description (
	courseid MEDIUMINT UNSIGNED NOT NULL,
	PRIMARY KEY(courseid),
	classname VARCHAR(64),
	description BLOB
)
