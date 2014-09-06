DROP TABLE IF EXISTS dayschedule_custom_summaries;
CREATE TABLE dayschedule_custom_summaries (
  daytype VARCHAR(32) NOT NULL,
  daydesc VARCHAR(128) NOT NULL
);
DROP TABLE IF EXISTS dayschedule_pretty_summaries;
CREATE TABLE dayschedule_pretty_summaries (
  daytype VARCHAR(32) NOT NULL,
  daydesc VARCHAR(128) NOT NULL
);
DROP TABLE IF EXISTS dayschedule_custom_schedules;
CREATE TABLE dayschedule_custom_schedules (
  daytype VARCHAR(32) NOT NULL,
  json VARCHAR(2048) NOT NULL
);
DROP TABLE IF EXISTS dayschedule_override_schedules;
CREATE TABLE dayschedule_override_schedules (
  dayname VARCHAR(8) NOT NULL,
  daytype VARCHAR(32) NOT NULL
);
