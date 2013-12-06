/*
** Contains "special occasions" and associated special backgrounds for login
*/
DROP TABLE IF EXISTS special_backgrounds;
CREATE TABLE special_backgrounds(
	startdt VARCHAR(255) NOT NULL,
	enddt VARCHAR(255) NOT NULL,
	occasion VARCHAR(255) NOT NULL,
	background VARCHAR(255) NOT NULL,
	js VARCHAR(255),
	priority INTEGER(4)
)
