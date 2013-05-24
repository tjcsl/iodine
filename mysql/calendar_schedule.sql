DROP TABLE IF EXISTS calendar_schedule;
CREATE TABLE calendar_schedule (
	day DATE UNIQUE NOT NULL,
	PRIMARY KEY(day),
	dayname VARCHAR(128),
	blocksarray VARCHAR(256) NOT NULL DEFAULT ''
);
