CREATE TABLE news_stories (
	id bigint NOT NULL DEFAULT '0' AUTO_INCREMENT UNIQUE,	/*Unique autoid*/
	PRIMARY KEY(id),				
	text TEXT NOT NULL,				/*Story text*/
	author varchar(128) NOT NULL,		/* author name */
	authorID bigint,			/*Student/teacher ID*/
	authortype ENUM('student','teacher','other') DEFAULT 'student',	/*Poster type*/
	title VARCHAR(255) NOT NULL,			/*Story brief title*/
	revised TIMESTAMP NOT NULL DEFAULT 'CURRENT_TIMESTAMP',	/*Date revised*/
	posted TIMESTAMP NOT NULL DEFAULT 'CURRENT_TIMESTAMP',	/*Date posted*/
	KEY(posted)
);
