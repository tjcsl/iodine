/* This table contains user information which is either editable or could
possibly be restricted by privacy preferences */
DROP TABLE IF EXISTS userinfo;
CREATE TABLE userinfo (
	/* binds to other user/userinfo tables */
	uid MEDIUMINT UNSIGNED NOT NULL UNIQUE,
	PRIMARY KEY(uid),
	
	bdate DATE DEFAULT NULL,
	
	phone_home VARCHAR(20) DEFAULT NULL,
	phone_cell VARCHAR(20) DEFAULT NULL,
	phone_other VARCHAR(20) DEFAULT NULL,
	
	/*	Address information	 */

	address1_city VARCHAR(63) DEFAULT NULL,
	address1_state VARCHAR(2) DEFAULT NULL,
	address1_zip VARCHAR(12) DEFAULT NULL,
	address1_street VARCHAR(255) DEFAULT NULL,
	
	address2_city VARCHAR(63) DEFAULT NULL,
	address2_state VARCHAR(2) DEFAULT NULL,
	address2_zip VARCHAR(12) DEFAULT NULL,
	address2_street VARCHAR(255) DEFAULT NULL,
	
	address3_city VARCHAR(63) DEFAULT NULL,
	address3_state VARCHAR(2) DEFAULT NULL,
	address3_zip VARCHAR(12) DEFAULT NULL,
	address3_street VARCHAR(255) DEFAULT NULL,

	/* Screen names and email */

	sn0 VARCHAR(127) DEFAULT NULL,
	sn1 VARCHAR(127) DEFAULT NULL,
	sn2 VARCHAR(127) DEFAULT NULL,
	sn3 VARCHAR(127) DEFAULT NULL,
	sn4 VARCHAR(127) DEFAULT NULL,
	sn5 VARCHAR(127) DEFAULT NULL,
	sn6 VARCHAR(127) DEFAULT NULL,
	sn7 VARCHAR(127) DEFAULT NULL,

	email0 VARCHAR(127) DEFAULT NULL,
	email1 VARCHAR(127) DEFAULT NULL,
	email2 VARCHAR(127) DEFAULT NULL,
	email3 VARCHAR(127) DEFAULT NULL,

	/* Other assorted stuff */

	webpage VARCHAR(255) DEFAULT NULL,
	
	locker SMALLINT UNSIGNED DEFAULT NULL,
	
	counselor VARCHAR(63) DEFAULT NULL,
	
	picture0 SMALLINT DEFAULT NULL,
	picture1 SMALLINT DEFAULT NULL,
	picture2 SMALLINT DEFAULT NULL,
	picture3 SMALLINT DEFAULT NULL,

	studentid MEDIUMINT unsigned NOT NULL

);
