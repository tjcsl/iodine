CREATE TABLE intrabox (
	boxid INT UNSIGNED NOT NULL DEFAULT '0' AUTO_INCREMENT UNIQUE, /*Unique autoid*/
	PRIMARY KEY(boxid),	
	
	name VARCHAR(63) NOT NULL
	/* Put some ACL/permission stuff down here */
);
