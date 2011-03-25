/*
**  Users can now like stories.
*/
DROP TABLE IF EXISTS news_likes;
CREATE TABLE news_likes(
	uid MEDIUMINT NOT NULL,
	nid BIGINT NOT NULL
)
