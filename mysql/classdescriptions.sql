CREATE TABLE classdescriptions(
	did BIGINT UNIQUE NOT NULL,
	PRIMARY KEY(did),
	name VARCHAR(255) DEFAULT "",
	description TEXT DEFAULT ""
);
