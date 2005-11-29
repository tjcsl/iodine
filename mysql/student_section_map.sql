DROP TABLE IF EXISTS student_section_map;
CREATE TABLE student_section_map (
	studentid MEDIUMINT UNSIGNED NOT NULL,
	PRIMARY KEY(studentid),
	sectionid INT UNSIGNED NOT NULL,
	KEY(sectionid)
);
