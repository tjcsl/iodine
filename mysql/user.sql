/* This table contains critical information about a user, typically system
information, which never needs to be hidden or protected, or casually edited */
CREATE TABLE user (
	/* Iodine uid */
	uid MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AUTO_INCREMENT UNIQUE,
	PRIMARY KEY(uid),

	username VARCHAR(128) NOT NULL,

	fname VARCHAR(64) NOT NULL,
	mname VARCHAR(64),
	lname VARCHAR(128) NOT NULL,
	suffix VARCHAR(16),
	nickname VARCHAR(64),
	/* like the ()'ed ones now in Intranet */

	/* This field _is_ edited, but it seems like it will be accessed so
	much, it may make sense to put it in this table, as this is a smaller
	table than userinfo */
	startpage VARCHAR(128) DEFAULT "news" NOT NULL /* Default I2 module */
);
