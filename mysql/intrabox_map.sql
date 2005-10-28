DROP TABLE IF EXISTS intrabox_map;
CREATE TABLE intrabox_map (
	/* uid of user */
	uid INT UNSIGNED NOT NULL,
	INDEX(uid),
	
	boxid INT NOT NULL,

	box_order TINYINT
);
