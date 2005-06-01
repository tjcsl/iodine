CREATE TABLE classes(
	cid BIGINT UNIQUE NOT NULL,
	PRIMARY KEY(cid),
	teachers TEXT NOT NULL, /* Colon-delimited set of teacherIDs  */
	period ENUM('0','1','2','3','4','5','6','7') DEFAULT 0,
	length ENUM('0','1','2','4') DEFAULT 0,
	time ENUM('0','1','2','3','4') DEFAULT 0,
	year TINYINT(4) DEFAULT 0,
	descriptionid BIGINT DEFAULT 0
);
