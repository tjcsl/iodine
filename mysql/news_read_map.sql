/*
** Maps read stories to users.
*/
DROP TABLE IF EXISTS news_read_map;
CREATE TABLE news_read_map(
	uid MEDIUMINT NOT NULL,
	nid BIGINT NOT NULL
)
