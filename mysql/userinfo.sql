CREATE TABLE users(
	uid BIGINT NOT NULL UNIQUE,
	PRIMARY KEY(uid),
	
	fname VARCHAR(64) DEFAULT "",
	mname VARCHAR(64) DEFAULT "",
	lname VARCHAR(128) DEFAULT "",
	
	bdate DATE DEFAULT '0',
	
	phone_home VARCHAR(20) DEFAULT "",
	phone_cell VARCHAR(20) DEFAULT "",
	phone_other VARCHAR(20) DEFAULT "",
	
	/*	Address information	 */

	address_primary_city VARCHAR(64) DEFAULT "",
	address_primary_state VARCHAR(2) DEFAULT "",
	address_primary_zip VARCHAR(12) DEFAULT "",
	address_primary_street VARCHAR(255) DEFAULT "",
	
	address_secondary_city VARCHAR(64) DEFAULT "",
	address_secondary_state VARCHAR(2) DEFAULT "",
	address_secondary_zip VARCHAR(12) DEFAULT "",
	address_secondary_street VARCHAR(255) DEFAULT "",
	
	address_tertiary_city VARCHAR(64) DEFAULT "",
	address_tertiary_state VARCHAR(2) DEFAULT "",
	address_tertiary_zip VARCHAR(12) DEFAULT "",
	address_tertiary_street VARCHAR(255) DEFAULT "",

	/* Screen names and email */

	sn0 VARCHAR(128) DEFAULT "",
	sn1 VARCHAR(128) DEFAULT "",
	sn2 VARCHAR(128) DEFAULT "",
	sn3 VARCHAR(128) DEFAULT "",
	sn4 VARCHAR(128) DEFAULT "",
	sn5 VARCHAR(128) DEFAULT "",
	sn6 VARCHAR(128) DEFAULT "",
	sn7 VARCHAR(128) DEFAULT "",

	email0 VARCHAR(128) DEFAULT "",
	email1 VARCHAR(128) DEFAULT "",
	email2 VARCHAR(128) DEFAULT "",
	email3 VARCHAR(128) DEFAULT "",

	/* Other assorted stuff */

	username VARCHAR(128) DEFAULT "", /* LAN username, etc. */

	sex ENUM('M','F','N') DEFAULT 'N',
	
	grade TINYINT(2) DEFAULT 0,
	
	webpage VARCHAR(255) DEFAULT "",
	
	locker BIGINT DEFAULT 0,
	
	counselor VARCHAR(64) DEFAULT "",
	
	startpage VARCHAR(128) DEFAULT "news", /* Default I2 module */
	
	picture0 BIGINT DEFAULT 0,
	picture1 BIGINT DEFAULT 1,
	picture2 BIGINT DEFAULT 2,
	picture3 BIGINT DEFAULT 3

);
