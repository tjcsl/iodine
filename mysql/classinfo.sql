CREATE TABLE classes(
	cid BIGINT UNIQUE NOT NULL,
	PRIMARY KEY(cid),
	teachers TEXT NOT NULL, /* Colon-delimited set of teacherIDs  */
	period TINYINT(2) DEFAULT 0,
	length TINYINT(2) DEFAULT 0,
	time TINYINT(2) DEFAULT 0,
	descriptionid BIGINT DEFAULT 0
);
