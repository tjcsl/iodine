/*
** Keeps track of parking spot applicants.
*/
DROP TABLE IF EXISTS parking_apps;
CREATE TABLE parking_apps(
	uid MEDIUMINT UNSIGNED,
	name VARCHAR(30),
	special_name VARCHAR(30),
	email VARCHAR(100),
	mentorship TINYINT(1),
	other_driver VARCHAR(30),
	other_driver_skips MEDIUMINT NOT NULL,
	assigned INT(11),
	skips MEDIUMINT, 
	grade INT,
	timestamp DATETIME
);
