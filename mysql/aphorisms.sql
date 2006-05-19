DROP TABLE IF EXISTS aphorisms;
CREATE TABLE aphorisms (
		  uid MEDIUMINT(8) NOT NULL,
		  PRIMARY KEY(uid),
		  college VARCHAR(64),
		  collegeplans VARCHAR(255),
		  nationalmeritsemifinalist TINYINT(1),
		  nationalmeritfinalist TINYINT(1),
		  nationalachievement TINYINT(1),
		  hispanicachievement TINYINT(1),
		  honor1 VARCHAR(128),
		  honor2 VARCHAR(128),
		  honor3 VARCHAR(128),
		  aphorism TEXT
);
