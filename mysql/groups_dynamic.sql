/*
** Keeps track of user membership requirements for LDAP, MySQL, or PHP based dynamic groups.
*/
DROP TABLE IF EXISTS groups_dynamic;
CREATE TABLE groups_dynamic (
	gid MEDIUMINT UNSIGNED NOT NULL,
	/*
	** LDAP is for LDAP queries
	** MYSQL is for MYSQL queries
	** PHP is for PHP code
	*/
	dbtype ENUM('LDAP','MYSQL','PHP') NOT NULL,
	query TEXT NOT NULL,
	KEY(gid)
);
