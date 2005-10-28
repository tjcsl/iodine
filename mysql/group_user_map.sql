/*
** Shows which users are members of which groups.
*/
DROP TABLE IF EXISTS group_user_map;
CREATE TABLE group_user_map(
	uid MEDIUMINT UNSIGNED NOT NULL,
	gid MEDIUMINT UNSIGNED NOT NULL,
	is_admin BIT,
	can_post BIT,
);
