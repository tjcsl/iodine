/*
** Controls which groups may access an intrabox (or its corresponding pane).
** If an ibox has any entry here, then only members of mapped groups are OK.
*/
CREATE TABLE intrabox_group_map(
	boxid INT UNSIGNED NOT NULL,
	gid MEDIUMINT UNSIGNED NOT NULL
);
