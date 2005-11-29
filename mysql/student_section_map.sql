DROP TABLE IF EXISTS student_section_map;
CREATE TABLE student_section_map (
	studentid unsigned MEDIUMINT NOT NULL,
	PRIMARY KEY(studentid),
	sectionid unsigned INT NOT NULL,
	KEY(sectionid)
);
