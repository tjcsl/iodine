DROP TABLE IF EXISTS news;
CREATE TABLE news(
	id BIGINT UNSIGNED NOT NULL DEFAULT NULL AUTO_INCREMENT UNIQUE, /*Unique autoid*/
	PRIMARY KEY(id),				
	title VARCHAR(255) NOT NULL,			/*Story title*/
	text TEXT NOT NULL,			/*Story text*/
	authorID MEDIUMINT UNSIGNED,			/*Student/teacher ID*/
	/*revised TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP,	*//*Date revised*/
	posted TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,	/*Date posted*/
	expire DATETIME NULL DEFAULT NULL,	/* The time the news item expires */
	visible TINYINT(1) UNSIGNED NOT NULL DEFAULT 1, /* visibility of the story */
	gid MEDIUMINT,
   KEY(posted)
);
