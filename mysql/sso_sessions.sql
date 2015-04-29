CREATE TABLE IF NOT EXISTS `sso_sessions` (
  `acckey` varchar(128) NOT NULL,
  `token` varchar(512) NOT NULL,
  `expire` int(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
