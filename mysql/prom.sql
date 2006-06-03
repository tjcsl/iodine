DROP TABLE IF EXISTS prom;
CREATE TABLE prom (
	uid MEDIUMINT(10) NOT NULL,
	PRIMARY KEY(uid),
	dateschool VARCHAR(255),
	attending TINYINT(1) NOT NULL,
	teacher VARCHAR(255),
	room VARCHAR(128),
	datename VARCHAR(255)
);
