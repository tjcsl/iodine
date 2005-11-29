DROP TABLE IF EXISTS section_course_map;
CREATE TABLE section_course_map (
	sectionid INT UNSIGNED,
	PRIMARY KEY(sectionid),
	courseid MEDIUMINT UNSIGNED,
	teacherid MEDIUMINT UNSIGNED,
	period TINYINT UNSIGNED,
	term TINYINT,
	room VARCHAR(32)
);
