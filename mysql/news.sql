CREATE TABLE news (
	id bigint NOT NULL DEFAULT '0' AUTO_INCREMENT UNIQUE,	/*Unique autoid*/
	PRIMARY KEY(id),				
	text BIGTEXT NOT NULL,				/*Story text*/
	authorID bigint NOT NULL,			/*Student/teacher ID*/
	authortype ENUM('0','1','2') DEFAULT '0',	/*0 for student, 1 for teacher, 2 for other*/
	title VARCHAR(255) NOT NULL,			/*Story brief title*/
	posted TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,	/*Date posted*/
	KEY(posted),					
	revised TIMESTAMP ON UPDATE CURRENT_TIMESTAMP	/*Date revised*/
);
