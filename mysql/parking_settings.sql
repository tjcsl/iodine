/*
** Keeps track of parking settings.
*/
DROP TABLE IF EXISTS parking_settings;
CREATE TABLE parking_settings(
	sort1 VARCHAR(20),
	sort2 VARCHAR(20),
	sort3 VARCHAR(20),
	sort4 VARCHAR(20),
	sort5 VARCHAR(20),
	startdate DATETIME,
	deadline DATETIME
);
