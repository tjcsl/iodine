DROP TABLE IF EXISTS calendar_alerts;
CREATE TABLE calendar_alerts (
	userid MEDIUMINT UNSIGNED NOT NULL,
	filtertype ENUM('tag','group','event') NOT NULL DEFAULT 'event',
	filter VARCHAR(128),
	nightbefore BOOLEAN NOT NULL DEFAULT FALSE,
	hourbefore BOOLEAN NOT NULL DEFAULT FALSE
);
