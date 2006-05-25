/*
** Keeps track of parking spot applicants' cars.
*/
DROP TABLE IF EXISTS parking_cars;
CREATE TABLE parking_cars(
	uid MEDIUMINT UNSIGNED NOT NULL,
	plate VARCHAR(10),
	make VARCHAR(20),
	model VARCHAR(20),
	color VARCHAR(10),
	year SMALLINT(4) UNSIGNED
);
