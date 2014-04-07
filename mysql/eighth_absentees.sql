DROP TABLE IF EXISTS eighth_absentees;
CREATE TABLE eighth_absentees (
	bid MEDIUMINT UNSIGNED NOT NULL,

	userid MEDIUMINT UNSIGNED NOT NULL,

	PRIMARY KEY(bid,userid)
);

CREATE TABLE IF NOT EXISTS `eighth_absences_cache` (
  `userid` int(11) DEFAULT NULL,
  `absences` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
