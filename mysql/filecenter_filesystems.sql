DROP TABLE IF EXISTS filecenter_filesystems;
CREATE TABLE filecenter_filesystems (
	name VARCHAR(64) UNIQUE NOT NULL,
	code LONGTEXT NOT NULL
);
