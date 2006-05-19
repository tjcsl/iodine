DROP TABLE IF EXISTS news_group_map;
CREATE TABLE news_group_map(
	nid BIGINT UNSIGNED NOT NULL, /*News post id*/
	gid VARCHAR(255) NOT NULL /*Group id*/
);
