CREATE TABLE users(
	uid SMALLINT UNSIGNED NOT NULL UNIQUE,
	PRIMARY KEY(uid),
	
	username VARCHAR(128) DEFAULT "", /* LAN username, etc. */

	fname VARCHAR(64) DEFAULT "",
	mname VARCHAR(64) DEFAULT "",
	lname VARCHAR(128) DEFAULT "",
	suffix VARCHAR(16) DEFAULT "",
	nickname VARCHAR(64) DEFAULT "",
	/* like the ()'ed ones now in Intranet */
	
	startpage VARCHAR(128) DEFAULT "news", /* Default I2 module */

	bdate DATE DEFAULT NULL,
	
	phone_home VARCHAR(20) DEFAULT "",
	phone_cell VARCHAR(20) DEFAULT "",
	phone_other VARCHAR(20) DEFAULT "",
	
	/*	Address information	 */

	address1_city VARCHAR(64) DEFAULT "",
	address1_state VARCHAR(2) DEFAULT "",
	address1_zip VARCHAR(12) DEFAULT "",
	address1_street VARCHAR(255) DEFAULT "",
	
	address2_city VARCHAR(64) DEFAULT "",
	address2_state VARCHAR(2) DEFAULT "",
	address2_zip VARCHAR(12) DEFAULT "",
	address2_street VARCHAR(255) DEFAULT "",
	
	address3_city VARCHAR(64) DEFAULT "",
	address3_state VARCHAR(2) DEFAULT "",
	address3_zip VARCHAR(12) DEFAULT "",
	address3_street VARCHAR(255) DEFAULT "",

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

	sex ENUM('M','F') DEFAULT NULL,
	
	grade ENUM('9', '10', '11', '12', 'staff') DEFAULT NULL,
	
	webpage VARCHAR(256) DEFAULT "",
	
	locker SMALLINT UNSIGNED DEFAULT 0,
	
	counselor VARCHAR(64) DEFAULT "",
	
	picture0 SMALLINT DEFAULT NULL,
	picture1 SMALLINT DEFAULT NULL,
	picture2 SMALLINT DEFAULT NULL,
	picture3 SMALLINT DEFAULT NULL,

	boxes TEXT NOT NULL DEFAULT "",

	groups TEXT DEFAULT NULL

);
