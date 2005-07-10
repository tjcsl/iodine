CREATE TABLE news(
	id INT UNSIGNED NOT NULL DEFAULT '0' AUTO_INCREMENT UNIQUE, /*Unique autoid*/
	PRIMARY KEY(id),				
	title VARCHAR(256) NOT NULL,			/*Story title*/
	text TEXT NOT NULL,			/*Story text*/
	author varchar(128) NOT NULL,		/* author name */
	authorID MEDIUMINT UNSIGNED,			/*Student/teacher ID*/
	authortype ENUM('student','teacher','other') DEFAULT 'student',	/*Poster type*/
	revised TIMESTAMP NOT NULL DEFAULT 'CURRENT_TIMESTAMP',	/*Date revised*/
	posted TIMESTAMP NOT NULL DEFAULT 'CURRENT_TIMESTAMP',	/*Date posted*/
	KEY(posted)
);
