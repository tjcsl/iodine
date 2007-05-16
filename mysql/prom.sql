DROP TABLE IF EXISTS prom;
CREATE TABLE prom (
	uid MEDIUMINT(10) UNSIGNED NOT NULL,
	PRIMARY KEY(uid),
	going TINYINT(1) NOT NULL,
	datefrom ENUM('TJ','FCPS','other'),
	datename VARCHAR(255),
	dategrade SMALLINT(6),
	dateother VARCHAR(128)
);
