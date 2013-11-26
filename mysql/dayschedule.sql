CREATE TABLE IF NOT EXISTS `dayschedule_custom_summaries` (
  `daytype` varchar(32) NOT NULL,
  `daydesc` varchar(128) NOT NULL
)
CREATE TABLE IF NOT EXISTS `dayschedule_pretty_summaries` (
  `daytype` varchar(32) NOT NULL,
  `daydesc` varchar(128) NOT NULL
)
CREATE TABLE IF NOT EXISTS `dayschedule_custom_schedules` (
  `daytype` varchar(32) NOT NULL,
  `json` varchar(2048) NOT NULL
)
CREATE TABLE IF NOT EXISTS `dayschedule_override_schedules` (
  `dayname` varchar(8) NOT NULL,
  `daytype` varchar(32) NOT NULL
)
