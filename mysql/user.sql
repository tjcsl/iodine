/* This table contains critical information about a user, typically system
information, which never needs to be hidden or protected, or casually edited */
DROP TABLE IF EXISTS user;
CREATE TABLE user (
	/* Iodine uid */
	uid MEDIUMINT UNSIGNED NOT NULL DEFAULT NULL AUTO_INCREMENT UNIQUE,
	PRIMARY KEY(uid),

	id MEDIUMINT UNSIGNED NOT NULL,
	username VARCHAR(15) NOT NULL,
	INDEX(username),

	sex ENUM('M','F') DEFAULT NULL,
	
	grade ENUM('9', '10', '11', '12', 'staff') DEFAULT NULL,

	fname VARCHAR(63) NOT NULL,
	mname VARCHAR(63) DEFAULT "",
	lname VARCHAR(127) NOT NULL,
	suffix VARCHAR(15) DEFAULT "",
	nickname VARCHAR(63) DEFAULT "",
	/* like the ()'ed ones now in Intranet */

	style VARCHAR(255) DEFAULT "default" NOT NULL, /* User style */

	header BOOLEAN DEFAULT TRUE NOT NULL, /* Whether to display a full titlebar for the user */

	chrome BOOLEAN DEFAULT TRUE NOT NULL /* Whether the user wants any embellishments at all */
);
