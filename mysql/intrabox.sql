CREATE TABLE intrabox (
	boxid INT UNSIGNED NOT NULL DEFAULT NULL AUTO_INCREMENT UNIQUE, /*Unique autoid*/
	PRIMARY KEY(boxid),	
	
	name VARCHAR(63) NOT NULL,

	display_name VARCHAR(127)
	/* Put some ACL/permission stuff down here */
);
