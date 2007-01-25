DROP TABLE IF EXISTS senior_destinations;
CREATE TABLE senior_destinations (
	uid MEDIUMINT(8) NOT NULL,
	PRIMARY KEY(UID),
	name VARCHAR(64),
	ceeb MEDIUMINT(8),
	college_certain TINYINT(1),
	major TINYINT(4),
	major_certain TINYINT(1)
)
