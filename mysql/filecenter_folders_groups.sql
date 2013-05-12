DROP TABLE IF EXISTS filecenter_folders_groups;
CREATE TABLE filecenter_folders_groups (
	gid MEDIUMINT UNSIGNED NOT NULL,
	path VARCHAR(255) NOT NULL,
	name VARCHAR(255) NOT NULL
);


INSERT INTO filecenter_folders_groups VALUES (13,"{{I2_ROOT}}filecenter/common/","View/Upload your Common (R:) files");
INSERT INTO filecenter_folders_groups VALUES (13,"{{I2_ROOT}}filecenter/local/","View/Upload your Windows (M:) files");
INSERT INTO filecenter_folders_groups VALUES (13,"{{I2_ROOT}}filecenter/portfolio/{{grad_year}}/{{i2_username}}","View your portfolio");
INSERT INTO filecenter_folders_groups VALUES (13,"https://shares.tjhsst.edu/portfolio/{{grad_year}}/{{i2_username}}","View your portfolio (Shares)");
INSERT INTO filecenter_folders_groups VALUES (1,"https://shares.tjhsst.edu/","View all Windows files (Shares)");
INSERT INTO filecenter_folders_groups VALUES (1,"{{I2_ROOT}}filecenter/main/{{studentorstaff}}/{{i2_username}}/","Access your UNIX files");
INSERT INTO filecenter_folders_groups VALUES (1,"{{I2_ROOT}}filecenter/csl/user/{{csl_username}}/","Access your old Systems Lab files");
INSERT INTO filecenter_folders_groups VALUES (8,"https://shares.tjhsst.edu/Staff","Staff Intranet");
INSERT INTO filecenter_folders_groups VALUES (2,"{{I2_ROOT}}filecenter/main/","AFS Root");
INSERT INTO filecenter_folders_groups VALUES (13,"https://shares.tjhsst.edu/{{tj01path}}","View your Windows (M:) files (Shares)");
INSERT INTO filecenter_folders_groups VALUES (13,"https://shares.tjhsst.edu/upload2.php","Upload to Windows files (Shares)");
