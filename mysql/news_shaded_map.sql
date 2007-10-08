/*
** Listing of all news items that are collapsed for each user.
*/
DROP TABLE IF EXISTS news_shaded_map;
CREATE TABLE news_shaded_map(
	uid MEDIUMINT NOT NULL,
	nid BIGINT NOT NULL
)
